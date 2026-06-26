<x-app-layout>
    <x-slot name="header">
        MANAJEMEN AKUN KELUARGA
    </x-slot>

    <div class="max-w-7xl mx-auto">
        
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Data Kredensial Keluarga</h3>
            </div>

            <form method="GET" action="{{ route('admin.akun.index') }}" class="w-full md:w-96">
                <div class="flex shadow-xs rounded-xl overflow-hidden gap-2">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari keluarga atau anggota..." 
                        class="w-full px-4 py-2.5 text-sm bg-white dark:bg-[#1e1e1e] border border-gray-200 dark:border-white/10 rounded-xl focus:border-emerald-500 focus:ring-0 text-gray-700 dark:text-gray-200 shadow-none transition">
                    
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 rounded-xl flex items-center justify-center transition font-semibold text-sm">
                        Cari
                    </button>

                    @if($search)
                        <a href="{{ route('admin.akun.index') }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2.5 rounded-xl text-sm font-semibold flex items-center justify-center transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($daftarAkun as $akun)
                <div class="bg-white dark:bg-[#1e1e1e] p-6 rounded-3xl border border-gray-100 dark:border-white/5 shadow-xs flex flex-col justify-between min-h-[160px] relative">
                    
                    <div>
                        <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest block mb-1 truncate" title="{{ $akun->keluarga->nama_keluarga ?? 'Nama Belum Diisi' }}">
                            {{ $akun->keluarga->nama_keluarga ?? 'Nama Belum Diisi' }}
                        </span>
                        <h4 class="text-md font-bold text-gray-800 dark:text-white uppercase tracking-tight mb-2">
                            {{ $akun->username }}
                        </h4>
                        
                        <div class="inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-semibold bg-gray-50 dark:bg-[#121212] text-gray-500 dark:text-gray-400 border border-gray-100 dark:border-white/5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            {{ ucfirst($akun->role) }}
                        </div>
                    </div>

                    <div class="flex justify-end items-center space-x-2 mt-4">
                        <button type="button" 
                            onclick="confirmDelete('{{ $akun->username }}')"
                            class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition transform active:scale-95">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Akun
                        </button>

                        <button type="button" 
                            onclick="openModal('modalReset{{ $akun->username }}')"
                            class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-xs transition transform active:scale-95">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                            Ubah Password
                        </button>
                    </div>

                    <div id="modalReset{{ $akun->username }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-xs transition-opacity duration-200">
                        <div class="bg-white dark:bg-[#1e1e1e] w-full max-w-md rounded-3xl shadow-2xl border border-gray-100 dark:border-white/5 transform scale-95 opacity-0 transition-all duration-200 overflow-hidden p-8 relative">
                            
                            <button type="button" onclick="handleBatal('modalReset{{ $akun->username }}')" class="absolute top-5 right-6 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xl font-medium">&times;</button>

                            <h3 class="text-md font-bold text-gray-800 dark:text-white uppercase tracking-wide mb-6">
                                RESET PASSWORD AKUN
                            </h3>

                            <form id="formReset{{ $akun->username }}" method="POST" action="{{ route('admin.akun.update-password', $akun->username) }}" class="space-y-5" onsubmit="handleUpdate(event, '{{ $akun->username }}')">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Username Akun</label>
                                    <div class="w-full px-4 py-3 text-sm bg-gray-100 dark:bg-[#121212] rounded-xl text-gray-700 dark:text-gray-300 font-semibold select-none border border-gray-200/50 dark:border-white/5">
                                        {{ $akun->username }}
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Password Baru</label>
                                    <input type="password" id="pass_{{ $akun->username }}" name="password" placeholder="••••••••"
                                        class="w-full px-4 py-3 text-sm bg-gray-50 dark:bg-[#121212] border-0 rounded-xl focus:ring-2 focus:ring-emerald-500/20 text-gray-800 dark:text-white transition shadow-inner">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-2">Konfirmasi Password Baru</label>
                                    <input type="password" id="conf_{{ $akun->username }}" name="password_confirmation" placeholder="••••••••"
                                        class="w-full px-4 py-3 text-sm bg-gray-50 dark:bg-[#121212] border-0 rounded-xl focus:ring-2 focus:ring-emerald-500/20 text-gray-800 dark:text-white transition shadow-inner">
                                </div>

                                <div class="pt-4 flex items-center justify-end space-x-2 border-t border-gray-100 dark:border-white/5 mt-4">
                                    <button type="button" onclick="handleBatal('modalReset{{ $akun->username }}')"
                                        class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider hover:text-gray-600 transition px-4 py-2">
                                        Batal
                                    </button>
                                    
                                    <button type="submit" 
                                        class="px-6 py-3 text-xs font-bold uppercase tracking-wider bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-md transition-all text-center flex justify-center items-center">
                                        Update
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white dark:bg-[#1e1e1e] p-12 text-center text-gray-400 dark:text-gray-500 font-medium rounded-3xl border border-gray-100 dark:border-white/5">
                    Tidak ada data akun keluarga ditemukan.
                </div>
            @endforelse
        </div>

        {{-- PAGINATION LINKS --}}
        <div class="mt-8">
            {{ $daftarAkun->links() }}
        </div>

    </div>

    <form id="form-delete-akun" method="POST" action="" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function getSwalConfig() {
            return {
                background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
            };
        }

        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                modal.firstElementChild.classList.remove('scale-95', 'opacity-0');
                modal.firstElementChild.classList.add('scale-100', 'opacity-100');
            }, 20);
        }

        function forceCloseModal(id) {
            const modal = document.getElementById(id);
            modal.firstElementChild.classList.remove('scale-100', 'opacity-100');
            modal.firstElementChild.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                const form = modal.querySelector('form');
                if(form) form.reset();
            }, 150);
        }

        // POP-UP UNTUK BATAL
        function handleBatal(modalId) {
            Swal.fire({
                title: 'Batalkan perubahan?',
                text: "Isian password baru yang telah Anda ketik akan dibersihkan.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6b7280',
                cancelButtonColor: '#059669',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Kembali Edit',
                ...getSwalConfig()
            }).then((result) => {
                if (result.isConfirmed) {
                    forceCloseModal(modalId);
                }
            });
        }

        // POP-UP VALIDATION & SIMPAN UPDATE
        function handleUpdate(event, username) {
            event.preventDefault();

            const password = document.getElementById(`pass_${username}`).value;
            const confirmation = document.getElementById(`conf_${username}`).value;

            if (!password || !confirmation) {
                Swal.fire({
                    title: 'Validasi Gagal!',
                    text: 'Silakan isi kolom Password Baru dan Konfirmasi terlebih dahulu.',
                    icon: 'error',
                    confirmButtonColor: '#10b981',
                    ...getSwalConfig()
                });
                return;
            }

            if (password.length < 8) {
                Swal.fire({
                    title: 'Password Terlalu Pendek!',
                    text: 'Kata sandi baru minimal harus terdiri dari 8 karakter.',
                    icon: 'warning',
                    confirmButtonColor: '#10b981',
                    ...getSwalConfig()
                });
                return;
            }

            if (password !== confirmation) {
                Swal.fire({
                    title: 'Password Tidak Cocok!',
                    text: 'Kombinasi password baru dan konfirmasi tidak sesuai. Silakan periksa kembali.',
                    icon: 'error',
                    confirmButtonColor: '#10b981',
                    ...getSwalConfig()
                });
                return;
            }

            Swal.fire({
                title: 'Simpan Password Baru?',
                text: "Sistem akan segera memperbarui kredensial login untuk akun " + username,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Perbarui!',
                cancelButtonText: 'Batal',
                ...getSwalConfig()
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`formReset${username}`).submit();
                }
            });
        }

        // POP-UP UNTUK HAPUS AKUN (SUDAH DIPERBAIKI URL DI SINI)
        function confirmDelete(username) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Akun " + username + " beserta seluruh data keluarga & anggota di dalamnya akan dihapus permanen dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                ...getSwalConfig()
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('form-delete-akun');
                    
                    // PERBAIKAN UTAMA: Menggunakan URL dinamis Laravel agar mengarah ke route 'admin.akun.destroy' yang benar
                    form.action = `/admin/akun/${username}`;
                    
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>