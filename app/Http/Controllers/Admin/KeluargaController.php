<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\keluarga; // Pastikan huruf besar/kecil sesuai file Model kamu
use App\Models\User;

class KeluargaController extends Controller
{
    
   public function index()
    {
        // Ganti ->latest() dengan ->orderBy('id_keluarga', 'desc') 
        // karena tabel kamu tidak memiliki kolom 'created_at'
        $daftarKeluarga = keluarga::with([
            'user', 
            'anggotaKeluarga' => function($query) {
                $query->orderByRaw("FIELD(hubungan, 'Kepala Keluarga', 'Ibu', 'Anak') ASC");
            }
        ])->orderBy('id_keluarga', 'desc')->get(); // <--- PERUBAHAN DI SINI

        return view('admin.data_keluarga.index', compact('daftarKeluarga'));
    }

   
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_keluarga' => 'required|string|max:255',
        ]);

        try {
            $keluarga = keluarga::findOrFail($id);
            $keluarga->update([
                'nama_keluarga' => $request->nama_keluarga
            ]);

            return redirect()->back()->with('success', 'Nama keluarga berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Keluarga sekaligus User Login
     */
    public function destroy($id)
    {
        try {
            $keluarga = keluarga::findOrFail($id);
            $userId = $keluarga->id_user;

            // 1. Hapus data keluarga
            $keluarga->delete();

            // 2. Hapus akun user terkait di tabel users agar sinkron
            if ($userId) {
                User::where('id_user', $userId)->delete();
            }

            return redirect()->route('admin.keluarga.index')->with('success', 'Keluarga dan akun user berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal hapus data.');
        }
    }
}