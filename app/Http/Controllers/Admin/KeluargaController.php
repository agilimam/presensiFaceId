<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\keluarga; 
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class KeluargaController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $daftarKeluarga = keluarga::with([
            'user',
            'anggotaKeluarga'
        ])
        ->when($search, function ($query) use ($search) {
            $query->where('nama_keluarga', 'like', "%{$search}%")
                ->orWhereHas('anggotaKeluarga', function ($q) use ($search) {
                    $q->where('nama_anggota', 'like', "%{$search}%");
                });
        })
        ->orderBy('id_keluarga', 'asc')
        ->paginate(9)
        ->withQueryString();

        return view('admin.data_keluarga.index', compact(
            'daftarKeluarga',
            'search'
        ));
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

    public function indexAkun(Request $request)
    {
        $search = $request->search;

        $daftarAkun = User::with('keluarga')
            ->where('role', 'keluarga')
            ->when($search, function ($query) use ($search) {
                return $query->where('username', 'like', "%{$search}%")
                             ->orWhereHas('keluarga', function ($q) use ($search) {
                                 $q->where('nama_keluarga', 'like', "%{$search}%");
                             });
            })
            ->orderBy('username', 'asc') 
            ->paginate(9)
            ->withQueryString();

        return view('admin.manajemen_akun.index', compact('daftarAkun', 'search'));
    }

    
    public function updatePassword(Request $request, $username): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Kolom password baru wajib diisi.',
            'password.min' => 'Password minimal harus terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        try {
            $user = User::where('username', $username)->firstOrFail();
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return redirect()->back()->with('success', 'Password akun keluarga ' . $user->username . ' berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui password: ' . $e->getMessage());
        }
    }

    
    public function destroyAkun($username): RedirectResponse
    {
        DB::beginTransaction();

        try {
         
            $user = User::where('username', $username)->firstOrFail();

         
            $dataKeluarga = keluarga::where('id_user', $user->id_user ?? $user->id)->first();

            if ($dataKeluarga) {
            
                $dataKeluarga->anggotaKeluarga()->delete(); 
                
            
                $dataKeluarga->delete();
            }

          
            $user->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Akun ' . $username . ' beserta seluruh data keluarga dan anggotanya berhasil dihapus permanen!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus akun dan data terkait: ' . $e->getMessage());
        }
    }
}