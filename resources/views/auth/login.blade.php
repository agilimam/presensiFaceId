<x-guest-layout>
    <div class="w-full max-w-4xl bg-white rounded-[2.5rem] shadow-2xl flex flex-col md:flex-row items-stretch overflow-hidden">
        
        <div class="w-full md:w-1/2 p-10 md:p-12 flex flex-col justify-center bg-white">
            <div class="mb-6">
                <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Portal Masuk</h2>
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mt-1">Masjid Al-Iman — Yogyakarta</p>
            </div>

            <x-auth-session-status class="mb-4 text-xs font-semibold text-emerald-600" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div class="relative">
                    <input id="username" 
                        class="block w-full px-0 py-2 bg-transparent border-b-2 border-gray-200 focus:border-emerald-500 focus:outline-none focus:ring-0 transition duration-200 text-sm text-gray-800 placeholder-gray-400" 
                        type="text" 
                        name="username" 
                        value="{{ old('username') }}" 
                        required 
                        autofocus 
                        placeholder="Username"
                        autocomplete="username" />
                    <x-input-error :messages="$errors->get('username')" class="mt-1 text-[11px] font-semibold text-red-500" />
                </div>

                <div class="relative">
                    <input id="password" 
                        class="block w-full px-0 py-2 bg-transparent border-b-2 border-gray-200 focus:border-emerald-500 focus:outline-none focus:ring-0 transition duration-200 text-sm text-gray-800 placeholder-gray-400"
                        type="password"
                        name="password"
                        required 
                        placeholder="Password"
                        autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1 text-[11px] font-semibold text-red-500" />
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label Vans for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <input id="remember_me" type="checkbox" class="rounded bg-gray-100 border-transparent text-emerald-600 focus:ring-emerald-500 transition cursor-pointer" name="remember">
                        <span class="ms-2 text-xs font-semibold text-gray-400 group-hover:text-emerald-600 transition">Ingat saya</span>
                    </label>
                    <span class="text-[10px] font-semibold text-gray-400">
                        Lupa sandi? Hubungi Admin Masjid
                    </span>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full font-bold text-xs tracking-wider uppercase shadow-lg shadow-emerald-600/20 transform active:scale-[0.99] transition-all duration-200">
                        Masuk Sekarang
                    </button>
                </div>
            </form>
        </div>

        <div class="w-full md:w-1/2 bg-emerald-50/50 p-10 md:p-12 flex flex-col items-center justify-center text-center border-t md:border-t-0 md:border-l border-gray-100/80">
            
            <div class="w-20 h-20 bg-white rounded-2xl shadow-xl shadow-emerald-900/5 flex items-center justify-center p-4 mb-5 border border-emerald-100/50">
                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
            </div>

            <h3 class="text-lg font-extrabold text-gray-800 tracking-tight mb-2">Belum Punya Akun?</h3>
            <p class="text-xs text-gray-400 max-w-xs leading-relaxed mb-6">
                Silakan daftarkan kelompok keluarga Anda terlebih dahulu untuk dapat menggunakan sistem presensi berbasis pengenal wajah.
            </p>

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-block px-8 py-2.5 border-2 border-emerald-600 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-full font-bold text-xs tracking-wider uppercase transition-all duration-200">
                    Daftar Akun Keluarga
                </a>
            @endif
        </div>

    </div>
</x-guest-layout>