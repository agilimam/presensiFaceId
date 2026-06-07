<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-[#0a0a0a] p-4">
        
        <!-- Logo & Title Section -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-600 rounded-2xl shadow-xl shadow-emerald-500/20 mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
            </div>
            <h2 class="text-3xl font-black tracking-tighter text-gray-900 dark:text-white">Portal Presensi</h2>
            <p class="text-emerald-600 font-bold uppercase tracking-widest text-xs mt-1">Masjid Al-Iman</p>
        </div>

        <!-- Login Card -->
        <div class="w-full max-w-md bg-white dark:bg-[#161615] border border-gray-200 dark:border-white/10 rounded-[2rem] p-8 shadow-2xl relative overflow-hidden">
            
            <!-- Session Status -->
            <x-auth-session-status class="mb-4 text-center text-sm font-medium text-emerald-600" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Username -->
                <div>
                    <label for="username" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 ml-1">
                        {{ __('Username') }}
                    </label>
                    <input id="username" 
                        class="block w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-white/5 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-[#1c1c1b] focus:ring-4 focus:ring-emerald-500/10 transition duration-200 text-sm dark:text-white" 
                        type="text" 
                        name="username" 
                        :value="old('username')" 
                        required 
                        autofocus 
                        placeholder="Masukkan username anda..."
                        autocomplete="username" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2 text-xs font-semibold text-red-500" />
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 ml-1">
                        {{ __('Password') }}
                    </label>
                    <input id="password" 
                        class="block w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-white/5 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-[#1c1c1b] focus:ring-4 focus:ring-emerald-500/10 transition duration-200 text-sm dark:text-white"
                        type="password"
                        name="password"
                        required 
                        placeholder="••••••••"
                        autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-semibold text-red-500" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                        <input id="remember_me" type="checkbox" class="rounded-md bg-gray-100 dark:bg-white/5 border-transparent text-emerald-600 focus:ring-emerald-500 transition cursor-pointer" name="remember">
                        <span class="ms-2 text-xs font-semibold text-gray-500 dark:text-gray-400 group-hover:text-emerald-600 transition">{{ __('Ingat saya') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-xs font-bold text-emerald-600 hover:text-emerald-500 transition underline decoration-2 underline-offset-4" href="{{ route('password.request') }}">
                            {{ __('Lupa sandi?') }}
                        </a>
                    @endif
                </div>

                <!-- Action Button -->
                <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black text-sm shadow-xl shadow-emerald-500/20 transform active:scale-[0.98] transition-all duration-200">
                    {{ __('MASUK KE SISTEM') }}
                </button>
            </form>

            @if (Route::has('register'))
                <p class="mt-8 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">
                    Jamaah baru? 
                    <a href="{{ route('register') }}" class="text-emerald-600 hover:text-emerald-500 font-bold transition">Daftar sekarang</a>
                </p>
            @endif
        </div>

        <p class="mt-8 text-xs text-gray-400 font-medium">
            &copy; {{ date('Y') }} Masjid Al-Iman — Yogyakarta
        </p>
    </div>
    
</x-guest-layout>