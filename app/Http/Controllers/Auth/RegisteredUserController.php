<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AnggotaKeluarga;
use App\Models\Keluarga;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
   public function store(Request $request): RedirectResponse{
    $request->validate([
        'username' => ['required', 'string', 'max:255', 'unique:users'],
        'nama_keluarga' => ['required', 'string', 'max:255'],
        'nik' => ['required', 'string', 'max:16', 'unique:keluarga'], 
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ],[
        'password.confirmed'=>'kata sandi tidak cocok dengan password yang dimasukkan',
    ]);

        $user = DB::transaction(function () use ($request) {
        // 1. Simpan ke tabel User
        $createdUser = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'keluarga',
        ]);

        // 2. Simpan ke tabel Keluarga
        $createdKeluarga = Keluarga::create([
            'id_user' => $createdUser->id_user, 
            'nama_keluarga' => $request->nama_keluarga,
            'nik' => $request->nik,
        ]);

        // 3. Tambahkan otomatis ke tabel AnggotaKeluarga sebagai Kepala Keluarga
        AnggotaKeluarga::create([
            'id_keluarga' => $createdKeluarga->id_keluarga, // Ambil ID dari hasil insert keluarga
            'nama_anggota' => $request->nama_keluarga,      // Ambil dari input
            'hubungan' => 'Kepala Keluarga',                // Set otomatis
            'status_wajah' => 'Belum Scan',                 // Default status
        ]);

    return $createdUser;
    });

    event(new Registered($user));
    Auth::login($user);

    return redirect(route('dashboard'));
    }
}
