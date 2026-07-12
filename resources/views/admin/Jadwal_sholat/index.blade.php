<x-app-layout>
    <x-slot name="header">
        MANAJEMEN JADWAL SHOLAT
    </x-slot>

    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="mb-8">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                Master Data Jadwal Sholat
            </h3>
            <p class="text-sm text-gray-500 mt-1">
               Admin hanya dapat mengubah jam pelaksanaan.
            </p>
        </div>

        {{-- Success --}}
        @if(session('success'))

            <div
                class="mb-5 p-4 rounded-xl bg-green-100 text-green-700 border border-green-300">

                {{ session('success') }}

            </div>

        @endif

        {{-- Card --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            @forelse($jadwal as $item)

            <div
                class="bg-white dark:bg-[#1e1e1e] rounded-3xl p-6 border border-gray-100 dark:border-white/5 shadow-xs flex flex-col justify-between">

                <div>

                    <span
                        class="text-2xl font-extrabold uppercase tracking-wide text-emerald-600">

                        {{ $item->nama_sholat }}

                    </span>

                    <div class="mt-5 space-y-4">

                        <div>

                            <p
                                class="text-[10px] uppercase tracking-wider text-gray-400">

                                Jam Mulai

                            </p>

                            <h4
                                class="text-2xl font-bold text-gray-800 dark:text-white">

                                {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}

                            </h4>

                        </div>

                        <div>

                            <p
                                class="text-[10px] uppercase tracking-wider text-gray-400">

                                Batas Tepat Waktu

                            </p>

                            <h4
                                class="text-xl font-bold text-amber-500">

                                {{ \Carbon\Carbon::parse($item->batas_tepat_waktu)->format('H:i') }}

                            </h4>

                        </div>

                    </div>

                </div>

                <div class="flex justify-end gap-2 mt-6">

                    <button
                        onclick="openModal('modalEdit{{ $item->id_jadwal }}')"
                        class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold">

                        Edit

                    </button>

                </div>

            </div>

            @empty

            <div
                class="col-span-3 bg-white dark:bg-[#1e1e1e] rounded-3xl p-10 text-center text-gray-500">

                Belum ada jadwal sholat. Jalankan seeder terlebih dahulu.

            </div>

            @endforelse

        </div>

    </div>

    @foreach($jadwal as $item)

    <div id="modalEdit{{ $item->id_jadwal }}" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm">

        <div class="bg-white dark:bg-[#1e1e1e] w-full max-w-lg rounded-3xl shadow-2xl border border-gray-100 dark:border-white/5 p-8 relative scale-95 opacity-0 transition-all duration-200">

            <button
                onclick="forceCloseModal('modalEdit{{ $item->id_jadwal }}')"
                class="absolute top-5 right-6 text-2xl text-gray-400 hover:text-gray-700">

                &times;

            </button>

            <h3 class="text-lg font-bold uppercase tracking-wide text-gray-800 dark:text-white mb-6">

                Edit Jadwal {{ $item->nama_sholat }}

            </h3>

            <form
                method="POST"
                action="{{ route('admin.jadwal.update',$item->id_jadwal) }}">

                @csrf
                @method('PUT')

                <div class="space-y-5">

                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">

                            Jam Mulai

                        </label>

                        <input
                            type="time"
                            name="jam_mulai"
                            value="{{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }}"
                            class="w-full rounded-xl border-0 bg-gray-100 dark:bg-[#121212] px-4 py-3">

                    </div>

                    <div>

                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">

                            Batas Tepat Waktu

                        </label>

                        <input
                            type="time"
                            name="batas_tepat_waktu"
                            value="{{ \Carbon\Carbon::parse($item->batas_tepat_waktu)->format('H:i') }}"
                            class="w-full rounded-xl border-0 bg-gray-100 dark:bg-[#121212] px-4 py-3">

                    </div>

                </div>

                <div class="mt-8 flex justify-end gap-3">

                    <button
                        type="button"
                        onclick="forceCloseModal('modalEdit{{ $item->id_jadwal }}')"
                        class="px-5 py-2 rounded-xl bg-gray-500 hover:bg-gray-600 text-white">

                        Batal

                    </button>

                    <button
                        type="submit"
                        class="px-6 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold">

                        Update

                    </button>

                </div>

            </form>

        </div>

    </div>

    @endforeach

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

                modal.firstElementChild.classList.remove('scale-95','opacity-0');
                modal.firstElementChild.classList.add('scale-100','opacity-100');

            },20);
        }
        function forceCloseModal(id){

            const modal=document.getElementById(id);

            modal.firstElementChild.classList.remove('scale-100','opacity-100');

            modal.firstElementChild.classList.add('scale-95','opacity-0');

            setTimeout(()=>{

                modal.classList.remove('flex');
                modal.classList.add('hidden');

            },150);

        }
    </script>

</x-app-layout>