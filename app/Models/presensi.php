<?php

namespace App\Models;

use Carbon\Carbon;
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
        'face_id',
        'status',
    ];

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga', 'id_keluarga');
    }

    public function anggotaKeluarga()
    {
        return $this->belongsTo(AnggotaKeluarga::class, 'id_anggota_keluarga', 'id_anggota_keluarga');
    }
    public function jadwal()
    {
        return $this->belongsTo(JadwalSholat::class,'id_jadwal');
    }
}