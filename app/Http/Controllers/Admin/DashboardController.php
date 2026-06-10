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
        $kemarin  = Carbon::yesterday('Asia/Jakarta');

        // ─────────────────────────────────────────────────────────────────
        // 1. STATISTIK UTAMA
        // ─────────────────────────────────────────────────────────────────
        $totalKeluarga   = keluarga::count();
        $totalAnggota    = anggotaKeluarga::count();
        $presensiHariIni = presensi::whereDate('waktu_absen', $hari_ini)->count();

        // ─────────────────────────────────────────────────────────────────
        // 2. DATA GRAFIK — Tren per sesi HARI INI
        // ─────────────────────────────────────────────────────────────────
        $daftarSholat = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];

        $absenHariIniRaw = presensi::whereDate('waktu_absen', $hari_ini)->get();

        $chartData = [];
        foreach ($daftarSholat as $sholat) {
            $chartData[] = $absenHariIniRaw->where('keterangan_sholat', $sholat)->count();
        }

      
        // 3. STATISTIK KELUARGA — dihitung dari HARI INI
        $jumlahKeluargaAktif = presensi::whereDate('waktu_absen', $hari_ini)
            ->distinct('id_keluarga')
            ->count('id_keluarga');

        // Jumlah keluarga yang hadir lengkap 5 waktu hari ini
        // → cukup 1 anggota yang absen per sesi, keluarga sudah dihitung
        $keluargaFullSesiHariIni = presensi::whereDate('waktu_absen', $hari_ini)
            ->get()
            ->groupBy('id_keluarga')                   // kelompokkan per keluarga
            ->filter(function ($logs) {
                return $logs->pluck('keterangan_sholat')->unique()->count() >= 5;
            })
            ->count();

       
        // 4. LEADERBOARD ISTIQOMAH — 7 Hari Terakhir
        $topDisiplin = DB::table('presensi')
            ->join('keluarga', 'presensi.id_keluarga', '=', 'keluarga.id_keluarga')
            ->where('presensi.waktu_absen', '>=', Carbon::now('Asia/Jakarta')->subDays(7)->startOfDay())
            ->select(
                'keluarga.id_keluarga',
                'keluarga.nama_keluarga',
                DB::raw('COUNT(DISTINCT DATE(presensi.waktu_absen), presensi.keterangan_sholat) as skor_istiqomah')
            )
            ->groupBy('keluarga.id_keluarga', 'keluarga.nama_keluarga')
            ->orderBy('skor_istiqomah', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalKeluarga',
            'totalAnggota',
            'presensiHariIni',
            'daftarSholat',
            'chartData',
            'jumlahKeluargaAktif',
            'keluargaFullSesiHariIni',
            'topDisiplin'
        ));


        
    }
    public function cleanStorage()
    {
        try {
            $logs  = \App\Models\Presensi::where('face_id', 'LIKE', '%.jpg%')->get();
            $count = 0;

            foreach ($logs as $log) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists('scan_masuk/' . $log->face_id)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete('scan_masuk/' . $log->face_id);
                    $log->update(['face_id' => 'Diverifikasi']);
                    $count++;
                }
            }

            return redirect()->back()->with('success', "Berhasil! $count berkas scan telah dibersihkan.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal membersihkan storage: ' . $e->getMessage());
        }
    }
}