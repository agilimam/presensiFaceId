<x-app-layout>
    <!-- PERBAIKAN: tinggi kontainer tidak lagi ditebak pakai angka px
         tetap (64px kemarin salah, jadi judul kepotong). Sekarang
         dihitung otomatis via JS berdasarkan sisa ruang layar yang
         benar-benar tersedia di bawah header/topbar. -->
    <div id="presensi-page" class="flex flex-col items-center px-6 py-3 text-center overflow-hidden">

        <div class="mb-2 shrink-0">
            <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tighter">SCAN WAJAH</h3>
            <p class="text-emerald-600 dark:text-emerald-500 font-bold mt-1 uppercase tracking-widest text-[10px]">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>

        <div class="flex-1 min-h-0 w-full flex items-center justify-center">
            <div id="camera-wrapper" class="relative h-full max-w-full rounded-[2rem] overflow-hidden bg-black">
                <div id="my_camera"></div>
                <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500/50 shadow-[0_0_15px_rgba(16,185,129,0.8)] animate-scan-line pointer-events-none"></div>
            </div>
        </div>

        <div class="mt-3 space-y-2 w-full max-w-[480px] shrink-0">
            <button onclick="take_snapshot()" id="btn-capture" 
                    class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-2xl shadow-xl shadow-emerald-500/20 transition-all transform active:scale-95 flex items-center justify-center gap-3">
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

    <style>
        html, body{
            overflow: hidden;
        }

       #camera-wrapper{
        width: 100%;
        max-width: 600px;   /* sebelumnya 450px */
        aspect-ratio: 4 / 3;
        border-radius: 2rem;
        overflow: hidden;
        background: #000;
    }

        #my_camera{
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        #my_camera,
        #my_camera video,
        #my_camera canvas{
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
        }

        @keyframes scan-line { 0% { top: 0%; opacity: 0; } 50% { opacity: 1; } 100% { top: 100%; opacity: 0; } }
        .animate-scan-line { animation: scan-line 3s linear infinite; }
    </style>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.25/webcam.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // PERBAIKAN UTAMA: hitung tinggi kontainer secara dinamis
    // berdasarkan posisi aktualnya di halaman, bukan angka tebakan.
    function fitPresensiPageHeight() {
        const page = document.getElementById('presensi-page');
        if (!page) return;
        const top = page.getBoundingClientRect().top;
        const bottomMargin = 16; // sedikit jarak aman di bawah
        const available = window.innerHeight - top - bottomMargin;
        page.style.height = Math.max(available, 300) + 'px';
    }

    window.addEventListener('resize', fitPresensiPageHeight);
    window.addEventListener('load', fitPresensiPageHeight);
    // Panggil juga segera (sebelum load penuh) supaya tidak sempat
    // kelihatan salah ukuran / kepotong sesaat
    fitPresensiPageHeight();
    setTimeout(fitPresensiPageHeight, 100);
    setTimeout(fitPresensiPageHeight, 500);

    function forceFillCamera() {
        const el = document.querySelector('#my_camera');
        if (!el) return;
        el.querySelectorAll('video, canvas').forEach(function (node) {
            node.removeAttribute('width');
            node.removeAttribute('height');
            node.style.width = '100%';
            node.style.height = '100%';
        });
    }

    function initCamera() {
        try { Webcam.reset(); } catch (e) {}

        Webcam.set({
            width: 640,
            height: 480,
            image_format: 'jpeg',
            jpeg_quality: 90,
            flip_horiz: true,
            constraints: {
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: "user"
            }
        });

        Webcam.attach('#my_camera');
        setTimeout(forceFillCamera, 300);
        setTimeout(fitPresensiPageHeight, 350);
    }

    initCamera();

    document.addEventListener('livewire:navigated', initCamera);
    window.addEventListener('beforeunload', function () {
        try { Webcam.reset(); } catch (e) {}
    });

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