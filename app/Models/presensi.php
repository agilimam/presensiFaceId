<?php

namespace App\Models;

use  Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class presensi extends Model
{
    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';

    public $timestamps = false; 

    protected $fillable = [
        'id_keluarga',
        'id_anggota_keluarga',
        'waktu_absen',
        'keterangan_sholat',
        'face_id',
        'status',
    ];

    public function anggotaKeluarga()
    {
        return $this->belongsTo(AnggotaKeluarga::class, 'id_anggota_keluarga', 'id_anggota_keluarga');
    }

    public static function getSesiSekarang()
    {
        $now = Carbon::now('Asia/Jakarta');
        // Menghitung total menit dari jam 00:00
        $totalMenit = (int)$now->format('H') * 60 + (int)$now->format('i');

        // Definisi Jam Azan & Batas Tepat Waktu (dalam menit dari jam 00:00)
        $waktu = [
            'Subuh'   => ['azan' => 4*60+21,  'tepat' => 5*60+41,  'label' => '04:21 - 05:41'],   
            'Dzuhur'  => ['azan' => 11*60+35, 'tepat' => 12*60+55, 'label' => '11:35 - 12:55'],  
            'Ashar'   => ['azan' => 14*60+57, 'tepat' => 16*60+28, 'label' => '14:57 - 16:28'],  
            'Maghrib' => ['azan' => 17*60+28, 'tepat' => 17*60+58, 'label' => '17:28 - 17:58'],  
            'Isya'    => ['azan' => 18*60+41, 'tepat' => 19*60+11, 'label' => '18:41 - 19:11'],  
        ];

        // 1. Tentukan Sesi
        if ($totalMenit >= $waktu['Isya']['azan'] || $totalMenit < $waktu['Subuh']['azan']) {
            $sesi = 'Isya';
        } elseif ($totalMenit >= $waktu['Subuh']['azan'] && $totalMenit < $waktu['Dzuhur']['azan']) {
            $sesi = 'Subuh';
        } elseif ($totalMenit >= $waktu['Dzuhur']['azan'] && $totalMenit < $waktu['Ashar']['azan']) {
            $sesi = 'Dzuhur';
        } elseif ($totalMenit >= $waktu['Ashar']['azan'] && $totalMenit < $waktu['Maghrib']['azan']) {
            $sesi = 'Ashar';
        } else {
            $sesi = 'Maghrib';
        }

        // 2. Logika Cek Status (Perbaikan Khusus Isya melewati tengah malam)
        if ($sesi === 'Isya') {
            // Jika jam sekarang antara 00:00 sampai 04:21 (Subuh)
            if ($totalMenit < $waktu['Subuh']['azan']) {
                $status = 'Terlambat';
            } else {
                // Jika jam sekarang antara 18:41 sampai 23:59
                $status = ($totalMenit <= $waktu['Isya']['tepat']) ? 'Tepat Waktu' : 'Terlambat';
            }
        } else {
            // Logika untuk sesi sholat selain Isya
            $status = ($totalMenit <= $waktu[$sesi]['tepat']) ? 'Tepat Waktu' : 'Terlambat';
        }

        return [
            'nama' => $sesi,
            'status' => $status,
            'range' => $waktu[$sesi]['label']
        ];
    }
}
