<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white leading-tight uppercase tracking-tighter">
                    Log Presensi Mesjid Al-Iman
                </h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase font-semibold tracking-wider">
                    {{ !$sesiSholatPilihan ? 'Monitoring Rekap Sholat Seluruh Keluarga' : 'Detail Kehadiran Sesi ' . $sesiSholatPilihan }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="{ openModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- TOMBOL FILTER SESI SHOLAT --}}
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('admin.presensi.index', request()->except('sholat')) }}" 
                       class="px-4 py-2 rounded-xl border transition-all text-xs {{ !$sesiSholatPilihan ? 'bg-emerald-600 border-emerald-600 text-white font-bold' : 'bg-white dark:bg-[#262626] border-gray-200 dark:border-white/5 text-gray-600 dark:text-gray-400' }}">
                       Semua Sesi
                    </a>
                    @foreach(['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'] as $s)
                        <a href="{{ route('admin.presensi.index', array_merge(request()->query(), ['sholat' => $s])) }}" 
                           class="px-4 py-2 rounded-xl border transition-all text-xs {{ $sesiSholatPilihan == $s ? 'bg-emerald-600 border-emerald-600 text-white font-bold' : 'bg-white dark:bg-[#262626] border-gray-200 dark:border-white/5 text-gray-600 dark:text-gray-400 hover:border-emerald-500' }}">
                            {{ $s }}
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
                
                <button @click="openModal = true" class="w-full md:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 rounded-xl text-white font-bold transition-all shadow-md shadow-emerald-500/10 text-xs uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    Export Laporan Resmi
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
                        {{ count($rekapKeluarga) }} Grup Keluarga Pantauan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-transparent">
                                <th class="px-6 py-4">Grup / Kepala Keluarga</th>
                                @if(!$sesiSholatPilihan)
                                    <th class="py-4 text-center">Subuh</th>
                                    <th class="py-4 text-center">Dzuhur</th>
                                    <th class="py-4 text-center">Ashar</th>
                                    <th class="py-4 text-center">Maghrib</th>
                                    <th class="py-4 text-center">Isya</th>
                                @else
                                    <th class="py-4 text-center">Jadwal Sholat</th>
                                    <th class="py-4 text-center">Absen Jam Pertama</th>
                                    <th class="py-4 text-left px-8">Anggota Keluarga yang Hadir</th>
                                    <th class="py-4 text-center">Status Keluarga</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-50 dark:divide-white/5">
                            @forelse($rekapKeluarga as $idKeluarga => $data)
                                @if($sesiSholatPilihan && !$data[$sesiSholatPilihan])
                                    @continue
                                @endif

                            <tr class="hover:bg-gray-50/60 dark:hover:bg-white/5 transition-all">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 bg-gradient-to-br from-emerald-500 to-teal-600 text-white rounded-xl flex items-center justify-center font-black text-xs shadow-md shadow-emerald-500/10">
                                            {{ strtoupper(substr($data['kepala_keluarga'], 0, 1)) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-bold text-gray-900 dark:text-white uppercase tracking-tight">{{ $data['kepala_keluarga'] }}</span>
                                            <span class="text-[9px] text-emerald-500 font-bold uppercase tracking-widest">Grup: {{ $data['nama_keluarga'] }}</span>
                                        </div>
                                    </div>
                                </td>

                                @if(!$sesiSholatPilihan)
                                    @foreach(['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'] as $sesi)
                                    <td class="py-4 text-center">
                                        @if($data[$sesi])
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="px-2.5 py-0.5 rounded-md text-[9px] font-black uppercase tracking-tight
                                                    {{ $data[$sesi]['status_utama'] == 'Tepat Waktu' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20' }}">
                                                    {{ $data[$sesi]['status_utama'] }}
                                                </span>
                                                
                                                <div class="flex flex-col gap-0.5 max-w-[125px] mx-auto">
                                                    @foreach($data[$sesi]['jamaah_hadir'] as $jamaah)
                                                        <span class="text-[8px] font-bold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-white/5 px-1.5 py-0.5 rounded whitespace-nowrap" 
                                                              title="Absen pukul {{ $jamaah['jam'] }} WIB ({{ $jamaah['status'] }})">
                                                            👤 {{ explode(' ', $jamaah['nama'])[0] }} ({{ $jamaah['jam'] }})
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-[10px] font-bold text-gray-300 dark:text-gray-700 uppercase tracking-widest select-none">❌ Alpa</span>
                                        @endif
                                    </td>
                                    @endforeach
                                @else
                                    @php $detail = $data[$sesiSholatPilihan]; @endphp
                                    <td class="py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30">
                                            {{ $sesiSholatPilihan }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center font-bold text-gray-800 dark:text-white">
                                        {{ $detail['jam_pertama'] }} WIB
                                    </td>
                                    <td class="py-4 px-8 text-left">
                                        <div class="flex flex-wrap gap-1.5 max-w-xl">
                                            @foreach($detail['jamaah_hadir'] as $index => $jamaah)
                                                <div class="flex items-center gap-1.5 bg-gray-50 dark:bg-white/5 px-2.5 py-1 rounded-lg border border-gray-100 dark:border-white/5 shadow-sm">
                                                    <span class="text-[10px] font-bold text-gray-700 dark:text-gray-300">
                                                        {{ $index + 1 }}. {{ $jamaah['nama'] }}
                                                    </span>
                                                    <span class="text-[9px] text-emerald-500 font-mono font-bold">
                                                        [{{ $jamaah['jam'] }}]
                                                    </span>
                                                    <span class="text-[8px] uppercase font-extrabold px-1 rounded {{ $jamaah['status'] == 'Tepat Waktu' ? 'text-emerald-500 bg-emerald-500/10' : 'text-rose-500 bg-rose-500/10' }}">
                                                        {{ $jamaah['status'] == 'Tepat Waktu' ? 'TW' : 'TL' }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-tight
                                            {{ $detail['status_utama'] == 'Tepat Waktu' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20' }}">
                                            {{ $detail['status_utama'] }}
                                        </span>
                                    </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center text-gray-400 italic text-xs uppercase tracking-widest font-bold">Tidak ada catatan aktivitas keluarga pada filter ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

       {{-- MODAL FILTER PDF (SATU INPUT TANGGAL SEJAJAR) --}}
<div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/60 dark:bg-black/80" @click="openModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white dark:bg-[#1f1f1f] rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full p-8 border border-gray-100 dark:border-white/5">
            <div class="mb-5">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight">Cetak PDF</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pilih rentang tanggal laporan.</p>
            </div>

            <form id="formExportPdf" action="{{ route('admin.presensi.export') }}" method="GET" @submit="openModal = false" target="_blank">
                <input type="hidden" name="aksi" value="preview">
                
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

                <div class="mt-6 flex justify-end gap-2 border-t border-gray-100 dark:border-white/5 pt-4">
                    <button type="button" @click="openModal = false" class="px-5 py-2.5 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-xs rounded-xl uppercase font-bold transition-all">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs rounded-xl uppercase font-bold transition-all shadow-lg shadow-emerald-500/20">Preview</button>
                </div>
            </form>
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