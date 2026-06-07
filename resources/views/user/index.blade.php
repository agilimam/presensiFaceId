<x-app-layout>
    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#212121] overflow-hidden shadow-sm sm:rounded-[3rem] border border-gray-200 dark:border-white/5 p-8 text-center transition-all duration-300">
                
                <div class="mb-6">
                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-500/20 rounded-[1.5rem] flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/10">
                        <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter">SCAN WAJAH</h3>
                    <p class="text-emerald-600 dark:text-emerald-500 font-bold mt-1 uppercase tracking-widest text-[10px]">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                <div class="relative inline-block border-[12px] border-gray-50 dark:border-white/5 rounded-[3rem] overflow-hidden shadow-2xl bg-black group">
                    <div id="my_camera" class="w-[400px] h-[300px]"></div>
                    <div class="absolute inset-0 border-[40px] border-black/30 pointer-events-none"></div>
                    <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.8)] animate-scan-line pointer-events-none"></div>
                    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-44 h-60 border-2 border-dashed border-emerald-500/50 rounded-[40%] animate-pulse">
                        <div class="absolute -top-2 -left-2 w-4 h-4 border-t-4 border-l-4 border-emerald-500 rounded-tl-lg"></div>
                        <div class="absolute -top-2 -right-2 w-4 h-4 border-t-4 border-r-4 border-emerald-500 rounded-tr-lg"></div>
                        <div class="absolute -bottom-2 -left-2 w-4 h-4 border-b-4 border-l-4 border-emerald-500 rounded-bl-lg"></div>
                        <div class="absolute -bottom-2 -right-2 w-4 h-4 border-b-4 border-r-4 border-emerald-500 rounded-br-lg"></div>
                    </div>
                </div>

                <div class="mt-10 space-y-4">
                    <button onclick="take_snapshot()" id="btn-capture" 
                            class="w-full py-4 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl shadow-xl shadow-emerald-500/20 transition-all transform active:scale-95 flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        ABSEN SEKARANG
                    </button>
                    <a href="{{ route('user.dashboard') }}" class="block text-xs font-bold text-gray-400 hover:text-emerald-600 uppercase tracking-widest transition-colors">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes scan-line { 0% { top: 0%; opacity: 0; } 50% { opacity: 1; } 100% { top: 100%; opacity: 0; } }
        .animate-scan-line { animation: scan-line 3s linear infinite; }
    </style>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Webcam.set({ width: 400, height: 300, image_format: 'jpeg', jpeg_quality: 90, flip_horiz: true, constraints: { facingMode: 'user' } });
        Webcam.attach('#my_camera');

        function take_snapshot() {
            const btn = document.getElementById('btn-capture');
            Webcam.snap(function(data_uri) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'cursor-not-allowed');
                btn.innerHTML = `MEMPROSES WAJAH...`;

                fetch("{{ route('user.presensi.scan') }}", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}", "Content-Type": "application/json" },
                    body: JSON.stringify({ image: data_uri })
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({ 
                            icon: 'success', title: 'Berhasil', text: data.message, timer: 3000, showConfirmButton: false,
                            background: document.documentElement.classList.contains('dark') ? '#212121' : '#fff',
                            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                        }).then(() => window.location.href = "{{ route('user.dashboard') }}");
                    } else {
                        // INI AKAN MUNCUL JIKA WAJAH TIDAK COCOK (ID 0)
                        Swal.fire({ 
                            icon: 'error', title: 'Gagal', text: data.message, confirmButtonColor: '#059669',
                            background: document.documentElement.classList.contains('dark') ? '#212121' : '#fff',
                            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
                        });
                        btn.disabled = false;
                        btn.classList.remove('opacity-70', 'cursor-not-allowed');
                        btn.innerHTML = 'ABSEN SEKARANG';
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    btn.classList.remove('opacity-70', 'cursor-not-allowed');
                    btn.innerHTML = 'ABSEN SEKARANG';
                });
            });
        }
    </script>
    @endpush
</x-app-layout>