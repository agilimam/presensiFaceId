
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Presensi Face ID - Masjid Al-Iman</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Instrument Sans', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>

    <body 
        x-data="{ loginModal: {{ $errors->any() ? 'true' : 'false' }} }"
        class="bg-gray-50 dark:bg-[#0a0a0a] text-gray-900 dark:text-white antialiased"
    >
        
        <!-- Navbar -->
        <nav class="max-w-7xl mx-auto px-6 py-6 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>

                <span class="text-xl font-bold tracking-tight text-emerald-700 dark:text-emerald-500 uppercase">
                    Al-Iman FaceID
                </span>
            </div>

            @if (Route::has('login'))
                <div class="flex gap-4 items-center">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="text-sm font-semibold hover:text-emerald-600 transition">
                            Dashboard
                        </a>
                    @else
                        <button 
                            @click="loginModal = true"
                            class="text-sm font-semibold hover:text-emerald-600 transition outline-none"
                        >
                            Log in
                        </button>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                               class="px-5 py-2 bg-emerald-600 text-white rounded-full text-sm font-bold shadow-md hover:bg-emerald-500 transition outline-none">
                                Daftar Jamaah
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </nav>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-6 py-16 lg:py-24 flex flex-col items-center text-center">

            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold mb-8 border border-emerald-200 dark:border-emerald-800">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                Sistem AI Presensi Aktif
            </div>

            <h1 class="text-5xl lg:text-7xl font-black tracking-tighter mb-6 leading-tight">
                Keamanan Digital untuk <br/>
                <span class="text-emerald-600">Kenyamanan Ibadah.</span>
            </h1>

            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mb-12 italic leading-relaxed">
                Selamat datang di portal manajemen Masjid Al-Iman. <br/>
                Sistem otomatis berbasis Python & Laravel Breeze untuk monitoring kehadiran jamaah secara real-time.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 mb-20">
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="px-10 py-4 bg-emerald-600 text-white rounded-2xl font-bold shadow-2xl hover:scale-105 transition duration-300">
                        Ke Dashboard
                    </a>
                @else
                    <button 
                        @click="loginModal = true"
                        class="px-10 py-4 bg-gray-900 dark:bg-emerald-600 text-white rounded-2xl font-bold shadow-2xl hover:scale-105 transition duration-300">
                        Mulai Presensi
                    </button>
                @endauth
            </div>

            <!-- Maps Section -->
            <div class="relative w-full max-w-5xl group">
                <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-[2rem] blur opacity-25 group-hover:opacity-40 transition duration-1000"></div>

                <div class="relative bg-white dark:bg-[#161615] border border-gray-200 dark:border-white/10 rounded-[2rem] p-2 shadow-2xl overflow-hidden">

                    <div class="rounded-[1.5rem] overflow-hidden aspect-video lg:aspect-[21/9]">
                        <iframe 
                            class="w-full h-full border-0 grayscale-[20%] contrast-[1.1] dark:invert-[90%] dark:hue-rotate-[180deg]"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.513574971239!2d110.3888358!3d-7.7352824!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a591e105e6085%3A0xc345f8f830604178!2sMasjid%20Al%20Iman!5e0!3m2!1sid!2sid!4v1714981234567!5m2!1sid!2sid"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>

                    <div class="absolute bottom-6 left-6 bg-white/90 dark:bg-[#161615]/90 backdrop-blur-md px-4 py-2 rounded-xl border border-gray-200 dark:border-white/10 shadow-lg">
                        <p class="text-xs font-bold text-emerald-700 dark:text-emerald-500 uppercase tracking-widest flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>

                            Lokasi: Masjid Al-Iman, Ngaglik
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <footer class="py-12 text-center text-sm text-gray-500 border-t border-gray-100 dark:border-white/5">
            &copy; {{ date('Y') }} Masjid Al-Iman. Dikembangkan dengan Laravel {{ app()->version() }} & Python.
        </footer>

        <!-- MODAL LOGIN -->
        <div 
            x-show="loginModal"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            x-cloak
        >
            <!-- Overlay -->
            <div 
                class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                @click="loginModal = false"
            ></div>

            <!-- Modal -->
            <div
                class="relative w-full max-w-md bg-white dark:bg-[#161615] border border-gray-200 dark:border-white/10 rounded-[2.5rem] p-8 shadow-2xl"
                x-show="loginModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
            >

                <!-- Close -->
                <button 
                    @click="loginModal = false"
                    class="absolute top-6 right-6 text-gray-400 hover:text-emerald-600"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-black tracking-tighter dark:text-white">
                        Portal Masuk
                    </h2>

                    <p class="text-emerald-600 font-bold uppercase tracking-widest text-[10px] mt-1">
                        Masjid Al-Iman
                    </p>
                </div>

                <!-- VALIDATION ERROR -->
                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 dark:bg-red-500/10 dark:border-red-500/20 p-4">
                        <div class="flex items-start gap-3">

                            <svg class="w-5 h-5 text-red-500 mt-0.5"
                                 fill="none"
                                 stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M12 8v4m0 4h.01M10.29 3.86l-7.5 13A1 1 0 003.66 18h16.68a1 1 0 00.87-1.5l-7.5-13a1 1 0 00-1.74 0z"/>
                            </svg>

                            <div class="text-sm text-red-700 dark:text-red-300">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>

                        </div>
                    </div>
                @endif

                <!-- FORM LOGIN -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Username -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">
                            Username
                        </label>

                        <input 
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            required
                            class="block w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-white/5 border-transparent focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 text-sm dark:text-white"
                            placeholder="Username anda"
                        >
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">
                            Password
                        </label>

                        <input 
                            type="password"
                            name="password"
                            required
                            class="block w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-white/5 border-transparent focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 text-sm dark:text-white"
                            placeholder="••••••••"
                        >
                    </div>

                    <!-- Submit -->
                    <button 
                        type="submit"
                        class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white rounded-2xl font-black text-sm shadow-xl shadow-emerald-500/20 transition-all"
                    >
                        MASUK SEKARANG
                    </button>
                </form>

                <!-- Register -->
                <p class="text-center mt-6 text-xs text-gray-400 font-semibold italic">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                       class="text-emerald-600 hover:underline">
                        Daftar Akun Keluarga
                    </a>
                </p>

            </div>
        </div>

    </body>
</html>
```
