<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class anggotaKeluarga extends Model
{
    protected $table = 'anggota_keluarga';
    protected $primaryKey = 'id_anggota_keluarga';

    protected $fillable = [
        'id_keluarga',
        'nama_anggota',
        'hubungan',
        'face_id',
        'status_wajah'
    ];

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga', 'id_keluarga');
    }
    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'id_anggota_keluarga', 'id_anggota_keluarga');
    }
}
