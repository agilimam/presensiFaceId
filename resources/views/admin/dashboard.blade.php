<x-app-layout>
    <!-- Slot Header (Top Bar) -->
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white leading-tight uppercase tracking-tighter flex items-center gap-2">
                    <span class="text-emerald-600">📊</span> Admin Dashboard Analitik
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mt-0.5">
                    Pusat Monitoring Real-Time & Keaktifan Jamaah Masjid Al-Iman
                </p>
            </div>

            <div x-data="{
                confirmClean() {
                    Swal.fire({
                        title: 'Bersihkan Storage?', 
                        text: 'Hapus foto verifikasi lama dari penyimpanan?', 
                        icon: 'warning',
                        showCancelButton: true, 
                        confirmButtonColor: '#ef4444', 
                        confirmButtonText: 'Ya, Bersihkan!',
                        cancelButtonText: 'Batal',
                        background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                    }).then((result) => { if (result.isConfirmed) { document.getElementById('clean-storage-form').submit(); } });
                }
            }">
                <form id="clean-storage-form" action="{{ route('admin.clean.storage') }}" method="POST" class="hidden">@csrf</form>
                <button @click="confirmClean()" class="flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-[#262626] border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-2xl font-bold text-[10px] uppercase tracking-widest hover:bg-red-50 dark:hover:bg-red-950/30 transition-all shadow-xs active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Maintenance Storage
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ═══════════════════════════════════════════════════════
             STAT CARDS (DESAIN PREMIUM GRADIENT BARIS ATAS)
             ═══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Total Keluarga --}}
            <div class="bg-gradient-to-br from-white to-gray-50/50 dark:from-[#262626] dark:to-[#1c1c1c] p-6 rounded-[2rem] border border-gray-200/60 dark:border-white/5 flex items-center gap-5 shadow-xs relative overflow-hidden group">
                <div class="absolute right-0 bottom-0 text-gray-200/20 dark:text-white/2 font-bold text-8xl select-none translate-y-4 translate-x-2 transition-transform duration-300 group-hover:scale-110">🏠</div>
                <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold border border-emerald-100/70 dark:border-emerald-500/20 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Registrasi Keluarga</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-0.5">{{ $totalKeluarga }}</p>
                </div>
            </div>

            {{-- Total Jamaah --}}
            <div class="bg-gradient-to-br from-white to-gray-50/50 dark:from-[#262626] dark:to-[#1c1c1c] p-6 rounded-[2rem] border border-gray-200/60 dark:border-white/5 flex items-center gap-5 shadow-xs relative overflow-hidden group">
                <div class="absolute right-0 bottom-0 text-gray-200/20 dark:text-white/2 font-bold text-8xl select-none translate-y-4 translate-x-2 transition-transform duration-300 group-hover:scale-110">👥</div>
                <div class="w-14 h-14 bg-blue-50 dark:bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold border border-blue-100/70 dark:border-blue-500/20 shadow-xs">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Jamaah</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white mt-0.5">{{ $totalAnggota }}</p>
                </div>
            </div>

            {{-- Absen Hari Ini --}}
            <div class="bg-gradient-to-br from-emerald-600 to-teal-700 p-6 rounded-[2rem] flex items-center gap-5 shadow-lg shadow-emerald-600/10 relative overflow-hidden group">
                <div class="absolute right-0 bottom-0 text-emerald-500/20 font-bold text-8xl select-none translate-y-4 translate-x-2 transition-transform duration-300 group-hover:scale-110">✅</div>
                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-white border border-white/20 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest">Total Presensi Hari Ini</p>
                    <p class="text-3xl font-black text-white mt-0.5">{{ $presensiHariIni }}</p>
                </div>
            </div>
        </div>
        {{-- ═══════════════════════════════════════════════════════
     GRAFIK UTAMA FULL WIDTH
     ═══════════════════════════════════════════════════════ --}}
<div class="space-y-6">

    <div class="bg-white dark:bg-[#1e1e1e] p-6 rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-xs">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-md font-bold text-gray-800 dark:text-white uppercase tracking-tight flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Grafik Tren Jamaah Hari Ini
                </h3>

                <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider mt-0.5">
                    Fluktuasi kuantitas presensi jamaah berdasarkan waktu sholat fardhu
                </p>
                <div class="flex flex-wrap gap-2 mt-3">

                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-xs font-bold">
                        {{ $presensiHariIni }} Presensi
                    </span>

                    <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-500 text-xs font-bold">
                        {{ $jumlahKeluargaAktif }} Keluarga Aktif
                    </span>

                    <span class="px-3 py-1 rounded-full bg-yellow-500/10 text-yellow-500 text-xs font-bold">
                        {{ $keluargaFullSesiHariIni }} Full 5 Sesi
                    </span>

                </div>
            </div>
        </div>

        <div class="h-[350px] relative">
            <canvas id="trenSholatChart"></canvas>
        </div>
    </div>

    {{-- CARD BAWAH --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- KELUARGA AKTIF --}}
        <div class="bg-white dark:bg-[#1e1e1e] p-6 rounded-[2rem] border border-gray-100 dark:border-white/5 shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">
                    Keluarga Aktif Hari Ini
                    </p>

                    <h3 class="text-4xl font-black text-gray-900 dark:text-white mt-2">
                    {{ $jumlahKeluargaAktif }}
                    </h3>

                    <span class="inline-flex mt-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-xs font-semibold">
                    {{ round(($jumlahKeluargaAktif / max($totalKeluarga,1))*100) }}% aktif
                    </span>

                    <p class="text-sm text-gray-500 mt-2">
                    Keluarga yang melakukan presensi hari ini
                    </p>
                </div>

                <div class="text-6xl opacity-70">
                    🏠
                </div>
            </div>
        </div>

        {{-- SEMPURNA 5 SESI --}}
        <div class="bg-white dark:bg-[#1e1e1e] p-6 rounded-[2rem] border border-gray-100 dark:border-white/5 shadow-xs">
            <div class="flex items-center justify-between">
                <div>

                    <p class="text-[10px] font-bold text-yellow-500 uppercase tracking-widest">
                        Sempurna 5 Sesi
                    </p>

                    <h3 class="text-4xl font-black text-yellow-500 mt-2">
                        {{ $keluargaFullSesiHariIni }}
                    </h3>

                    <p class="text-sm text-gray-500 mt-2">
                        Keluarga hadir lengkap semua waktu sholat
                    </p>

                </div>

                <div class="text-6xl opacity-70">
                    ⭐
                </div>
            </div>
        </div>
    </div> {{-- END GRID CARD BAWAH --}}
</div> {{-- END SPACE-Y-6 --}}

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const ctx = document.getElementById('trenSholatChart').getContext('2d');

        const gradientBg = ctx.createLinearGradient(0, 0, 0, 300);
        gradientBg.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
        gradientBg.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($daftarSholat) !!},
                datasets: [
                    {
                        label: 'Jumlah Jamaah',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.08)',
                        borderColor: '#059669',
                        borderWidth: 2,
                        borderRadius: 12,
                        borderSkipped: false,
                        barThickness: 20,
                    },
                    {
                        label: 'Tren Kelancaran',
                        data: {!! json_encode($chartData) !!},
                        type: 'line',
                        borderColor: '#10b981',
                        backgroundColor: gradientBg,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#059669',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1.5,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#171717',
                        titleColor: '#9ca3af',
                        bodyColor: '#ffffff',
                        borderColor: '#262626',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: (ctx) => ` ${ctx.parsed.y} Jamaah Tercatat`,
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 11, weight: 'bold' }
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                        },
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 10 },
                            stepSize: 1,
                        },
                        border: { display: false }
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>