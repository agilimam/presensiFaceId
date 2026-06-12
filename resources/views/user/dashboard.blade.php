<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white leading-tight">
                    {{ __('Dashboard Keluarga') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 transition-colors duration-300">
                    Manajemen data dan anggota keluarga jamaah Masjid Al-Iman
                </p>
            </div>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="py-8" x-data="{ 
        openTambah: false, 
        openEdit: false,
        editData: { id: '', nama: '', hubungan: '' },
        
        confirmDelete(id, nama) {
            Swal.fire({
                title: 'Hapus Anggota?',
                text: 'Data ' + nama + ' akan dihapus permanen dari sistem!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                customClass: {
                    popup: 'rounded-[2.5rem] border border-white/10 shadow-2xl',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold uppercase text-xs tracking-widest',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold uppercase text-xs tracking-widest'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-stretch">

                {{-- CARD KIRI (SUDAH DIPERBAIKI) --}}
                <div class="lg:col-span-2 flex">
                    <div class="bg-white dark:bg-[#212121] rounded-[2.5rem] p-8 border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden relative group transition-colors w-full h-full">

                        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-emerald-500/5 rounded-full blur-3xl transition-colors duration-500"></div>

                        <div class="mb-8 flex items-center justify-between bg-emerald-50 dark:bg-emerald-500/5 p-5 rounded-3xl border border-emerald-100 dark:border-emerald-500/10 relative z-10 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/20">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest leading-none mb-1">
                                        Waktu Sholat Aktif
                                    </p>
                                    <h4 class="text-xl font-black text-gray-900 dark:text-white uppercase leading-tight">
                                        {{ $sholatAktif['nama'] }}
                                    </h4>
                                    <div class="flex items-center gap-1.5 mt-1.5">
                                        <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-[9px] font-bold text-gray-500 dark:text-emerald-500/60 uppercase tracking-tight transition-colors">
                                            Tepat Waktu: <span class="text-emerald-600 dark:text-emerald-400">{{ $sholatAktif['range'] }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1 transition-colors">
                                    Status
                                </p>
                                <span class="px-3 py-1 rounded-lg text-xs font-black uppercase 
                                    {{ $sholatAktif['status'] == 'Terlambat' ? 'bg-rose-100 text-rose-600 shadow-sm shadow-rose-500/10' : 'bg-emerald-100 text-emerald-600 shadow-sm shadow-emerald-500/10' }}">
                                    {{ $sholatAktif['status'] }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 transition-colors">
                                    Kepala Keluarga
                                </p>
                                <p class="text-xl font-black text-gray-900 dark:text-white leading-tight uppercase truncate transition-colors">
                                    {{ $kepalaKeluarga ? $kepalaKeluarga->nama_anggota : 'Belum Diatur' }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- CARD RIWAYAT --}}
                <div class="flex">
                    <div class="bg-white dark:bg-[#212121] rounded-[2.5rem] p-8 border border-gray-200 dark:border-white/5 shadow-sm overflow-hidden transition-colors w-full h-full flex flex-col">

                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6 transition-colors">
                            Riwayat Terakhir
                        </h3>

                        {{-- SCROLL AREA --}}
                        <div class="space-y-6 overflow-y-auto pr-2 flex-1 max-h-[320px] custom-scrollbar">

                            @forelse($riwayatSingkat as $log)
                                <div class="flex items-start gap-4">
                                    <div class="w-6 h-6 rounded-full 
                                        {{ $log->status == 'Terlambat' ? 'bg-rose-500' : 'bg-emerald-500' }}
                                        flex-shrink-0 shadow-lg">
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-xs font-black text-gray-900 dark:text-white leading-none truncate mb-1 transition-colors">
                                            {{ $log->anggotaKeluarga->nama_anggota ?? 'Jamaah' }}
                                        </p>

                                        <p class="text-[10px] text-gray-500 font-bold uppercase transition-colors">
                                            {{ $log->keterangan_sholat }} •
                                            {{ \Carbon\Carbon::parse($log->waktu_absen)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic transition-colors">
                                    Belum ada absen.
                                </p>
                            @endforelse

                        </div>

                    </div>
                </div>

            </div>

            {{-- TABLE --}}
            <div class="bg-white dark:bg-[#212121] rounded-[2.5rem] border border-gray-200 dark:border-white/5 overflow-hidden shadow-sm transition-colors">

                <div class="p-8 border-b border-gray-100 dark:border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30 dark:bg-transparent transition-colors">

                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tighter transition-colors">
                            Daftar Anggota Keluarga
                        </h3>
                    </div>

                    <button @click="openTambah = true"
                        class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-2xl transition-all shadow-lg flex items-center gap-2">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>

                        TAMBAH ANGGOTA
                    </button>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full text-left">

                        <thead>
                            <tr class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest border-b border-gray-100 dark:border-white/5 transition-colors">
                                <th class="px-8 py-4">Nama Lengkap</th>
                                <th class="py-4">Hubungan</th>
                                <th class="py-4 text-center">Biometrik</th>
                                <th class="px-8 py-4 text-right transition-colors">Kelola</th>
                            </tr>
                        </thead>

                        <tbody class="text-sm">

                            @forelse($anggota as $item)
                                <tr class="border-b border-gray-50 dark:border-white/5 last:border-0 hover:bg-gray-50 dark:hover:bg-white/5 transition-all">

                                    <td class="px-8 py-6 font-bold text-gray-900 dark:text-white uppercase truncate transition-colors">
                                        {{ $item->nama_anggota }}
                                    </td>

                                    <td class="py-6 text-gray-600 dark:text-gray-400 font-medium capitalize transition-colors">
                                        {{ $item->hubungan }}
                                    </td>

                                   <td class="py-6 text-center">
                                    @if($item->status_wajah == 'VERIFIED')
                                        <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-full text-[10px] font-black uppercase transition-colors">
                                            ✅ Aktif
                                        </span>
                                    @elseif($item->status_wajah == 'PENDING')
                                        <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 rounded-full text-[10px] font-black uppercase transition-colors">
                                            ⌛ Diproses
                                        </span>
                                    @elseif($item->status_wajah == 'DUPLICATE')
                                        <span class="px-3 py-1 bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 rounded-full text-[10px] font-black uppercase transition-colors">
                                            ⚠️ Duplikat
                                        </span>
                                    @elseif($item->status_wajah == 'GAGAL')
                                        <span class="px-3 py-1 bg-orange-100 dark:bg-orange-500/10 text-orange-700 dark:text-orange-400 rounded-full text-[10px] font-black uppercase transition-colors">
                                            ❌ Gagal
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 rounded-full text-[10px] font-black uppercase transition-colors">
                                            — Menunggu
                                        </span>
                                    @endif
                                </td>

                                    <td class="px-8 py-6 text-right transition-colors">

                                        <div class="flex items-center justify-end gap-2">

                                            {{-- EDIT --}}
                                            <button
                                                @click="editData = { id: '{{ $item->id_anggota_keluarga }}', nama: '{{ $item->nama_anggota }}', hubungan: '{{ $item->hubungan }}' }; openEdit = true"
                                                class="p-2 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 rounded-xl hover:bg-blue-600 hover:text-white transition-all">

                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>

                                            </button>

                                            {{-- DELETE --}}
                                            <form id="delete-form-{{ $item->id_anggota_keluarga }}"
                                                action="{{ route('user.anggota.destroy', $item->id_anggota_keluarga) }}"
                                                method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="button"
                                                    @click="confirmDelete('{{ $item->id_anggota_keluarga }}', '{{ $item->nama_anggota }}')"
                                                    class="p-2 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-600 hover:text-white transition-all">

                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>

                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-8 py-12 text-center text-gray-400 italic transition-colors">
                                        Belum ada anggota.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>
            </div>
        </div>

        {{-- CUSTOM SCROLLBAR --}}
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(16, 185, 129, 0.4);
                border-radius: 999px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(16, 185, 129, 0.7);
            }
        </style>

        {{-- MODAL TAMBAH --}}
        <div x-show="openTambah" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">

            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="openTambah = false"></div>

            <div class="bg-white dark:bg-[#1e1e1e] rounded-[3rem] p-10 w-full max-w-md relative z-[110] border border-white/10 shadow-2xl transition-colors">

                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 uppercase tracking-tighter transition-colors">
                    Tambah Anggota
                </h3>

                <form action="{{ route('user.anggota.store') }}" method="POST" class="space-y-5">

                    @csrf

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 transition-colors">
                            Nama Lengkap
                        </label>

                        <input type="text" name="nama_anggota" required
                            class="w-full mt-2 bg-gray-50 dark:bg-black/20 border-none rounded-2xl py-4 px-6 text-gray-800 dark:text-white focus:ring-emerald-500 transition-all shadow-inner">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 transition-colors">
                            Hubungan
                        </label>

                        <select name="hubungan" required
                            class="w-full mt-2 bg-gray-50 dark:bg-black/20 border-none rounded-2xl py-4 px-6 text-gray-800 dark:text-white transition-all shadow-inner">

                            <option value="Kepala Keluarga">Kepala Keluarga</option>
                            <option value="Ibu">Ibu</option>
                            <option value="Anak">Anak</option>

                        </select>
                    </div>

                    <div class="flex gap-4 pt-4">

                        <button type="button" @click="openTambah = false"
                            class="flex-1 py-4 text-gray-400 font-bold text-xs uppercase tracking-widest transition-colors">
                            Batal
                        </button>

                        <button type="submit"
                            class="flex-[2] py-4 bg-emerald-600 text-white font-bold rounded-2xl text-xs shadow-lg hover:bg-emerald-500 transition-all transition-colors">
                            SIMPAN DATA
                        </button>

                    </div>

                </form>
            </div>
        </div>

        {{-- MODAL EDIT --}}
        <div x-show="openEdit" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4">

            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="openEdit = false"></div>

            <div class="bg-white dark:bg-[#1e1e1e] rounded-[3rem] p-10 w-full max-w-md relative z-[110] border border-white/10 shadow-2xl transition-colors">

                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 uppercase tracking-tighter transition-colors">
                    Edit Anggota
                </h3>

                <form :action="'{{ url('user/anggota') }}/' + editData.id" method="POST"
                    class="space-y-5">

                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 transition-colors">
                            Nama Lengkap
                        </label>

                        <input type="text" name="nama_anggota" x-model="editData.nama" required
                            class="w-full mt-2 bg-gray-50 dark:bg-black/20 border-none rounded-2xl py-4 px-6 text-gray-800 dark:text-white focus:ring-emerald-500 transition-all shadow-inner">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2 transition-colors">
                            Hubungan
                        </label>

                        <select name="hubungan" x-model="editData.hubungan" required
                            class="w-full mt-2 bg-gray-50 dark:bg-black/20 border-none rounded-2xl py-4 px-6 text-gray-800 dark:text-white transition-all shadow-inner">

                            <option value="Kepala Keluarga">Kepala Keluarga</option>
                            <option value="Ibu">Ibu</option>
                            <option value="Anak">Anak</option>

                        </select>
                    </div>

                    <div class="flex gap-4 pt-4">

                        <button type="button" @click="openEdit = false"
                            class="flex-1 py-4 text-gray-400 font-bold text-xs uppercase tracking-widest transition-colors">
                            Batal
                        </button>

                        <button type="submit"
                            class="flex-[2] py-4 bg-blue-600 text-white font-bold rounded-2xl text-xs shadow-lg hover:bg-blue-500 transition-all transition-colors">
                            UPDATE DATA
                        </button>

                    </div>

                </form>
            </div>
        </div>

    </div>

    {{-- NOTIFIKASI BERHASIL --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#059669',
                    confirmButtonText: 'OKE',
                    background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    customClass: {
                        popup: 'rounded-[2.5rem] border border-white/10 shadow-2xl',
                        confirmButton: 'rounded-xl px-10 py-3 font-bold uppercase text-xs tracking-widest shadow-lg shadow-emerald-500/20'
                    }
                });
            });
        </script>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'error',
                    title: 'PERHATIAN!',
                    html: "{!! session('error') !!}",
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OKE',
                    background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    customClass: {
                        popup: 'rounded-[2.5rem] border border-white/10 shadow-2xl',
                        confirmButton: 'rounded-xl px-10 py-3 font-bold uppercase text-xs tracking-widest shadow-lg shadow-rose-500/20'
                    }
                });
            });
        </script>
    @endif

</x-app-layout>