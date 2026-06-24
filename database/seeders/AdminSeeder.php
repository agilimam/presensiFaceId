<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pakai updateOrCreate: Kalau admin sudah ada, dia bakal update.
        // Kalau belum ada, dia bakal buat baru. Aman!
        User::updateOrCreate(
            ['username' => 'admin'], // Kunci pencariannya
            [
                'password' => Hash::make('admin123'), // Hash::make lebih standar Laravel
                'role'     => 'admin', 
            ]
        );
    }
}