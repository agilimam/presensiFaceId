<x-app-layout>
    <style>
        #my_camera {
            width: 450px !important;
            height: 340px !important;
            margin: 0 auto;
            position: relative;
            background: black;
            border-radius: 3rem;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #my_camera video, #my_camera canvas {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            border-radius: 3rem;
            display: block;
        }

        @keyframes scan-line {
            0% { top: 0%; opacity: 0; }
            50% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        .animate-scan-line { animation: scan-line 3s linear infinite; }
        [x-cloak] { display: none !important; }

        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(16, 185, 129, 0.2); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(16, 185, 129, 0.5); }
    </style>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white leading-tight uppercase tracking-tighter">Manajemen Registrasi Wajah</h2>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Data Biometrik Keluarga</p>
            </div>
        </div>
    </x-slot>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>

    <div class="py-4" x-data="{
            modalOpen: false,
            modalEditKeluarga: false,
            namaAnggota: '',
            targetFormId: '',
            previewDepan: null,
            editKeluargaData: { id: '', nama: '' },

            openEditKeluarga(id, nama) {
                this.editKeluargaData = { id: id, nama: nama };
                this.modalEditKeluarga = true;
            },

            confirmDeleteKeluarga(id, nama) {
                Swal.fire({
                    title: 'Hapus Keluarga?',
                    text: 'Keluarga ' + nama + ' beserta seluruh anggotanya akan dihapus permanen!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal',
                    background: document.documentElement.classList.contains('dark') ? '#212121' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    customClass: { popup: 'rounded-[2rem]' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-keluarga-form-' + id).submit();
                    }
                });
            },

            confirmResetFace(id, nama) {
                Swal.fire({
                    title: 'Reset Wajah?',
                    text: 'Data biometrik ' + nama + ' akan dihapus. Anda harus melakukan scan ulang nanti.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Reset!',
                    cancelButtonText: 'Batal',
                    background: document.documentElement.classList.contains('dark') ? '#212121' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    customClass: { popup: 'rounded-[2.5rem] border border-white/10 shadow-2xl' }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('reset-face-form-' + id).submit();
                    }
                });
            },

            startCamera(nama, formId) {
                window.kameraAktif = true;
                this.namaAnggota = nama;
                this.targetFormId = formId;
                this.previewDepan = null;
                this.modalOpen = true;

                Webcam.reset();
                setTimeout(() => {
                    Webcam.set({
                        width: 450, height: 340,
                        image_format: 'jpeg', jpeg_quality: 90, flip_horiz: true,
                        constraints: { facingMode: 'user' }
                    });
                    Webcam.attach('#my_camera');
                }, 500);
            },

            stopCamera() {
                Webcam.reset();
                this.modalOpen = false;
            },

            take_snapshot_js() {
                Webcam.snap((data_uri) => {
                    let id = this.targetFormId.split('-').pop();
                    this.previewDepan = data_uri;
                    document.getElementById('input-front-' + id).value = data_uri;
                    document.getElementById(this.targetFormId).submit();
                });
            }
        }">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($daftarKeluarga as $keluarga)
                    <div class="bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-white/5 rounded-[1.5rem] p-5 shadow-sm flex flex-col justify-between">
                        <div>
                            {{-- Header Kartu Keluarga --}}
                            <div class="mb-4 flex items-center justify-between gap-3 border-b border-gray-100 dark:border-white/5 pb-2">
                                <div class="min-w-0 flex-1">
                                    <p class="text-[7px] text-emerald-600 uppercase tracking-widest font-black">Keluarga</p>
                                    <h4 class="text-xs font-bold text-gray-800 dark:text-white uppercase truncate">{{ $keluarga->nama_keluarga }}</h4>
                                </div>
                                <div class="flex gap-1.5 shrink-0">
                                    {{-- Tombol Edit Nama Keluarga --}}
                                    <button @click="openEditKeluarga('{{ $keluarga->id_keluarga }}', '{{ $keluarga->nama_keluarga }}')"
                                            class="p-2 text-blue-500 bg-blue-50 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    {{-- Tombol Hapus Keluarga --}}
                                    <form id="delete-keluarga-form-{{ $keluarga->id_keluarga }}"
                                          action="{{ route('admin.keluarga.destroy', $keluarga->id_keluarga) }}"
                                          method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button"
                                                @click="confirmDeleteKeluarga('{{ $keluarga->id_keluarga }}', '{{ $keluarga->nama_keluarga }}')"
                                                class="p-2 text-red-500 bg-red-50 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Daftar Anggota --}}
                            <div class="space-y-2 max-h-[240px] overflow-y-auto pr-1 custom-scrollbar">
                                @foreach($keluarga->anggotaKeluarga as $person)
                                    @php
                                        $status   = $person->status_wajah;
                                        // Tombol kamera aktif HANYA jika belum punya data ATAU perlu scan ulang
                                        $bisaScan = in_array($status, [null, 'DUPLICATE', 'GAGAL']);
                                    @endphp

                                    <div class="flex items-center gap-2 p-2.5 rounded-xl bg-gray-50 dark:bg-black/20 border border-gray-100 dark:border-white/5">

                                        {{-- Avatar inisial --}}
                                        <div class="w-8 h-8 {{ $person->face_id ? 'bg-emerald-500' : 'bg-gray-200' }} rounded-lg flex items-center justify-center font-black text-xs text-white shrink-0">
                                            {{ strtoupper(substr($person->nama_anggota, 0, 1)) }}
                                        </div>

                                        {{-- Nama & Status --}}
                                        <div class="flex-1 min-w-0">
                                            <h5 class="text-[11px] font-bold text-gray-800 dark:text-gray-200 truncate uppercase leading-none">
                                                {{ $person->nama_anggota }}
                                            </h5>

                                            @if($status == 'PENDING')
                                                <p class="text-[8px] text-yellow-500 font-black uppercase mt-0.5">⌛ PROSES...</p>
                                            @elseif($status == 'DUPLICATE')
                                                <p class="text-[8px] text-red-500 font-black uppercase mt-0.5">⚠️ DUPLIKAT!</p>
                                            @elseif($status == 'VERIFIED')
                                                <p class="text-[8px] text-emerald-500 font-black uppercase mt-0.5">✅ VERIFIED</p>
                                            @elseif($status == 'GAGAL')
                                                <p class="text-[8px] text-orange-400 font-black uppercase mt-0.5">❌ GAGAL</p>
                                            @else
                                                <p class="text-[8px] text-gray-400 font-black uppercase mt-0.5">BELUM SCAN</p>
                                            @endif
                                        </div>

                                        {{-- Tombol Aksi --}}
                                        <div class="flex items-center gap-1 shrink-0">

                                            {{-- ✅ FORM REGISTRASI WAJAH (DIPERBAIKI) --}}
                                            <form id="form-face-{{ $person->id_anggota_keluarga }}"
                                                  action="{{ route('admin.register.update', $person->id_anggota_keluarga) }}"
                                                  method="POST">
                                                @csrf
                                                <input type="hidden"
                                                       name="image_face"
                                                       id="input-front-{{ $person->id_anggota_keluarga }}">

                                                <button type="button"
                                                    {{-- @click HANYA dipasang jika bisaScan = true --}}
                                                    @if($bisaScan)
                                                        @click="startCamera('{{ $person->nama_anggota }}', 'form-face-{{ $person->id_anggota_keluarga }}')"
                                                    @endif
                                                    {{-- Styling konsisten dengan $bisaScan --}}
                                                    class="p-1.5 rounded-lg transition-all shadow-md text-white
                                                        {{ $bisaScan
                                                            ? 'bg-emerald-600 hover:bg-emerald-500 active:scale-90 cursor-pointer'
                                                            : 'bg-gray-400 cursor-not-allowed opacity-50' }}"
                                                    {{ !$bisaScan ? 'disabled' : '' }}
                                                    title="{{ $bisaScan ? 'Klik untuk scan wajah' : ($status == 'VERIFIED' ? 'Sudah terverifikasi' : 'Sedang diproses') }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0118.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                </button>
                                            </form>

                                            {{-- Tombol RESET — tampil jika ada data biometrik --}}
                                            @if(in_array($status, ['VERIFIED', 'DUPLICATE', 'GAGAL']))
                                                <form id="reset-face-form-{{ $person->id_anggota_keluarga }}"
                                                      action="{{ route('admin.register.destroyFace', $person->id_anggota_keluarga) }}"
                                                      method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="button"
                                                            @click="confirmResetFace('{{ $person->id_anggota_keluarga }}', '{{ $person->nama_anggota }}')"
                                                            class="p-1.5 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm"
                                                            title="Reset data wajah">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-gray-400">Belum ada data keluarga.</div>
                @endforelse
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             MODAL EDIT NAMA KELUARGA
        ═══════════════════════════════════════════════════════ --}}
        <div x-show="modalEditKeluarga" x-cloak
             class="fixed inset-0 z-[120] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="modalEditKeluarga = false"></div>
            <div class="bg-white dark:bg-[#1e1e1e] rounded-[2rem] p-8 w-full max-w-sm relative z-[130] border border-white/10 shadow-2xl">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2 uppercase tracking-tighter">Edit Nama Keluarga</h3>
                <form :action="'{{ url('admin/keluarga') }}/' + editKeluargaData.id" method="POST" class="space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-2">Nama Keluarga</label>
                        <input type="text" name="nama_keluarga" x-model="editKeluargaData.nama" required
                               class="w-full mt-2 bg-gray-50 dark:bg-black/20 border-none rounded-2xl py-4 px-6 text-gray-800 dark:text-white focus:ring-emerald-500 transition-all shadow-inner">
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="modalEditKeluarga = false"
                                class="flex-1 py-4 text-gray-400 font-bold text-xs uppercase tracking-widest">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-[2] py-4 bg-emerald-600 text-white font-bold rounded-2xl text-xs uppercase tracking-widest shadow-lg">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════
             MODAL SCAN BIOMETRIK
        ═══════════════════════════════════════════════════════ --}}
        <div x-show="modalOpen" x-cloak
             class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="stopCamera()"></div>
            <div class="bg-[#212121] rounded-[3.5rem] shadow-2xl w-full max-w-4xl relative z-[110] p-8 md:p-10 border border-white/5 overflow-hidden">

                {{-- Header --}}
                <div class="mb-8 w-full text-center">
                    <h3 class="text-2xl font-black text-white uppercase tracking-tighter">REGISTRASI BIOMETRIK WAJAH</h3>
                    <p class="text-emerald-500 font-bold uppercase text-[10px] tracking-widest mt-1" x-text="namaAnggota"></p>
                </div>

                {{-- Grid Kamera + Preview --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">

                    {{-- Kamera Live --}}
                    <div class="flex flex-col items-center justify-between bg-black/20 p-4 rounded-[2.5rem] border border-white/5">
                        <div class="relative w-full aspect-[4/3] bg-black rounded-[2rem] overflow-hidden shadow-inner flex items-center justify-center">
                            <div id="my_camera" class="!w-full !h-full"></div>
                            <div class="absolute inset-0 border-[15px] border-black/10 pointer-events-none rounded-[2rem]"></div>
                            <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.8)] animate-scan-line pointer-events-none"></div>
                        </div>
                        <div class="mt-4">
                            <span class="bg-emerald-600/10 text-emerald-400 border border-emerald-500/20 px-5 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm">
                                📷 KAMERA AKTIF
                            </span>
                        </div>
                    </div>

                    {{-- Pratinjau --}}
                    <div class="flex flex-col items-center justify-between bg-black/20 p-4 rounded-[2.5rem] border border-white/5">
                        <div class="relative w-full aspect-[4/3] bg-[#1a1a1a] rounded-[2rem] overflow-hidden border border-white/5 flex items-center justify-center shadow-inner">
                            <template x-if="previewDepan">
                                <img :src="previewDepan" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previewDepan">
                                <div class="text-center p-6 flex flex-col items-center gap-2">
                                    <svg class="w-8 h-8 text-gray-700 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-[9px] text-gray-600 font-bold uppercase tracking-widest">Belum Ada Gambar</span>
                                </div>
                            </template>
                        </div>
                        <div class="mt-4">
                            <span class="bg-blue-600/10 text-blue-400 border border-blue-500/20 px-5 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest shadow-sm">
                                🖼️ PRATINJAU MASTER
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Petunjuk & Tombol --}}
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6 items-center pt-6 border-t border-white/5">
                    <div class="md:col-span-2 bg-emerald-500/5 border border-emerald-500/10 p-4 rounded-2xl">
                        <h4 class="text-[9px] font-black text-emerald-400 uppercase mb-1 tracking-widest">📌 PETUNJUK REGISTER</h4>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-tight leading-normal">
                            Posisikan wajah tepat di tengah area kamera, pastikan cahaya cukup menerangi wajah, lalu tekan tombol ambil foto untuk menyimpan.
                        </p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button type="button" @click="take_snapshot_js()"
                                class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-xl uppercase tracking-widest text-[10px] shadow-lg shadow-emerald-900/20 active:scale-[0.97] transition-all">
                            Ambil Foto & Simpan
                        </button>
                        <button type="button" @click="stopCamera()"
                                class="text-[9px] font-black text-gray-500 hover:text-red-400 uppercase tracking-widest transition-colors py-1">
                            Batalkan
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- NOTIFIKASI SESSION --}}
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'success',
                    title: 'BERHASIL!',
                    text: "{{ session('success') }}",
                    confirmButtonColor: '#059669',
                    confirmButtonText: 'OKE',
                    background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    customClass: { popup: 'rounded-[2.5rem]', confirmButton: 'rounded-xl px-10 py-3 font-bold uppercase text-xs' }
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'GAGAL!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#ef4444',
                    confirmButtonText: 'OKE',
                    background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                    customClass: { popup: 'rounded-[2.5rem]', confirmButton: 'rounded-xl px-10 py-3 font-bold uppercase text-xs' }
                });
            });
        </script>
    @endif
    
</x-app-layout>