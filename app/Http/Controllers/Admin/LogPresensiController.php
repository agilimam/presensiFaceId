<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi;
use App\Models\anggotaKeluarga;
use App\Models\JadwalSholat;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class LogPresensiController extends Controller
{
    public function index(Request $request)
    {
        $tanggalPilihan = $request->get('date', Carbon::today('Asia/Jakarta')->format('Y-m-d'));

        // Urutan sesi mengikuti jam_mulai: Subuh -> Zuhur -> Ashar -> Magrib -> Isya
        $daftarSesi = JadwalSholat::orderBy('jam_mulai')->get();

        // 'sholat' di URL berisi id_jadwal (integer), bukan nama, supaya match dengan kolom id_jadwal di tabel presensi
        $sesiSholatPilihan = $request->get('sholat');
        $sesiTerpilihObj = $sesiSholatPilihan ? $daftarSesi->firstWhere('id_jadwal', $sesiSholatPilihan) : null;
        $sesiSholatNama = $sesiTerpilihObj->nama_sholat ?? null;

        $daftarKeluarga = anggotaKeluarga::with('keluarga')
            ->where('hubungan', 'Kepala Keluarga')
            ->when($request->search, function ($query) use ($request) {
                return $query->where('nama_anggota', 'like', '%' . $request->search . '%')
                             ->orWhereHas('keluarga', function ($q) use ($request) {
                                 $q->where('nama_keluarga', 'like', '%' . $request->search . '%');
                             });
            })
            ->get();

        $logsRaw = Presensi::with('anggotaKeluarga')
            ->whereDate('waktu_absen', $tanggalPilihan)
            ->when($sesiSholatPilihan, function ($query) use ($sesiSholatPilihan) {
                return $query->where('id_jadwal', $sesiSholatPilihan);
            })
            ->get();

        // Sembunyikan presensi Isya "basi" dari hari sebelumnya begitu azan Subuh hari ini masuk
        $sesiSubuh = $daftarSesi->firstWhere('nama_sholat', 'Subuh');
        $sesiIsya  = $daftarSesi->firstWhere('nama_sholat', 'Isya');

        if ($sesiSubuh && $sesiIsya) {
            $hariIniStr   = Carbon::today('Asia/Jakarta')->format('Y-m-d');
            $now          = Carbon::now('Asia/Jakarta');
            $subuhHariIni = Carbon::today('Asia/Jakarta')
                ->setTimeFromTimeString($sesiSubuh->jam_mulai);

            if ($tanggalPilihan === $hariIniStr && $now->gte($subuhHariIni)) {
                $logsRaw = $logsRaw->reject(function ($item) use ($sesiIsya, $subuhHariIni) {
                    return $item->id_jadwal === $sesiIsya->id_jadwal
                        && Carbon::parse($item->waktu_absen)->lt($subuhHariIni);
                });
            }
        }

        $rekapKeluarga = [];

        foreach ($daftarKeluarga as $kk) {
            $idKeluarga = $kk->id_keluarga;
            $absenKeluargaIni = $logsRaw->where('id_keluarga', $idKeluarga);
            $lastAbsen = null;

            foreach ($daftarSesi as $sesi) {
                $absenSesi = $absenKeluargaIni
                    ->where('id_jadwal', $sesi->id_jadwal)
                    ->sortBy('waktu_absen');

                if ($absenSesi->isNotEmpty()) {
                    $logPertama = $absenSesi->first();

                    $semuaYangHadir = [];
                    $idAnggotaSudahDicatat = [];

                    foreach ($absenSesi as $log) {
                        if ($log->anggotaKeluarga) {
                            $idAnggota = $log->anggotaKeluarga->id_anggota_keluarga;

                            if (in_array($idAnggota, $idAnggotaSudahDicatat)) {
                                continue;
                            }

                            $semuaYangHadir[] = [
                                'nama'   => $log->anggotaKeluarga->nama_anggota,
                                'jam'    => Carbon::parse($log->waktu_absen)->format('H:i'),
                                'status' => $log->status,
                            ];

                            $idAnggotaSudahDicatat[] = $idAnggota;
                        }
                    }

                    $rekapKeluarga[$idKeluarga][$sesi->nama_sholat] = [
                        'status_utama' => $logPertama->status,
                        'jam_pertama'  => Carbon::parse($logPertama->waktu_absen)->format('H:i'),
                        'jamaah_hadir' => $semuaYangHadir,
                    ];

                    $waktuLog = Carbon::parse($logPertama->waktu_absen);
                    if (!$lastAbsen || $waktuLog->gt($lastAbsen)) {
                        $lastAbsen = $waktuLog;
                    }
                }
            }

            // Dipakai untuk pengurutan di bawah (keluarga dengan presensi terbaru di atas)
            $rekapKeluarga[$idKeluarga]['last_absen'] = $lastAbsen?->format('Y-m-d H:i:s');
            $rekapKeluarga[$idKeluarga]['nama_keluarga'] = $kk->keluarga->nama_keluarga ?? '-';
            $rekapKeluarga[$idKeluarga]['kepala_keluarga'] = $kk->nama_anggota ?? '-';
        }

        uasort($rekapKeluarga, function ($a, $b) {
            return strtotime($b['last_absen'] ?? '1970-01-01')
                 <=> strtotime($a['last_absen'] ?? '1970-01-01');
        });

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 9;

        // preserve_keys = true, supaya id_keluarga tidak ke-reset jadi index numerik biasa
        $currentPageItems = array_slice($rekapKeluarga, ($currentPage - 1) * $perPage, $perPage, true);

        $rekapKeluargaPaginator = new LengthAwarePaginator(
            $currentPageItems,
            count($rekapKeluarga),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.log_presensi.index', compact('rekapKeluargaPaginator', 'tanggalPilihan', 'sesiSholatPilihan', 'sesiSholatNama', 'daftarSesi'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('date_start', Carbon::today()->format('Y-m-d'));
        $endDate = $request->get('date_end', $startDate);
        $sesiSholatPilihan = $request->get('sholat'); // id_jadwal, opsional

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $labelTanggal = ($startDate == $endDate)
            ? Carbon::parse($startDate)->translatedFormat('d F Y')
            : Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');

        // Kolom sesi di tabel: kalau difilter cuma 1 sesi, kalau tidak tampil semua urut jam_mulai
        $daftarSesi = JadwalSholat::orderBy('jam_mulai')
            ->when($sesiSholatPilihan, function ($query) use ($sesiSholatPilihan) {
                return $query->where('id_jadwal', $sesiSholatPilihan);
            })
            ->get();

        $logsRaw = Presensi::with([
            'anggotaKeluarga',
            'anggotaKeluarga.keluarga.anggotaKeluarga',
        ])
            ->whereBetween('waktu_absen', [$start, $end])
            ->when($sesiSholatPilihan, function ($query) use ($sesiSholatPilihan) {
                return $query->where('id_jadwal', $sesiSholatPilihan);
            })
            ->orderBy('waktu_absen', 'asc')
            ->get();

        $rekapPerTanggal = $logsRaw->groupBy(function ($item) {
            return Carbon::parse($item->waktu_absen)->format('Y-m-d');
        });

        $dompdf = app('dompdf.wrapper');
        $html = view('admin.log_presensi.cetak_pdf', compact('rekapPerTanggal', 'labelTanggal', 'daftarSesi'))->render();

        $dompdf->loadHTML($html)->setPaper('a4', 'landscape');
        return $dompdf->stream();
    }
}