<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }" 
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
      :class="{ 'dark': darkMode }">
<head>
    @livewireStyles
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Al-Iman - Presensi Masjid</title>

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-[#121212] text-gray-900 dark:text-gray-200 transition-colors duration-300">
    <div class="flex min-h-screen" x-cloak>
        <aside class="w-72 fixed inset-y-0 left-0 z-50 bg-white dark:bg-[#1e1e1e] border-r border-gray-200 dark:border-white/5 flex flex-col h-screen transition-all duration-300">
            @include('layouts.navigation')
        </aside>

        <div class="flex-1 ml-72 flex flex-col min-w-0">
            @isset($header)
            <header class="bg-white/80 dark:bg-[#121212]/80 backdrop-blur-md sticky top-0 z-40 border-b border-gray-200 dark:border-white/5">
                <div class="max-w-7xl mx-auto py-6 px-8 flex justify-between items-center">
                    <div class="text-emerald-600 dark:text-emerald-400 font-bold text-xl">
                        {{ $header }}
                    </div>
                </div>
            </header>
            @endisset

            <main class="p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Notifikasi Otomatis dari Controller
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#059669',
                background: document.documentElement.classList.contains('dark') ? '#212121' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#ef4444',
                background: document.documentElement.classList.contains('dark') ? '#212121' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
            });
        @endif
    </script>

    @stack('scripts')
    @livewireScripts
</body>
</html>