<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\keluarga;
use App\Models\anggotaKeluarga;
use App\Models\presensi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $hari_ini = Carbon::today('Asia/Jakarta');
        $now      = Carbon::now('Asia/Jakarta');
        $kemarin  = Carbon::yesterday('Asia/Jakarta');

        $totalKeluarga = keluarga::count();
        $totalAnggota  = anggotaKeluarga::count();

        $daftarSholat = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

   
        $absenHariIniRaw = presensi::whereDate('waktu_absen', $hari_ini)->get();

    
        $jadwal = presensi::getJadwalSholat();
        $subuhAzanHariIni = $hari_ini->copy()->addMinutes($jadwal['Subuh']['azan']);

        if ($now->gte($subuhAzanHariIni)) {
            $absenHariIniRaw = $absenHariIniRaw->reject(function ($item) use ($subuhAzanHariIni) {
                return $item->keterangan_sholat === 'Isya'
                    && Carbon::parse($item->waktu_absen)->lt($subuhAzanHariIni);
            });
        }

        $presensiHariIni = $absenHariIniRaw->count();

        $chartData = [];
        foreach ($daftarSholat as $sholat) {
            $chartData[] = $absenHariIniRaw->where('keterangan_sholat', $sholat)->count();
        }

        $jumlahKeluargaAktif = $absenHariIniRaw->pluck('id_keluarga')->unique()->count();

        $keluargaFullSesiHariIni = $absenHariIniRaw
            ->groupBy('id_keluarga')
            ->filter(function ($logs) {
                return $logs->pluck('keterangan_sholat')->unique()->count() >= 5;
            })
            ->count();

        return view('admin.dashboard', compact(
            'totalKeluarga',
            'totalAnggota',
            'presensiHariIni',
            'daftarSholat',
            'chartData',
            'jumlahKeluargaAktif',
            'keluargaFullSesiHariIni',
        ));
    }
}