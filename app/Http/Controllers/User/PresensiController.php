<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Presensi;
use App\Models\Keluarga;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index()
    {
        return view('user.index');
    }

    public function scan(Request $request)
    {
        $user = Auth::user();

        $keluarga = Keluarga::where('id_user', $user->id_user)->first();

        if (!$keluarga) {
            return response()->json([
                'success' => false,
                'message' => 'Data keluarga tidak ditemukan.'
            ]);
        }

        try {

            $now = Carbon::now('Asia/Jakarta');

            /*
            |--------------------------------------------------------------------------
            | Ambil sesi sholat dari Model Presensi
            |--------------------------------------------------------------------------
            */
            $infoSesi = Presensi::getSesiSekarang();

            if (!$infoSesi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Saat ini belum memasuki waktu presensi.'
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan foto scan
            |--------------------------------------------------------------------------
            */
            $imageName = 'SCAN_' . $user->id_user . '_' . $now->timestamp . '.jpg';

            Storage::disk('public')->put(
                'scan_masuk/' . $imageName,
                base64_decode(
                    str_replace(
                        ['data:image/jpeg;base64,', ' '],
                        ['', '+'],
                        $request->image
                    )
                )
            );

            /*
            |--------------------------------------------------------------------------
            | Simpan data presensi
            |--------------------------------------------------------------------------
            */
            $presensi = Presensi::create([
                'id_keluarga'         => $keluarga->id_keluarga,
                'id_anggota_keluarga' => null,
                'id_jadwal'           => $infoSesi['id_jadwal'],
                'waktu_absen'         => $now,
                'status'              => $infoSesi['status'],
                'face_id'             => $imageName,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Tunggu Python mengenali wajah
            |--------------------------------------------------------------------------
            */
            sleep(5);

            $presensi->refresh();

            /*
            |--------------------------------------------------------------------------
            | Jika wajah berhasil dikenali
            |--------------------------------------------------------------------------
            */
            if ($presensi->id_anggota_keluarga) {

                $sudahAbsen = Presensi::where('id_anggota_keluarga', $presensi->id_anggota_keluarga)
                    ->where('id_jadwal', $infoSesi['id_jadwal'])
                    ->whereDate('waktu_absen', $now->toDateString())
                    ->where('id_presensi', '!=', $presensi->id_presensi)
                    ->exists();

                if ($sudahAbsen) {

                    $presensi->delete();

                    if (Storage::disk('public')->exists('scan_masuk/' . $imageName)) {
                        Storage::disk('public')->delete('scan_masuk/' . $imageName);
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah melakukan presensi ' . $infoSesi['nama'] . ' hari ini.'
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Presensi ' . $infoSesi['nama'] . ' berhasil dicatat.',
                    'status'  => $infoSesi['status']
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Jika wajah gagal dikenali
            |--------------------------------------------------------------------------
            */
            if (Storage::disk('public')->exists('scan_masuk/' . $imageName)) {
                Storage::disk('public')->delete('scan_masuk/' . $imageName);
            }

            $presensi->delete();

            return response()->json([
                'success' => false,
                'message' => 'WAJAH TIDAK DIKENALI! Pastikan wajah terlihat jelas dan sudah terdaftar.'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem.',
                'error'   => $e->getMessage()
            ]);
        }
    }
}