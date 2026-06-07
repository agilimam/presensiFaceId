<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white leading-tight uppercase tracking-tighter">
                    Admin Dashboard
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium mt-0.5">
                    Pusat Analitik & Keaktifan Jamaah Masjid Al-Iman
                </p>
            </div>

            <div x-data="{
                confirmClean() {
                    Swal.fire({
                        title: 'Bersihkan Storage?', text: 'Hapus foto verifikasi lama?', icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Ya, Bersihkan!'
                    }).then((result) => { if (result.isConfirmed) { document.getElementById('clean-storage-form').submit(); } });
                }
            }">
                <form id="clean-storage-form" action="{{ route('admin.clean.storage') }}" method="POST" class="hidden">@csrf</form>
                <button @click="confirmClean()" class="flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-[#262626] border border-red-200 dark:border-red-900/30 text-red-600 dark:text-red-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-50 dark:hover:bg-red-950/30 transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    Maintenance Storage
                </button>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ═══════════════════════════════════════════════════════
             STAT CARDS
        ═══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Total Keluarga --}}
            <div class="bg-white dark:bg-[#262626] p-6 rounded-[2rem] border border-gray-200 dark:border-white/5 flex items-center gap-5 shadow-sm">
                <div class="w-14 h-14 bg-emerald-50 dark:bg-white/5 rounded-2xl flex items-center justify-center text-emerald-600 font-bold border border-emerald-100 dark:border-white/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Keluarga</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-0.5">{{ $totalKeluarga }}</p>
                </div>
            </div>

            {{-- Total Jamaah --}}
            <div class="bg-white dark:bg-[#262626] p-6 rounded-[2rem] border border-gray-200 dark:border-white/5 flex items-center gap-5 shadow-sm">
                <div class="w-14 h-14 bg-emerald-50 dark:bg-white/5 rounded-2xl flex items-center justify-center text-emerald-600 font-bold border border-emerald-100 dark:border-white/10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Jamaah</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-0.5">{{ $totalAnggota }}</p>
                </div>
            </div>

            {{-- Absen Hari Ini --}}
            <div class="bg-emerald-600 p-6 rounded-[2rem] flex items-center gap-5 shadow-xl shadow-emerald-500/10">
                <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-white border border-white/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M5 11h14M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-emerald-100 uppercase tracking-widest">Absen Masuk Hari Ini</p>
                    <p class="text-2xl font-black text-white mt-0.5">{{ $presensiHariIni }}</p>
                </div>
            </div>

        </div>

        {{-- ═══════════════════════════════════════════════════════
             MAIN CONTENT
        ═══════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Grafik + Mini Cards --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Grafik Tren --}}
                <div class="bg-white dark:bg-[#212121] p-6 rounded-[2.5rem] border border-gray-200 dark:border-white/5 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-md font-bold text-gray-800 dark:text-white uppercase tracking-tight">
                                Grafik Tren Jamaah Hari Ini
                            </h3>
                            <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider mt-0.5">
                                Jumlah absen per waktu sholat
                            </p>
                        </div>
                    </div>
                    <div class="h-64 relative">
                        <canvas id="trenSholatChart"></canvas>
                    </div>
                </div>

                {{-- Mini Stat Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    {{-- Keluarga Aktif Hari Ini --}}
                    <div class="bg-white dark:bg-[#212121] p-6 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">
                                Keluarga Aktif Hari Ini
                            </span>
                            <h4 class="text-2xl font-black text-gray-800 dark:text-white mt-1">
                                {{ $jumlahKeluargaAktif }}
                                <span class="text-xs font-normal text-gray-400">Keluarga</span>
                            </h4>
                        </div>
                        <span class="text-2xl">⚡</span>
                    </div>

                    {{-- Full 5 Waktu Hari Ini --}}
                    <div class="bg-white dark:bg-[#212121] p-6 rounded-3xl border border-gray-200 dark:border-white/5 shadow-sm flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-wider">
                                Full 5 Waktu Hari Ini
                            </span>
                            <h4 class="text-2xl font-black text-emerald-500 mt-1">
                                {{ $keluargaFullSesiHariIni }}
                                <span class="text-xs font-normal text-gray-400">Keluarga</span>
                            </h4>
                        </div>
                        <span class="text-2xl">👑</span>
                    </div>

                </div>
            </div>

            {{-- ═══════════════════════════════════════════════════
                 LEADERBOARD ISTIQOMAH
            ═══════════════════════════════════════════════════ --}}
            <div class="bg-white dark:bg-[#212121] p-6 rounded-[2.5rem] border border-gray-200 dark:border-white/5 shadow-sm flex flex-col">

                <div class="mb-4">
                    <h3 class="text-md font-bold text-gray-800 dark:text-white uppercase tracking-tight">
                        LEADERBOARD 
                    </h3>
                    <p class="text-[9px] text-gray-400 uppercase font-bold tracking-wider mt-0.5">
                        Top 5 · Skor Kehadiran 7 Hari ·
                    </p>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-white/5 flex-1">
                    @forelse($topDisiplin as $index => $row)
                        <div class="flex items-center justify-between py-3.5">
                            <div class="flex items-center gap-3">

                                {{-- Badge peringkat --}}
                                @if($index === 0)
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs bg-amber-400/20 text-amber-500">
                                        🥇
                                    </div>
                                @elseif($index === 1)
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs bg-gray-200/60 dark:bg-white/10 text-gray-500">
                                        🥈
                                    </div>
                                @elseif($index === 2)
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs bg-orange-400/10 text-orange-500">
                                        🥉
                                    </div>
                                @else
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs bg-gray-100 dark:bg-white/5 text-gray-400">
                                        {{ $index + 1 }}
                                    </div>
                                @endif

                                <span class="text-xs font-bold text-gray-800 dark:text-gray-200 uppercase">
                                    {{ $row->nama_keluarga }}
                                </span>
                            </div>

                            {{-- Skor: gunakan skor_istiqomah dari controller --}}
                            <span class="px-2.5 py-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg text-[10px] font-black whitespace-nowrap">
                                🔥 {{ $row->skor_istiqomah }} / 35
                            </span>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full py-12 gap-2">
                            <span class="text-3xl">🕌</span>
                            <p class="text-xs text-gray-400 italic text-center">Belum ada data presensi<br>dalam 7 hari terakhir.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer info --}}
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-white/5">
                    <p class="text-[9px] text-gray-400 text-center leading-relaxed">
                        Skor dihitung per sholat per hari (maks 1 poin/sholat).
                    </p>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const ctx = document.getElementById('trenSholatChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($daftarSholat) !!},
                datasets: [
                    {
                        label: 'Jumlah Jamaah',
                        data: {!! json_encode($chartData) !!},
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.15)',
                            'rgba(16, 185, 129, 0.15)',
                            'rgba(16, 185, 129, 0.15)',
                            'rgba(16, 185, 129, 0.15)',
                            'rgba(16, 185, 129, 0.15)',
                        ],
                        borderColor: '#059669',
                        borderWidth: 2,
                        borderRadius: 10,
                        borderSkipped: false,
                    },
                    {
                        // Garis tren overlay di atas bar
                        label: 'Tren',
                        data: {!! json_encode($chartData) !!},
                        type: 'line',
                        borderColor: '#34d399',
                        backgroundColor: 'transparent',
                        borderWidth: 2.5,
                        tension: 0.4,
                        pointBackgroundColor: '#059669',
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
                        backgroundColor: '#1a1a1a',
                        titleColor: '#a3a3a3',
                        bodyColor: '#ffffff',
                        borderColor: '#262626',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            label: (ctx) => ` ${ctx.parsed.y} jamaah`,
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
                            color: 'rgba(156,163,175,0.08)',
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