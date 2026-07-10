<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('🔄 Membuat data presensi 7 hari terakhir...');

        $anggota = DB::table('anggota_keluarga')->get();

        $jadwalSholat = [
            'Subuh'   => '04:40',
            'Dzuhur'  => '12:10',
            'Ashar'   => '15:20',
            'Maghrib' => '18:00',
            'Isya'    => '19:10',
        ];

        // 7 hari ke belakang (10 Juli sampai 4 Juli)
        for ($hari = 1; $hari <= 7; $hari++) {

            $tanggal = Carbon::today()->subDays($hari);

            foreach ($anggota as $jamaah) {

                /*
                * Banyak sholat yang dihadiri hari itu
                * 0 = tidak hadir sama sekali
                * 1 = hadir 1 kali
                * ...
                * 5 = hadir semua
                */

                $jumlahHadir = rand(0, 5);

                if ($jumlahHadir == 0) {
                    continue;
                }

                $namaSholat = array_keys($jadwalSholat);

                shuffle($namaSholat);

                $sholatHariIni = array_slice($namaSholat, 0, $jumlahHadir);

                foreach ($sholatHariIni as $sholat) {

                    [$jam, $menit] = explode(':', $jadwalSholat[$sholat]);

                    $waktuAbsen = $tanggal
                        ->copy()
                        ->setTime($jam, $menit)
                        ->addMinutes(rand(0, 10));

                    DB::table('presensi')->insert([
                        'id_keluarga'          => $jamaah->id_keluarga,
                        'id_anggota_keluarga'  => $jamaah->id_anggota_keluarga,
                        'keterangan_sholat'    => $sholat,
                        'waktu_absen'          => $waktuAbsen,
                        'face_id'              => null,
                        'status'               => 'HADIR',
                    ]);
                }
            }
        }

        $this->command->info('✅ Data presensi berhasil dibuat.');
    }
}