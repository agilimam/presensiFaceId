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
       
        $createdUser = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'keluarga',
        ]);

       
        $createdKeluarga = Keluarga::create([
            'id_user' => $createdUser->id_user, 
            'nama_keluarga' => $request->nama_keluarga,
            'nik' => $request->nik,
        ]);

        
        AnggotaKeluarga::create([
            'id_keluarga' => $createdKeluarga->id_keluarga, 
            'nama_anggota' => $request->nama_keluarga,      
            'hubungan' => 'Kepala Keluarga',               
        ]);

    return $createdUser;
    });

    event(new Registered($user));
    Auth::login($user);

    return redirect(route('dashboard'));
    }
}
