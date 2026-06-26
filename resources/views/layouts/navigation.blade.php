<nav class="h-full flex flex-col justify-between bg-white dark:bg-[#1e1e1e] text-gray-900 dark:text-white transition-colors duration-300">
    <div>
        <div class="p-8 flex items-center gap-4 border-b border-gray-100 dark:border-white/5">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-[#064e3b] rounded-2xl flex items-center justify-center border border-emerald-100 dark:border-white/10 shadow-sm transition-colors">
                <x-application-logo class="w-7 h-7 text-emerald-600 dark:text-emerald-400 fill-current" />
            </div>
            <div>
                <h1 class="font-bold text-lg leading-none text-gray-900 dark:text-white">Al-Iman</h1>
                <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1">Presensi Masjid</p>
            </div>
        </div>

        <div class="p-4 space-y-2">
            <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4 px-4 opacity-50">Menu Utama</p>
            
            <a href="{{ route('dashboard') }}" 
               class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-emerald-600 dark:hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                <span>Dashboard</span>
            </a>

            @if(Auth::user()->role === 'keluarga')
            <a href="{{ route('user.presensi.index') }}" 
               class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('user.presensi.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-emerald-600 dark:hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                <span>Absensi</span>
            </a>
            @endif

            @if(Auth::user()->role === 'admin')
            <a href="{{ route('admin.presensi.index') }}" 
               class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.presensi.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-emerald-600 dark:hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                <span>Log Presensi</span>
            </a>
            <a href="{{ route('admin.keluarga.index') }}" 
               class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.keluarga.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-emerald-600 dark:hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                <span>Data Keluarga</span>
            </a>
            <a href="{{ route('admin.akun.index') }}" 
               class="flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-300 {{ request()->routeIs('admin.akun.*') ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 hover:text-emerald-600 dark:hover:text-white' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                <span>Manajemen Akun</span>
            </a>

            
            @endif
        </div>
    </div>

    <div class="p-6 border-t border-gray-100 dark:border-white/5 bg-gray-50 dark:bg-black/20 transition-colors">
        <button @click="darkMode = !darkMode" class="w-full flex items-center justify-between px-4 py-2.5 mb-4 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl hover:border-emerald-500 transition-all group">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mode Tema</span>
            <div class="text-gray-400 group-hover:text-emerald-500">
                <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 000 14 7 7 0 000-14z" /></svg>
                <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
            </div>
        </button>

        <div class="flex items-center gap-3 mb-6 px-2 text-gray-900 dark:text-white">
            <div class="w-10 h-10 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-600 font-bold border border-emerald-100 dark:border-emerald-500/20">
                {{ substr(Auth::user()->username, 0, 1) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-bold truncate">{{ Auth::user()->username }}</p>
                <p class="text-[10px] text-gray-500 uppercase tracking-tighter">{{ Auth::user()->role }}</p>
            </div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        <button type="button" onclick="Swal.fire({
            title: 'Yakin ingin keluar?',
            text: 'Sesi anda akan berakhir.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Keluar!',
            background: document.documentElement.classList.contains('dark') ? '#1e1e1e' : '#fff',
            color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
            customClass: { popup: 'rounded-[2rem]' }
        }).then((result) => { if (result.isConfirmed) document.getElementById('logout-form').submit(); })" 
        class="w-full flex items-center justify-center gap-2 bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 py-3 rounded-xl text-xs font-bold text-gray-500 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            LOG OUT
        </button>
    </div>
</nav>