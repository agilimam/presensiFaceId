<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class keluarga extends Model
{
    // Karena nama tabelmu 'keluarga' (Laravel mencari 'keluargas')
    protected $table = 'keluarga'; 
    
    // Karena Primary Key kamu bukan 'id'
    protected $primaryKey = 'id_keluarga'; 

    // Isi sesuai kolom di migration kamu
    protected $fillable = [
        'id_user',
        'nama_keluarga',
        'nik',
    ];

    // Relasi balik ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Relasi ke Anggota Keluarga
    public function anggotaKeluarga()
    {
        return $this->hasMany(AnggotaKeluarga::class, 'id_keluarga', 'id_keluarga');
    }
}
