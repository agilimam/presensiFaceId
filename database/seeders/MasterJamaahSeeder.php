<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class MasterJamaahSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        $this->command->info('🔄 Memulai Seeder: Mengisi database hingga tepat 100 jamaah...');

        $totalJamaahTarget = 100;
        $currentTotalJamaah = 0;
        $indexKeluarga = 1;

        while ($currentTotalJamaah < $totalJamaahTarget) {

            // Jumlah anggota 3 atau 4
            $jumlahAnggota = rand(3, 4);

            // Supaya total tetap tepat 100
            $sisa = $totalJamaahTarget - $currentTotalJamaah;
            if ($jumlahAnggota > $sisa) {
                $jumlahAnggota = $sisa;
            }

            // Nama Kepala Keluarga
            $namaAyah = $faker->name('male');

            // Username = nama kepala keluarga + nomor
            $username = Str::slug($namaAyah, '') . $indexKeluarga;

            // Nama keluarga
            $namaKeluarga = "Keluarga {$namaAyah}";

            // Insert user
            $userId = DB::table('users')->insertGetId([
                'username'   => $username,
                'password'   => Hash::make('password'),
                'role'       => 'keluarga',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert keluarga
            $idKeluarga = DB::table('keluarga')->insertGetId([
                'id_user'       => $userId,
                'nama_keluarga' => $namaKeluarga,
                'nik'           => $faker->nik(),
            ]);

            // Insert anggota keluarga
            for ($i = 1; $i <= $jumlahAnggota; $i++) {

                if ($i == 1) {
                    $nama = $namaAyah;
                    $hubungan = 'Kepala Keluarga';
                } elseif ($i == 2) {
                    $nama = $faker->name('female');
                    $hubungan = 'Ibu';
                } else {
                    $nama = $faker->name();
                    $hubungan = 'Anak';
                }

                DB::table('anggota_keluarga')->insert([
                    'id_keluarga'  => $idKeluarga,
                    'nama_anggota' => $nama,
                    'hubungan'     => $hubungan,
                    'face_id'      => null,
                    'status_wajah' => null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]);

                $currentTotalJamaah++;
            }

            $indexKeluarga++;
        }

        $this->command->info("✅ Seeder selesai!");
        $this->command->info("👨‍👩‍👧‍👦 Total keluarga : " . ($indexKeluarga - 1));
        $this->command->info("👥 Total jamaah   : {$currentTotalJamaah}");
        $this->command->info("🔑 Password seluruh akun : password");
    }
}