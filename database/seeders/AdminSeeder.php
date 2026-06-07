<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        user::create([
            'username' => 'admin',
            'password' => bcrypt('admin123'), 
            'role' => 'admin', 
        ]);
    }
}
