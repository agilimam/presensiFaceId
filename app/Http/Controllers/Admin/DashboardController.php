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
        $daftarSholat = JadwalSholat::orderBy('jam_mulai')->get();

   
        $absenHariIniRaw = presensi::whereDate('waktu_absen', $hari_ini)->get();
        $presensiHariIni = $absenHariIniRaw->count();

        $chartData = [];
        foreach ($daftarSholat as $jadwal) {
        $chartData[] = $absenHariIniRaw
                ->where('id_jadwal',$jadwal->id_jadwal)
                ->count();
            }
        $jumlahKeluargaAktif = $absenHariIniRaw->pluck('id_keluarga')->unique()->count();

        $keluargaFullSesiHariIni = $absenHariIniRaw
            ->groupBy('id_keluarga')
            ->filter(function ($logs) {
                return $logs->pluck('id_jadwal')->unique()->count() >= 5;
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