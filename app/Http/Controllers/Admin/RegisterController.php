<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\anggotaKeluarga;
use Illuminate\Support\Facades\Storage;
use Exception;

class RegisterController extends Controller
{
    public function updateFace(Request $request, $id)
        {
            $request->validate(['image_face' => 'required']);

            try {
                $anggota = anggotaKeluarga::findOrFail($id);
                $base64Data = $request->image_face;

                if (str_contains($base64Data, ';base64,')) {
                    $image_parts = explode(";base64,", $base64Data);
                    $image_base64 = base64_decode($image_parts[1]);

                    // Gunakan nama sementara agar diproses Python
                    $tempFileName = 'temp_' . $id . '.jpg';
                    
                    // Simpan ke storage/app/public/faces/ (tapi namanya temp_)
                    Storage::disk('public')->put('faces/' . $tempFileName, $image_base64);

                    // Update database ke status PENDING
                    $anggota->update([
                        'face_id' => $tempFileName,
                        'status_wajah' => 'PENDING'
                    ]);

                    return redirect()->back()->with('success', 'Wajah sedang diverifikasi sistem, mohon tunggu beberapa detik.');
                }
                return redirect()->back()->with('error', 'Format gambar tidak valid.');
            } catch (Exception $e) {
                return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
            }
        }
    public function destroyFace($id)
    {
        try {
            $anggota = anggotaKeluarga::findOrFail($id);

            // Hapus file wajah jika ada
            if ($anggota->face_id &&
                Storage::disk('public')->exists('faces/' . $anggota->face_id)) {

                Storage::disk('public')->delete('faces/' . $anggota->face_id);
            }

            // Reset data wajah
            $anggota->update([
                'face_id' => null,
                'status_wajah' => null
            ]);

            return redirect()->back()->with(
                'success',
                'Face ID ' . $anggota->nama_anggota . ' berhasil direset.'
            );

        } catch (Exception $e) {

            return redirect()->back()->with(
                'error',
                'Gagal mereset data Face ID.'
            );
        }
    }
}