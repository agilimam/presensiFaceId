<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    protected $primaryKey = 'id_user';
    use HasFactory, Notifiable;
    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke tabel Keluarga (One-to-One)
     */
    public function keluarga()
    {
        return $this->hasOne(Keluarga::class, 'id_user', 'id_user');
    }

    /**
     * Helper untuk cek role di Controller/Middleware
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}