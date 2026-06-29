<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class keluarga extends Model
{
  
    protected $table = 'keluarga'; 
    
  
    protected $primaryKey = 'id_keluarga'; 

    
    protected $fillable = [
        'id_user',
        'nama_keluarga',
        'nik',
    ];

   
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function anggotaKeluarga()
    {
        return $this->hasMany(AnggotaKeluarga::class, 'id_keluarga', 'id_keluarga');
    }
}
