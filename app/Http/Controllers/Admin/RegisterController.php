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
                    $tempFileName = 'temp_' . $id . '.jpg';
                    
                    Storage::disk('public')->put('faces/' . $tempFileName, $image_base64);
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

            if ($anggota->face_id &&
                Storage::disk('public')->exists('faces/' . $anggota->face_id)) {

                Storage::disk('public')->delete('faces/' . $anggota->face_id);
            }

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
    
    public function updateAnggota(Request $request, $id)
    {
        $request->validate([
            'nama_anggota' => 'required|string|max:255'
        ]);

        try {
            $anggota = anggotaKeluarga::findOrFail($id);
            $namaLama = $anggota->nama_anggota;
            
            $anggota->update([
                'nama_anggota' => $request->nama_anggota
            ]);

            if ($anggota->hubungan === 'Kepala Keluarga') {

                $keluarga = \App\Models\Keluarga::find($anggota->id_keluarga);
                
                if ($keluarga) {
                    $keluarga->update([
                        'nama_keluarga' => $request->nama_anggota
                    ]);
                }
            }
            return redirect()->back()->with('success', 'Nama anggota ' . $namaLama . ' berhasil diubah menjadi ' . $request->nama_anggota);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah nama anggota: ' . $e->getMessage());
        }
    }
}