<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-emerald-800 p-4 font-sans">
        
        <!-- Main Split Card Container (Persegi Panjang Simetris & Proporsional) -->
        <div class="w-full max-w-4xl bg-white rounded-[2.5rem] shadow-2xl flex flex-col md:flex-row items-stretch overflow-hidden">
            
            <!-- Sisi Kiri: Form Pendaftaran -->
            <div class="w-full md:w-1/2 p-10 md:p-12 flex flex-col justify-center bg-white">
                <div class="mb-6">
                    <h2 class="text-2xl font-extrabold text-gray-800 tracking-tight">Daftar Akun</h2>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mt-1">Lengkapi Data Pendaftaran Keluarga</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <!-- Username Input -->
                    <div class="relative">
                        <input id="username" 
                            class="block w-full px-0 py-2 bg-transparent border-b-2 border-gray-200 focus:border-emerald-500 focus:ring-0 transition duration-200 text-sm text-gray-800 placeholder-gray-400" 
                            type="text" 
                            name="username" 
                            value="{{ old('username') }}" 
                            required 
                            autofocus 
                            placeholder="Username" />
                        <x-input-error :messages="$errors->get('username')" class="mt-1 text-[11px] font-semibold text-red-500" />
                    </div>

                    <!-- Nama Kepala Keluarga Input -->
                    <div class="relative">
                        <input id="nama_keluarga" 
                            class="block w-full px-0 py-2 bg-transparent border-b-2 border-gray-200 focus:border-emerald-500 focus:ring-0 transition duration-200 text-sm text-gray-800 placeholder-gray-400" 
                            type="text" 
                            name="nama_keluarga" 
                            value="{{ old('nama_keluarga') }}" 
                            required 
                            placeholder="Nama Lengkap (Kepala Keluarga)" />
                        <x-input-error :messages="$errors->get('nama_keluarga')" class="mt-1 text-[11px] font-semibold text-red-500" />
                    </div>

                    <!-- NIK Input -->
                    <div class="relative">
                        <input id="nik" 
                            class="block w-full px-0 py-2 bg-transparent border-b-2 border-gray-200 focus:border-emerald-500 focus:ring-0 transition duration-200 text-sm text-gray-800 placeholder-gray-400" 
                            type="text" 
                            name="nik" 
                            value="{{ old('nik') }}" 
                            required 
                            placeholder="NIK (Sesuai KTP 16 Digit)" />
                        <x-input-error :messages="$errors->get('nik')" class="mt-1 text-[11px] font-semibold text-red-500" />
                    </div>

                    <!-- Password Fields Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="relative">
                            <input id="password" 
                                class="block w-full px-0 py-2 bg-transparent border-b-2 border-gray-200 focus:border-emerald-500 focus:ring-0 transition duration-200 text-sm text-gray-800 placeholder-gray-400"
                                type="password"
                                name="password"
                                required 
                                autocomplete="new-password"
                                placeholder="Password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-1 text-[11px] font-semibold text-red-500" />
                        </div>

                        <div class="relative">
                            <input id="password_confirmation" 
                                class="block w-full px-0 py-2 bg-transparent border-b-2 border-gray-200 focus:border-emerald-500 focus:ring-0 transition duration-200 text-sm text-gray-800 placeholder-gray-400"
                                type="password"
                                name="password_confirmation"
                                required 
                                placeholder="Konfirmasi Password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1 text-[11px] font-semibold text-red-500" />
                        </div>
                    </div>

                    <!-- Button Action -->
                    <div class="pt-2">
                        <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-full font-bold text-xs tracking-wider uppercase shadow-lg shadow-emerald-600/20 transform active:scale-[0.99] transition-all duration-200">
                            {{ __('Buat Akun Baru') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Sisi Kanan: Panel Info (Tinggi Otomatis Mengikuti Sisi Kiri) -->
            <div class="w-full md:w-1/2 bg-emerald-50/50 p-10 md:p-12 flex flex-col items-center justify-center text-center border-t md:border-t-0 md:border-l border-gray-100/80">
                
                <!-- Circular Icon / Logo Box Layout -->
                <div class="w-20 h-20 bg-white rounded-2xl shadow-xl shadow-emerald-900/5 flex items-center justify-center p-4 mb-5 border border-emerald-100/50">
                    <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>

                <h3 class="text-lg font-extrabold text-gray-800 tracking-tight mb-2">Sudah Terdaftar?</h3>
                <p class="text-xs text-gray-400 max-w-xs leading-relaxed mb-6">
                    Silakan masuk menggunakan akun Anda untuk mengakses sistem monitoring dan manajemen kehadiran.
                </p>

                <!-- Outlined Action Button -->
                <a href="{{ route('login') }}" class="inline-block px-8 py-2.5 border-2 border-emerald-600 hover:bg-emerald-600 text-emerald-600 hover:text-white rounded-full font-bold text-xs tracking-wider uppercase transition-all duration-200">
                    {{ __('Masuk Ke Portal') }}
                </a>
            </div>

        </div>
    </div>
</x-guest-layout>