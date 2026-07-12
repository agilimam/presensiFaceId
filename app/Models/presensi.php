<?php

namespace App\Models;

use App\Models\Keluarga;
use App\Models\AnggotaKeluarga;
use App\Models\JadwalSholat;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class presensi extends Model
{
    protected $table = 'presensi';
    protected $primaryKey = 'id_presensi';

    public $timestamps = false;

    protected $fillable = [
        'id_keluarga',
        'id_anggota_keluarga',
        'waktu_absen',
        'id_jadwal',
        'face_id',
        'status',
    ];

    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'id_keluarga', 'id_keluarga');
    }

    public function anggotaKeluarga()
    {
        return $this->belongsTo(AnggotaKeluarga::class, 'id_anggota_keluarga', 'id_anggota_keluarga');
    }
    public function jadwal()
    {
        return $this->belongsTo(JadwalSholat::class,'id_jadwal');
    }

    public static function getSesiSekarang()
    {
        $now = Carbon::now('Asia/Jakarta');

        $urutan = ['Subuh', 'Zuhur', 'Ashar', 'Magrib', 'Isya'];
        $jadwal = collect();

        foreach ($urutan as $nama) {
            $item = JadwalSholat::whereRaw('LOWER(nama_sholat)=?', [strtolower($nama)])
                ->first();

            if ($item) {
                $jadwal->push($item);
            }
        }

        if ($jadwal->isEmpty()) {
            return null;
        }

        $jumlah = $jadwal->count();

        for ($i = 0; $i < $jumlah; $i++) {

            $current = $jadwal[$i];
            $next = $jadwal[($i + 1) % $jumlah];

            $mulai = Carbon::today('Asia/Jakarta')
                ->setTimeFromTimeString($current->jam_mulai);
        
            $batas = Carbon::today('Asia/Jakarta')
                ->setTimeFromTimeString($current->batas_tepat_waktu);
            if ($batas->lt($mulai)) {
                $batas->addDay();
            }

            $akhir = Carbon::today('Asia/Jakarta')
                ->setTimeFromTimeString($next->jam_mulai);
            if ($akhir->lte($mulai)) {
                $akhir->addDay();
                if ($now->lt($mulai)) {
                    $mulai->subDay();
                    $batas->subDay();
                }
            }

            if ($now->between($mulai, $akhir)) {

                return [

                    'id_jadwal' => $current->id_jadwal,

                    'nama' => $current->nama_sholat,

                    'status' => $now->lte($batas)
                        ? 'Tepat Waktu'
                        : 'Terlambat',

                    'range' =>
                        Carbon::parse($current->jam_mulai)->format('H:i')
                        . ' - ' .
                        Carbon::parse($current->batas_tepat_waktu)->format('H:i'),

                    'jam_mulai' => $current->jam_mulai,

                    'batas_tepat_waktu' => $current->batas_tepat_waktu,
                ];
            }
        }

        return null;
    }
    
}