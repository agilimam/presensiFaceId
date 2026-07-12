<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('presensi')->truncate();

        $anggota = DB::table('anggota_keluarga')->get();
        $jadwal = DB::table('jadwal_sholat')->get();

        foreach ($anggota as $anggotaKeluarga) {

            // Presensi 7 hari terakhir
            for ($hari = 6; $hari >= 0; $hari--) {

                $tanggal = Carbon::today('Asia/Jakarta')->subDays($hari);

                foreach ($jadwal as $jadwalSholat) {

                    // 75% kemungkinan hadir
                    if (rand(1,100) > 75) {
                        continue;
                    }

                    $jamMulai = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $tanggal->format('Y-m-d') . ' ' . $jadwalSholat->jam_mulai,
                        'Asia/Jakarta'
                    );

                    $batas = Carbon::createFromFormat(
                        'Y-m-d H:i:s',
                        $tanggal->format('Y-m-d') . ' ' . $jadwalSholat->batas_tepat_waktu,
                        'Asia/Jakarta'
                    );

                    if (rand(1,100) <= 80) {

                        // Tepat waktu
                        $status = 'Tepat Waktu';

                        $selisihMenit = $jamMulai->diffInMinutes($batas);

                        $waktuAbsen = $jamMulai->copy()->addMinutes(
                            rand(0, $selisihMenit)
                        );

                    } else {

                        // Terlambat (1-30 menit)
                        $status = 'Terlambat';

                        $waktuAbsen = $batas->copy()->addMinutes(
                            rand(1,30)
                        );
                    }

                    DB::table('presensi')->insert([
                        'id_keluarga' => $anggotaKeluarga->id_keluarga,
                        'id_anggota_keluarga' => $anggotaKeluarga->id_anggota_keluarga,
                        'id_jadwal' => $jadwalSholat->id_jadwal,
                        'waktu_absen' => $waktuAbsen->format('Y-m-d H:i:s'),
                        'face_id' => $anggotaKeluarga->face_id,
                        'status' => $status,
                    ]);
                }
            }
        }

        $this->command->info('✅ Presensi 7 hari berhasil dibuat.');
    }
}