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
       
        $totalMenit = (int)$now->format('H') * 60 + (int)$now->format('i');

       
        $waktu = [
            'Subuh'   => ['azan' => 4*60+21,  'tepat' => 5*60+41,  'label' => '04:21 - 05:41'],   
            'Dzuhur'  => ['azan' => 11*60+35, 'tepat' => 12*60+55, 'label' => '11:35 - 12:55'],  
            'Ashar'   => ['azan' => 14*60+57, 'tepat' => 16*60+28, 'label' => '14:57 - 16:28'],  
            'Maghrib' => ['azan' => 17*60+28, 'tepat' => 17*60+58, 'label' => '17:28 - 17:58'],  
            'Isya'    => ['azan' => 18*60+41, 'tepat' => 19*60+11, 'label' => '18:41 - 19:11'],  
        ];

      
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

       
        if ($sesi === 'Isya') {
         
            if ($totalMenit < $waktu['Subuh']['azan']) {
                $status = 'Terlambat';
            } else {
               
                $status = ($totalMenit <= $waktu['Isya']['tepat']) ? 'Tepat Waktu' : 'Terlambat';
            }
        } else {
            
            $status = ($totalMenit <= $waktu[$sesi]['tepat']) ? 'Tepat Waktu' : 'Terlambat';
        }

        return [
            'nama' => $sesi,
            'status' => $status,
            'range' => $waktu[$sesi]['label']
        ];
    }
}
