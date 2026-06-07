<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class MasterJamaahSeeder extends Seeder
{
    public function run()
    {
        
        $faker = Faker::create('id_ID'); 

        $this->command->info('🔄 Memulai Seeder: Mengisi database hingga TEPAT 100 Jamaah dengan anggota 3-4 orang per keluarga...');

        $totalJamaahTarget = 100;
        $currentTotalJamaah = 0;
        $indexKeluarga = 1;

        // Sistem akan terus membuat keluarga sampai total jamaah pas 100
        while ($currentTotalJamaah < $totalJamaahTarget) {
            
            // 1. Tentukan jumlah anggota untuk keluarga ini (3 atau 4 orang)
            $jumlahAnggota = rand(3, 4);

            // ANTISIPASI SISA: Jika sisa kuota ke angka 100 kurang dari 3, 
            // kita sesuaikan jumlah anggota keluarga terakhir agar pas 100
            $sisaKuota = $totalJamaahTarget - $currentTotalJamaah;
            if ($sisaKuota < $jumlahAnggota) {
                $jumlahAnggota = $sisaKuota;
            }

            // 2. Ambil nama laki-laki untuk sosok Ayah (Kepala Keluarga)
            $namaAyah = $faker->name('male'); 
            $namaKeluarga = 'Keluarga ' . $namaAyah;

            // 3. Buat Akun User login (username: keluarga1, keluarga2, dst)
            $userId = DB::table('users')->insertGetId([
                'username'   => 'keluarga' . $indexKeluarga,          
                'password'   => Hash::make('password'),   
                'role'       => 'keluarga',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Buat Data Keluarga
            $idKeluarga = DB::table('keluarga')->insertGetId([
                'id_user'       => $userId,
                'nama_keluarga' => $namaKeluarga, 
                'nik'           => $faker->nik(), 
            ]);

            // 5. Masukkan anggota keluarga sesuai jumlah yang ditentukan (3 atau 4 orang)
            for ($j = 1; $j <= $jumlahAnggota; $j++) {
                
                if ($j == 1) {
                    $nama = $namaAyah; // Nama Kepala Keluarga sama dengan nama master keluarga
                    $hubungan = 'Kepala Keluarga'; 
                } elseif ($j == 2 && $jumlahAnggota > 1) {
                    $nama = $faker->name('female'); // Nama Ibu
                    $hubungan = 'Ibu';
                } else {
                    $nama = $faker->name(); // Nama Anak
                    $hubungan = 'Anak';
                }

                // 6. Insert ke tabel anggota_keluarga
                DB::table('anggota_keluarga')->insert([
                    'id_keluarga'   => $idKeluarga,
                    'nama_anggota'  => $nama,
                    'hubungan'      => $hubungan, 
                    'face_id'       => null,        
                ]);

           
                $currentTotalJamaah++;
            }

            $indexKeluarga++;
        }

        $totalKeluargaTerbuat = $indexKeluarga - 1;
        $this->command->info("✅ Sukses! Berhasil membuat {$totalKeluargaTerbuat} keluarga dengan total TEPAT {$currentTotalJamaah} Jamaah.");
    }
}