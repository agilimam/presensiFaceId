<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white leading-tight uppercase tracking-tighter">
                    Log Presensi Mesjid Al-Iman
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase font-semibold tracking-wider">
                    {{ !$sesiSholatNama ? 'Monitoring Rekap Sholat Seluruh Keluarga' : 'Detail Kehadiran Sesi ' . $sesiSholatNama }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ openModalPdf: false, openModalDetail: false, selectedKeluarga: null }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- TOMBOL FILTER SESI SHOLAT --}}
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.presensi.index', request()->except('sholat')) }}"
                       class="px-4 py-2 rounded-xl border transition-all text-xs {{ !$sesiSholatPilihan ? 'bg-emerald-600 border-emerald-600 text-white font-bold' : 'bg-white dark:bg-[#262626] border-gray-200 dark:border-white/5 text-gray-600 dark:text-gray-400' }}">
                       Semua Sesi
                    </a>
                    @foreach($daftarSesi as $sesi)
                        <a href="{{ route('admin.presensi.index', array_merge(request()->query(), ['sholat' => $sesi->id_jadwal])) }}"
                           class="px-4 py-2 rounded-xl border transition-all text-xs {{ (string) $sesiSholatPilihan === (string) $sesi->id_jadwal ? 'bg-emerald-600 border-emerald-600 text-white font-bold' : 'bg-white dark:bg-[#262626] border-gray-200 dark:border-white/5 text-gray-600 dark:text-gray-400 hover:border-emerald-500' }}">
                            {{ $sesi->nama_sholat }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- SEARCH BAR & INPUT KALENDER TANGGAL --}}
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-white/5 p-4 rounded-3xl shadow-sm">
                <form action="{{ route('admin.presensi.index') }}" method="GET" class="w-full md:flex-1 flex flex-wrap items-center gap-3">
                    @if(request('sholat')) <input type="hidden" name="sholat" value="{{ request('sholat') }}"> @endif

                    <div class="w-full md:w-auto">
                        <input type="date" name="date" value="{{ $tanggalPilihan }}" onchange="this.form.submit()"
                               class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#262626] border-none rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 focus:ring-emerald-500 shadow-inner">
                    </div>

                    <div class="relative flex-1 min-w-[260px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kepala keluarga..."
                               class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-[#262626] border-none rounded-xl text-xs text-gray-700 dark:text-gray-200 focus:ring-emerald-500 shadow-inner">
                        <div class="absolute left-3 top-3 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        </div>
                    </div>
                </form>

                <button @click="openModalPdf = true" class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 rounded-xl text-white font-bold transition-all shadow-md shadow-emerald-500/10 text-xs uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Export PDF
                </button>
            </div>

            {{-- KONTEN REKAP TABEL UTAMA KELUARGA --}}
            <div class="bg-white dark:bg-[#212121] rounded-[2.5rem] border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-100 dark:border-white/5 flex justify-between items-center bg-gray-50/30 dark:bg-transparent">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white uppercase tracking-tight">Presensi Keaktifan Keluarga</h3>
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest mt-0.5">Tanggal: {{ \Carbon\Carbon::parse($tanggalPilihan)->translatedFormat('d F Y') }}</p>
                    </div>
                    <span class="text-[10px] bg-emerald-500 text-white px-4 py-1.5 rounded-full font-bold uppercase tracking-widest">
                        {{ $rekapKeluargaPaginator->total() }} Keluarga
                    </span>
                </div>

                <div class="overflow-x-auto">
                    {{-- GRID CARD VIEW --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        @forelse($rekapKeluargaPaginator as $data)
                        @php
                            // Sesi yang ditampilkan di modal detail: kalau ada filter sesi, cuma sesi itu.
                            // Kalau "Semua Sesi", tampilkan semua sesi yang punya data hari itu.
                            $sesiUntukDetail = $sesiSholatNama ? [$sesiSholatNama] : $daftarSesi->pluck('nama_sholat')->toArray();
                            $detailSesi = [];
                            foreach ($sesiUntukDetail as $sesi) {
                                if (isset($data[$sesi])) {
                                    $detailSesi[$sesi] = [
                                        'jam_pertama' => $data[$sesi]['jam_pertama'],
                                        'jamaah_hadir' => $data[$sesi]['jamaah_hadir'],
                                    ];
                                }
                            }
                        @endphp
                        <div class="bg-white dark:bg-[#1e1e1e] p-6 rounded-3xl border border-gray-100 dark:border-white/5 shadow-sm hover:shadow-lg transition-all"
                             x-data="{ detail: @js(['nama_keluarga' => $data['nama_keluarga'], 'kepala_keluarga' => $data['kepala_keluarga'], 'sesi' => $detailSesi]) }">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-sm font-black text-gray-800 dark:text-white uppercase truncate">{{ $data['kepala_keluarga'] }}</h4>
                                    <p class="text-[9px] text-emerald-500 font-bold uppercase tracking-widest">{{ $data['nama_keluarga'] }}</p>
                                </div>
                            </div>

                            {{-- Indikator 5 Waktu --}}
                            <div class="flex gap-2 mb-4">
                                @foreach($daftarSesi as $sesi)
                                    <div class="flex-1 flex flex-col items-center gap-1">
                                        <span class="text-[8px] font-bold text-gray-400">{{ \Illuminate\Support\Str::substr($sesi->nama_sholat, 0, 1) }}</span>
                                        <div class="w-full h-2 rounded-full {{ isset($data[$sesi->nama_sholat]) ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-[#333]' }}"></div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Footer Detail --}}
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] text-gray-400 uppercase font-bold">
                                    {{ isset($data['last_absen']) ? \Carbon\Carbon::parse($data['last_absen'])->format('H:i') : 'Belum Absen' }}
                                </span>
                                <button @click="selectedKeluarga = detail; openModalDetail = true"
                                        class="text-[9px] px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-widest transition-all">
                                    Detail
                                </button>
                            </div>
                        </div>
                        @empty
                        <div class="col-span-full py-20 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                            Tidak ada data presensi keluarga.
                        </div>
                        @endforelse
                    </div>

                    {{-- PAGINATION LINKS --}}
                    <div class="px-6 pb-6">
                        {{ $rekapKeluargaPaginator->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL FILTER PDF (SATU INPUT TANGGAL SEJAJAR) --}}
        <div x-show="openModalPdf" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 dark:bg-black/80" @click="openModalPdf = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-[#1f1f1f] rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-8 border border-gray-100 dark:border-white/5">
                    <div class="mb-5">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Cetak PDF</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pilih rentang tanggal laporan.</p>
                    </div>

                    <form id="formExportPdf" action="{{ route('admin.presensi.export') }}" method="GET" @submit="openModalPdf = false" target="_blank">
                        <input type="hidden" id="inputAksiPdf" name="aksi" value="preview">

                        {{-- DUA INPUT TANGGAL SEJAJAR TANPA TOGGLE --}}
                        <div class="grid grid-cols-2 gap-4 bg-gray-50 dark:bg-[#181818] p-4 rounded-2xl border border-gray-100 dark:border-white/5 shadow-inner">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5">Mulai</label>
                                <input type="date" name="date_start" required value="{{ date('Y-m-d') }}"
                                       class="w-full px-3 py-2 bg-white dark:bg-[#262626] border border-gray-200 dark:border-white/5 rounded-xl text-xs font-bold text-gray-800 dark:text-gray-200">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5">Sampai</label>
                                <input type="date" name="date_end" required value="{{ date('Y-m-d') }}"
                                       class="w-full px-3 py-2 bg-white dark:bg-[#262626] border border-gray-200 dark:border-white/5 rounded-xl text-xs font-bold text-gray-800 dark:text-gray-200">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5">Sesi Sholat</label>
                            <select name="sholat"
                                    class="w-full px-3 py-2.5 bg-gray-50 dark:bg-[#181818] border border-gray-100 dark:border-white/5 rounded-xl text-xs font-bold text-gray-800 dark:text-gray-200 shadow-inner">
                                <option value="">Semua Sesi</option>
                                @foreach($daftarSesi as $sesi)
                                    <option value="{{ $sesi->id_jadwal }}">{{ $sesi->nama_sholat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mt-6 flex justify-end gap-2 border-t border-gray-100 dark:border-white/5 pt-4">
                            <button type="button" @click="openModalPdf = false" class="px-5 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs rounded-xl uppercase font-bold transition-all">Batal</button>
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs rounded-xl uppercase font-bold transition-all shadow-lg shadow-emerald-500/20">Preview</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL DETAIL KEHADIRAN KELUARGA --}}
        <div x-show="openModalDetail" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-900/60 dark:bg-black/80" @click="openModalDetail = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div class="inline-block align-bottom bg-white dark:bg-[#1f1f1f] rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-8 border border-gray-100 dark:border-white/5"
                     x-show="openModalDetail" x-transition>
                    <template x-if="selectedKeluarga">
                        <div>
                            <div class="mb-5">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight" x-text="selectedKeluarga.kepala_keluarga"></h3>
                                <p class="text-xs text-emerald-500 font-bold uppercase tracking-widest mt-1" x-text="selectedKeluarga.nama_keluarga"></p>
                            </div>

                            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
                                <template x-if="Object.keys(selectedKeluarga.sesi).length === 0">
                                    <div class="py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-xs">
                                        Belum ada kehadiran tercatat.
                                    </div>
                                </template>

                                <template x-for="(info, sesi) in selectedKeluarga.sesi" :key="sesi">
                                    <div class="bg-gray-50 dark:bg-[#181818] rounded-2xl p-4 border border-gray-100 dark:border-white/5">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-black text-gray-800 dark:text-white uppercase tracking-wider" x-text="sesi"></span>
                                            <span class="text-[10px] text-gray-400 font-bold" x-text="'Absen pertama: ' + info.jam_pertama"></span>
                                        </div>

                                        <template x-if="info.jamaah_hadir.length === 0">
                                            <p class="text-[10px] text-gray-400 italic">Tidak ada identitas anggota tercatat.</p>
                                        </template>

                                        <div class="space-y-2">
                                            <template x-for="jamaah in info.jamaah_hadir" :key="jamaah.nama + jamaah.jam">
                                                <div class="flex items-center justify-between bg-white dark:bg-[#1e1e1e] px-3 py-2 rounded-xl border border-gray-100 dark:border-white/5">
                                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-200" x-text="jamaah.nama"></span>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[10px] text-gray-400 font-bold" x-text="jamaah.jam"></span>
                                                        <span class="text-[9px] px-2 py-0.5 rounded-full font-bold uppercase"
                                                              :class="jamaah.status === 'Tepat Waktu' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : 'bg-amber-500/10 text-amber-600 dark:text-amber-400'"
                                                              x-text="jamaah.status"></span>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-6 flex justify-end border-t border-gray-100 dark:border-white/5 pt-4">
                                <button type="button" @click="openModalDetail = false" class="px-5 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs rounded-xl uppercase font-bold transition-all">Tutup</button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setAksiPdf(tipe) {
            const form = document.getElementById('formExportPdf');
            const inputAksi = document.getElementById('inputAksiPdf');
            if (tipe === 'download') { inputAksi.value = 'download'; form.removeAttribute('target'); }
            else { inputAksi.value = 'preview'; form.setAttribute('target', '_blank'); }
        }
    </script>
</x-app-layout>