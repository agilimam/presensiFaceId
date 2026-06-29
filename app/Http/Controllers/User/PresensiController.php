<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Presensi; 
use App\Models\Keluarga;
use App\Models\AnggotaKeluarga;
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
            return response()->json(['success' => false, 'message' => 'Data keluarga tidak ditemukan.']);
        }

        try {
            $infoSesi = Presensi::getSesiSekarang(); 
            $now = Carbon::now('Asia/Jakarta');

            
            $imageName = 'SCAN_' . $user->id_user . '_' . $now->timestamp . '.jpg';
            Storage::disk('public')->put('scan_masuk/' . $imageName, base64_decode(str_replace(['data:image/jpeg;base64,', ' '], ['', '+'], $request->image)));

            $presensi = Presensi::create([
                'id_keluarga'         => $keluarga->id_keluarga, 
                'id_anggota_keluarga' => null, 
                'waktu_absen'         => $now,
                'keterangan_sholat'   => $infoSesi['nama'],   
                'status'              => $infoSesi['status'], 
                'face_id'             => $imageName,
            ]);

           
            sleep(5); 
            $presensi->refresh();

  
            if ($presensi->id_anggota_keluarga !== null && $presensi->id_anggota_keluarga > 0) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Berhasil! Kehadiran ' . $infoSesi['nama'] . ' telah dicatat.'
                ]);
            }

            
            if (Storage::disk('public')->exists('scan_masuk/' . $imageName)) {
                Storage::disk('public')->delete('scan_masuk/' . $imageName);
            }
            $presensi->delete();

            return response()->json([
                'success' => false, 
                'message' => 'WAJAH TIDAK DIKENALI! Pastikan wajah terlihat jelas, pencahayaan cukup, dan gunakan wajah terdaftar.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Sistem sibuk, silakan ulangi scan.']);
        }
    }
}