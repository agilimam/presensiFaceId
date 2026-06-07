<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 dark:bg-[#0a0a0a] p-4 py-12">
        
        <!-- Header Section -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-emerald-600 rounded-2xl shadow-xl shadow-emerald-500/20 mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <h2 class="text-3xl font-black tracking-tighter text-gray-900 dark:text-white">Daftar Akun</h2>
            <p class="text-emerald-600 font-bold uppercase tracking-widest text-xs mt-1">Jamaah Masjid Al-Iman</p>
        </div>

        <!-- Registration Card -->
        <div class="w-full max-w-lg bg-white dark:bg-[#161615] border border-gray-200 dark:border-white/10 rounded-[2rem] p-8 shadow-2xl relative">
            
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
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
                        placeholder="Contoh: ahmad_user" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2 text-xs font-semibold text-red-500" />
                </div>

                <!-- Nama Keluarga -->
                <div>
                    <label for="nama_keluarga" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 ml-1">
                        {{ __('Nama Keluarga (Kepala Keluarga)') }}
                    </label>
                    <input id="nama_keluarga" 
                        class="block w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-white/5 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-[#1c1c1b] focus:ring-4 focus:ring-emerald-500/10 transition duration-200 text-sm dark:text-white" 
                        type="text" 
                        name="nama_keluarga" 
                        :value="old('nama_keluarga')" 
                        required 
                        placeholder="Nama lengkap kepala keluarga" />
                    <x-input-error :messages="$errors->get('nama_keluarga')" class="mt-2 text-xs font-semibold text-red-500" />
                </div>

                <!-- NIK -->
                <div>
                    <label for="nik" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 ml-1">
                        {{ __('NIK (Sesuai KTP)') }}
                    </label>
                    <input id="nik" 
                        class="block w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-white/5 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-[#1c1c1b] focus:ring-4 focus:ring-emerald-500/10 transition duration-200 text-sm dark:text-white" 
                        type="text" 
                        name="nik" 
                        :value="old('nik')" 
                        required 
                        placeholder="16 Digit NIK" />
                    <x-input-error :messages="$errors->get('nik')" class="mt-2 text-xs font-semibold text-red-500" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                            autocomplete="new-password"
                            placeholder="••••••••" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs font-semibold text-red-500" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-2 ml-1">
                            {{ __('Konfirmasi') }}
                        </label>
                        <input id="password_confirmation" 
                            class="block w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-white/5 border-transparent focus:border-emerald-500 focus:bg-white dark:focus:bg-[#1c1c1b] focus:ring-4 focus:ring-emerald-500/10 transition duration-200 text-sm dark:text-white"
                            type="password"
                            name="password_confirmation"
                            required 
                            placeholder="••••••••" />
                    </div>
                </div>

                <!-- Action Section -->
                <div class="pt-4 space-y-4">
                    <button type="submit" class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black text-sm shadow-xl shadow-emerald-500/20 transform active:scale-[0.98] transition-all duration-200">
                        {{ __('DAFTAR AKUN KELUARGA') }}
                    </button>

                    <div class="text-center">
                        <a class="text-xs font-bold text-gray-500 dark:text-gray-400 hover:text-emerald-600 transition underline decoration-2 underline-offset-4" href="{{ route('login') }}">
                            Sudah punya akun? Masuk di sini
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <p class="mt-8 text-xs text-gray-400 font-medium">
            &copy; {{ date('Y') }} Masjid Al-Iman — Yogyakarta
        </p>
    </div>
</x-guest-layout>