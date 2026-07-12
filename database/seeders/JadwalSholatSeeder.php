<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalSholat;

class JadwalSholatSeeder extends Seeder
{

    public function run(): void
    {
        $data = [
            ['nama_sholat' => 'Subuh',   'jam_mulai' => '04:30', 'batas_tepat_waktu' => '05:00'],
            ['nama_sholat' => 'Dzuhur',  'jam_mulai' => '12:00', 'batas_tepat_waktu' => '12:30'],
            ['nama_sholat' => 'Ashar',   'jam_mulai' => '15:15', 'batas_tepat_waktu' => '15:45'],
            ['nama_sholat' => 'Maghrib', 'jam_mulai' => '18:00', 'batas_tepat_waktu' => '18:15'],
            ['nama_sholat' => 'Isya',    'jam_mulai' => '19:15', 'batas_tepat_waktu' => '19:45'],
        ];

        foreach ($data as $item) {
            JadwalSholat::firstOrCreate(
                ['nama_sholat' => $item['nama_sholat']],
                $item
            );
        }
    }
}