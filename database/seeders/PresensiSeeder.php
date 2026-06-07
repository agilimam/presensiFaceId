<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PresensiSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('presensi')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        $daftarAnggota = DB::table('anggota_keluarga')->get();

        // ─────────────────────────────────────────────────────────────────────
        // WAKTU SINKRON 100% DENGAN Model presensi::getSesiSekarang()
        // Format: menit dari 00:00
        // ─────────────────────────────────────────────────────────────────────
        $waktu = [
            'Subuh'   => ['azan' => 4*60+21,  'tepat' => 5*60+41],   // 261 – 341
            'Dzuhur'  => ['azan' => 11*60+35, 'tepat' => 12*60+55],  // 695 – 775
            'Ashar'   => ['azan' => 14*60+57, 'tepat' => 16*60+28],  // 897 – 988
            'Maghrib' => ['azan' => 17*60+28, 'tepat' => 17*60+58],  // 1048 – 1078
            'Isya'    => ['azan' => 18*60+41, 'tepat' => 19*60+11],  // 1121 – 1151
        ];

        // ─────────────────────────────────────────────────────────────────────
        // HELPER: hitung status PERSIS sama dengan logika di Model
        // ─────────────────────────────────────────────────────────────────────
        $hitungStatus = function (string $sesi, int $menitAbsen) use ($waktu): string {
            if ($sesi === 'Isya') {
                // Absen antara 00:00–04:20 (lewat tengah malam) → selalu Terlambat
                if ($menitAbsen < $waktu['Subuh']['azan']) {
                    return 'Terlambat';
                }
                // Absen antara 18:41–23:59
                return ($menitAbsen <= $waktu['Isya']['tepat']) ? 'Tepat Waktu' : 'Terlambat';
            }

            // Sesi selain Isya — sama persis dengan model
            return ($menitAbsen <= $waktu[$sesi]['tepat']) ? 'Tepat Waktu' : 'Terlambat';
        };

        // ─────────────────────────────────────────────────────────────────────
        // HELPER: random menit absen yang valid per sesi
        //
        // Range absen yang masuk akal:
        //   • Tepat Waktu : azan  s/d tepat
        //   • Terlambat   : tepat+1 s/d tepat+60
        //   • Isya Terlambat lewat tengah malam : tepat+1 s/d 23:59 ATAU 00:00 s/d subuh-1
        // ─────────────────────────────────────────────────────────────────────
        $randomMenit = function (string $sesi, bool $tepatWaktu) use ($waktu): int {
            $azan  = $waktu[$sesi]['azan'];
            $tepat = $waktu[$sesi]['tepat'];

            if ($tepatWaktu) {
                // Tepat Waktu: dari azan sampai batas tepat
                return rand($azan, $tepat);
            }

            // Terlambat
            if ($sesi === 'Isya') {
                // Isya terlambat bisa lewat tengah malam
                // 50% lewat tengah malam (00:00–04:20), 50% masih malam (19:12–23:59)
                if (rand(0, 1) === 0) {
                    // 19:12 – 23:59  (tepat+1 – 1439)
                    return rand($tepat + 1, 23*60+59);
                } else {
                    // 00:00 – 04:20  (0 – subuh azan - 1)
                    return rand(0, $waktu['Subuh']['azan'] - 1);
                }
            }

            // Sesi lain: tepat+1 sampai tepat+60 (maksimal 1 jam terlambat)
            return rand($tepat + 1, $tepat + 60);
        };

        // ─────────────────────────────────────────────────────────────────────
        // PILIH KELUARGA RAJIN (95% hadir) vs BIASA (40% hadir)
        // ─────────────────────────────────────────────────────────────────────
        $keluargaIds   = $daftarAnggota->pluck('id_keluarga')->unique()->values();
        $keluargaRajin = $keluargaIds->shuffle()->take(20);

        $dataPresensi = [];

        for ($i = 0; $i < 7; $i++) {
            $tanggal = Carbon::now('Asia/Jakarta')->subDays($i);

            foreach ($keluargaIds as $idKeluarga) {
                $isRajin         = $keluargaRajin->contains($idKeluarga);
                $anggotaKeluarga = $daftarAnggota->where('id_keluarga', $idKeluarga);

                foreach ($waktu as $namaSesi => $interval) {

                    // Tentukan apakah keluarga ini hadir di sesi ini
                    $hadir = rand(1, 100) <= ($isRajin ? 95 : 40);
                    if (! $hadir) {
                        continue;
                    }

                    // Tentukan tepat/terlambat terlebih dahulu
                    // Keluarga rajin: 80% tepat waktu, keluarga biasa: 50% tepat waktu
                    $tepatWaktu = rand(1, 100) <= ($isRajin ? 80 : 50);

                    // Ambil menit absen yang sesuai
                    $menitAbsen = $randomMenit($namaSesi, $tepatWaktu);

                    // Hitung status PERSIS seperti model (validasi silang)
                    $status = $hitungStatus($namaSesi, $menitAbsen);

                    // ─── Handle Isya yang melewati tengah malam ──────────────
                    // Jika menit < subuh, artinya absennya hari BERIKUTNYA
                    // (dini hari setelah Isya), maka tanggal +1 hari
                    if ($namaSesi === 'Isya' && $menitAbsen < $waktu['Subuh']['azan']) {
                        $tanggalAbsen = $tanggal->copy()->addDay();
                    } else {
                        $tanggalAbsen = $tanggal->copy();
                    }

                    $jam   = (int) floor($menitAbsen / 60);
                    $menit = $menitAbsen % 60;

                    foreach ($anggotaKeluarga as $anggota) {
                        $dataPresensi[] = [
                            'id_keluarga'         => $anggota->id_keluarga,
                            'id_anggota_keluarga' => $anggota->id_anggota_keluarga,
                            'face_id'             => 'Diverifikasi',
                            'waktu_absen'         => $tanggalAbsen->setTime($jam, $menit)->format('Y-m-d H:i:s'),
                            'keterangan_sholat'   => $namaSesi,
                            'status'              => $status,
                            
                        ];
                    }
                }
            }
        }

        
        foreach (array_chunk($dataPresensi, 200) as $chunk) {
            DB::table('presensi')->insert($chunk);
        }

        $total      = count($dataPresensi);
        $tepat      = count(array_filter($dataPresensi, fn($r) => $r['status'] === 'Tepat Waktu'));
        $terlambat  = $total - $tepat;

        $this->command->info("✅ Seeder sinkron 100% dengan Model!");
        $this->command->info("📊 Total presensi : {$total}");
        $this->command->info("✅ Tepat Waktu    : {$tepat}");
        $this->command->info("⏰ Terlambat      : {$terlambat}");
    }
}