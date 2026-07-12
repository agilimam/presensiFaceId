<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalSholat extends Model
{
    protected $table = 'jadwal_sholat';

    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'nama_sholat',
        'jam_mulai',
        'batas_tepat_waktu',
    ];
}