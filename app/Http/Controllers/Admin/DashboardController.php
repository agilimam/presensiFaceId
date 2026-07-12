<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\keluarga;
use App\Models\anggotaKeluarga;
use App\Models\presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalSholat;

class DashboardController extends Controller
{
    public function index()
    {
        $hari_ini = Carbon::today('Asia/Jakarta');
        $now      = Carbon::now('Asia/Jakarta');
        $kemarin  = Carbon::yesterday('Asia/Jakarta');

        $totalKeluarga = keluarga::count();
        $totalAnggota  = anggotaKeluarga::count();
        $daftarSholat  = JadwalSholat::orderBy('jam_mulai')->get();

        $absenHariIniRaw = presensi::whereDate('waktu_absen', $hari_ini)->get();
        $presensiHariIni = $absenHariIniRaw->count();

        $chartData = [];
        foreach ($daftarSholat as $jadwal) {
            $chartData[] = $absenHariIniRaw
                ->where('id_jadwal', $jadwal->id_jadwal)
                ->count();
        }

        $jumlahKeluargaAktif = $absenHariIniRaw->pluck('id_keluarga')->unique()->count();

        $keluargaFullSesiHariIni = $absenHariIniRaw
            ->groupBy('id_keluarga')
            ->filter(function ($logs) {
                return $logs->pluck('id_jadwal')->unique()->count() >= 5;
            })
            ->count();

        // =========================================================
        // JADWAL SHOLAT YANG SEDANG BERLANGSUNG
        // =========================================================
        // Definisi "sedang berlangsung": sesi dengan jam_mulai terakhir
        // yang sudah lewat, sampai sesi berikutnya mulai. Khusus Isya,
        // dianggap masih berlangsung sampai Subuh esok hari (lewat tengah malam).
        $sesiHariIni = $daftarSholat->map(function ($jadwal) use ($hari_ini) {
            return (object) [
                'id_jadwal'   => $jadwal->id_jadwal,
                'nama_sholat' => $jadwal->nama_sholat,
                'jam_mulai'   => $hari_ini->copy()->setTimeFromTimeString($jadwal->jam_mulai),
            ];
        })->sortBy('jam_mulai')->values();

        // Cari sesi terakhir yang jam_mulai-nya sudah lewat hari ini
        $sholatSedangBerlangsung = $sesiHariIni->last(function ($sesi) use ($now) {
            return $sesi->jam_mulai->lte($now);
        });

        // Kalau belum ada (misal masih dini hari sebelum Subuh),
        // yang sedang berlangsung adalah Isya dari malam sebelumnya.
        if (!$sholatSedangBerlangsung) {
            $sholatSedangBerlangsung = (object) [
                'id_jadwal'   => $sesiHariIni->last()->id_jadwal,
                'nama_sholat' => $sesiHariIni->last()->nama_sholat,
                'jam_mulai'   => $sesiHariIni->last()->jam_mulai->copy()->subDay(),
            ];
        }

        // Sesi berikutnya (untuk info "menuju sesi selanjutnya")
        $indexSekarang = $sesiHariIni->search(function ($sesi) use ($sholatSedangBerlangsung) {
            return $sesi->nama_sholat === $sholatSedangBerlangsung->nama_sholat;
        });

        $sholatBerikutnya = $sesiHariIni->get($indexSekarang + 1) ?? $sesiHariIni->first();

        return view('admin.dashboard', compact(
            'totalKeluarga',
            'totalAnggota',
            'presensiHariIni',
            'daftarSholat',
            'chartData',
            'jumlahKeluargaAktif',
            'keluargaFullSesiHariIni',
            'sholatSedangBerlangsung',
            'sholatBerikutnya',
        ));
    }
}