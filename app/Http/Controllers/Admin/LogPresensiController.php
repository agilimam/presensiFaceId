<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Presensi; 
use App\Models\anggotaKeluarga; 
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class LogPresensiController extends Controller
{
    public function index(Request $request)
    {
        
        $tanggalPilihan = $request->get('date', Carbon::today()->format('Y-m-d'));
        $sesiSholatPilihan = $request->get('sholat'); 

        $daftarKeluarga = anggotaKeluarga::with('keluarga')
            ->where('hubungan', 'Kepala Keluarga')
            ->when($request->search, function ($query) use ($request) {
                return $query->where('nama_anggota', 'like', '%' . $request->search . '%')
                             ->orWhereHas('keluarga', function($q) use ($request){
                                 $q->where('nama_keluarga', 'like', '%' . $request->search . '%');
                             });
            })
            ->get();

        // FIX #1: Filter berdasarkan sesi sholat yang dipilih (kalau ada)
        $logsRaw = Presensi::with('anggotaKeluarga')
            ->whereDate('waktu_absen', $tanggalPilihan)
            ->when($sesiSholatPilihan, function ($query) use ($sesiSholatPilihan) {
                return $query->where('keterangan_sholat', $sesiSholatPilihan);
            })
            ->get();

        // Daftar sesi yang perlu dihitung: kalau ada filter, cuma sesi itu saja
        $daftarSesi = $sesiSholatPilihan
            ? [$sesiSholatPilihan]
            : ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

        $rekapKeluarga = [];

        foreach ($daftarKeluarga as $kk) {
            $idKeluarga = $kk->id_keluarga;
            $absenKeluargaIni = $logsRaw->where('id_keluarga', $idKeluarga);

            $rekapKeluarga[$idKeluarga] = [
                'nama_keluarga'   => $kk->keluarga->nama_keluarga ?? 'Keluarga Baru',
                'kepala_keluarga' => $kk->nama_anggota,
                'last_absen'      => $absenKeluargaIni->max('waktu_absen'),
                'Subuh'   => null,
                'Dzuhur'  => null,
                'Ashar'   => null,
                'Maghrib' => null,
                'Isya'    => null,
            ];

            foreach ($daftarSesi as $sesi) {
                $absenSesi = $absenKeluargaIni->where('keterangan_sholat', $sesi)->sortBy('waktu_absen');
                if ($absenSesi->isNotEmpty()) {
                    $logPertama = $absenSesi->first();

                    // FIX #2: Dedup jamaah yang hadir per id_anggota_keluarga
                    // supaya 1 orang yang absen 2x di sesi yang sama tidak terhitung dobel
                    $semuaYangHadir = [];
                    $idAnggotaSudahDicatat = [];

                    foreach ($absenSesi as $log) {
                        if ($log->anggotaKeluarga) {
                            $idAnggota = $log->anggotaKeluarga->id_anggota_keluarga;

                            if (in_array($idAnggota, $idAnggotaSudahDicatat)) {
                                continue; // sudah dicatat sebelumnya, skip
                            }

                            $semuaYangHadir[] = [
                                'nama' => $log->anggotaKeluarga->nama_anggota,
                                'jam'  => Carbon::parse($log->waktu_absen)->format('H:i'),
                                'status' => $log->status
                            ];

                            $idAnggotaSudahDicatat[] = $idAnggota;
                        }
                    }

                    $rekapKeluarga[$idKeluarga][$sesi] = [
                        'status_utama' => $logPertama->status, 
                        'jam_pertama'  => Carbon::parse($logPertama->waktu_absen)->format('H:i'),
                        'jamaah_hadir' => $semuaYangHadir 
                    ];
                }
            }
        }

        uasort($rekapKeluarga, function ($a, $b) {
            return strtotime($b['last_absen'] ?? '1970-01-01') 
                 <=> strtotime($a['last_absen'] ?? '1970-01-01');
        });

        // 2. Sekarang buat Paginator di luar fungsi uasort
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 9; 
        
        // Ambil potongan data untuk halaman saat ini
        $currentPageItems = array_slice($rekapKeluarga, ($currentPage - 1) * $perPage, $perPage);

        // Buat objek Paginator
        $rekapKeluargaPaginator = new LengthAwarePaginator(
            $currentPageItems, 
            count($rekapKeluarga), 
            $perPage, 
            $currentPage, 
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // 3. Kirim ke view
        return view('admin.log_presensi.index', compact('rekapKeluargaPaginator', 'tanggalPilihan', 'sesiSholatPilihan'));
    }

    public function exportPdf(Request $request)
{
    $startDate = $request->get('date_start', Carbon::today()->format('Y-m-d'));
    $endDate = $request->get('date_end', $startDate);

    $start = Carbon::parse($startDate)->startOfDay();
    $end = Carbon::parse($endDate)->endOfDay();
    
    $labelTanggal = ($startDate == $endDate) 
        ? Carbon::parse($startDate)->translatedFormat('d F Y')
        : Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');

    $logsRaw = Presensi::with(['anggotaKeluarga', 'anggotaKeluarga.keluarga'])
        ->whereBetween('waktu_absen', [$start, $end])
        ->orderBy('waktu_absen', 'asc')
        ->get();

    // KELOMPOKKAN DATA BERDASARKAN TANGGAL DULU
    $rekapPerTanggal = $logsRaw->groupBy(function($item) {
        return Carbon::parse($item->waktu_absen)->format('Y-m-d');
    });

    $dompdf = app('dompdf.wrapper');
    $html = view('admin.log_presensi.cetak_pdf', compact('rekapPerTanggal', 'labelTanggal'))->render();
    
    $dompdf->loadHTML($html)->setPaper('a4', 'landscape');
    return $dompdf->stream();
    }
}