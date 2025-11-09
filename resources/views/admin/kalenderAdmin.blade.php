@extends('layouts.app')

@section('title', 'Kalender')

@section('content')

<div class="max-w-screen space-y-8">

    @if (session('success'))
        <div class="bg-green-100 text-green-800 border border-green-300 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold text-gray-800 mb-4">Buat Kegiatan</h2>

    <div class="bg-white p-6 rounded-xl shadow space-y-6">
        <form method="POST" action="{{ route('kegiatan.store') }}" class="space-y-4" id="formKegiatan">
            @csrf

            <div>
                <label for="judulKegiatan" class="block text-base font-semibold mb-1 text-gray-700">Judul Kegiatan</label>
                <input type="text" name="judulKegiatan" id="judulKegiatan" minlength="3" required
                       placeholder="Ketik judul kegiatan ..."
                       class="w-full border border-gray-300 rounded-full px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 placeholder-gray-400">
            </div>

            <div>
                <label for="deskripsiKegiatan" class="block text-base font-semibold mb-1 text-gray-700">Deskripsi Kegiatan</label>
                <textarea name="deskripsiKegiatan" id="deskripsiKegiatan" minlength="3" required
                          placeholder="Ketik deskripsi kegiatan ..."
                          class="w-full border border-gray-300 rounded-2xl px-5 py-3 h-40 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 placeholder-gray-400"></textarea>
            </div>

            <div>
                <label for="tanggalKegiatan" class="block text-base font-semibold mb-1 text-gray-700">Tanggal Kegiatan</label>
                <input type="date" name="tanggalKegiatan" id="tanggalKegiatan" required
                       class="w-full border border-gray-300 rounded-full px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800 placeholder-gray-400">
                <p id="tanggalError" class="text-red-600 text-sm mt-1 hidden">Tanggal kegiatan tidak boleh sebelum hari ini.</p>
            </div>

            <div class="text-center">
                <button type="submit" id="btnPublikasikan"
                        class="bg-gray-400 text-white font-semibold px-12 py-3 rounded-full transition duration-300 cursor-not-allowed"
                        disabled>
                    Publikasikan
                </button>
            </div>
        </form>
    </div>

    @php
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();
        $startDayOfWeek = $startOfMonth->dayOfWeekIso;
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayInCalendar = $startOfMonth->copy()->subDays($startDayOfWeek - 1);
        $cellsToDisplay = 42;
        $lastDayInCalendar = $firstDayInCalendar->copy()->addDays($cellsToDisplay - 1);
    @endphp

    <section aria-labelledby="kalender-kegiatan-section-title">
        <h2 id="kalender-kegiatan-section-title" class="text-2xl font-bold text-gray-800 mb-6">Kalender Kegiatan</h2>
        <div class="bg-white p-6 rounded-xl shadow space-y-6">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <p class="font-bold text-xl md:text-2xl text-blue-600 order-2 sm:order-1">{{ $currentDate->translatedFormat('F, Y') }}</p>
                <div class="flex items-center space-x-2 order-1 sm:order-2 self-end sm:self-center">
                    <a href="{{ route('kalender', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                       title="Bulan Sebelumnya"
                       class="text-gray-600 hover:text-blue-600 p-1.5 rounded-md hover:bg-slate-100 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path fill="currentColor" d="M15.41 7.41L14 6l-6 6l6 6l1.41-1.41L10.83 12z"/></svg>
                    </a>
                    <a href="{{ route('kalender', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                       title="Bulan Berikutnya"
                       class="text-gray-600 hover:text-blue-600 p-1.5 rounded-md hover:bg-slate-100 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"><path fill="currentColor" d="M8.59 16.59L10 18l6-6l-6-6l-1.41 1.41L13.17 12z"/></svg>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-7 border-t border-l border-slate-200">
                @foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $day)
                    <div class="p-2.5 border-b border-r border-slate-200 text-center text-xs font-semibold text-gray-500">{{ $day }}</div>
                @endforeach

                @for ($date = $firstDayInCalendar->copy(); $date <= $lastDayInCalendar; $date->addDay())
                    @php
                        $tanggal = $date->toDateString();
                        $kegiatanHariIni = $kalendars->where('tanggal', $tanggal);
                        $isCurrentMonth = $date->month === $currentDate->month;
                        $isToday = $date->isToday();

                        $cellBgColor = '';
                        $dayNumberTextColor = 'text-gray-700';
                        $eventDetailsTextColor = 'text-gray-700';

                        if ($isCurrentMonth && $kegiatanHariIni->isNotEmpty()) {
                            $colors = [
                                ['bg' => 'bg-sky-100', 'day_text' => 'text-sky-800', 'event_text' => 'text-sky-700'],
                                ['bg' => 'bg-amber-100', 'day_text' => 'text-amber-800', 'event_text' => 'text-amber-700'],
                                ['bg' => 'bg-rose-100', 'day_text' => 'text-rose-800', 'event_text' => 'text-rose-700'],
                                ['bg' => 'bg-teal-100', 'day_text' => 'text-teal-800', 'event_text' => 'text-teal-700'],
                                ['bg' => 'bg-indigo-100', 'day_text' => 'text-indigo-800', 'event_text' => 'text-indigo-700']
                            ];
                            $selectedColor = $colors[$kegiatanHariIni->first()->id % count($colors)];
                            $cellBgColor = $selectedColor['bg'];
                            $dayNumberTextColor = $selectedColor['day_text'];
                            $eventDetailsTextColor = $selectedColor['event_text'];
                        }

                        $finalCellClasses = 'border-b border-r border-slate-200 p-2 h-28 sm:h-32 text-left overflow-y-auto relative';
                        if (!$isCurrentMonth) {
                            $finalCellClasses .= ' bg-slate-50';
                            $dayNumberTextColor = 'text-gray-400';
                        } elseif (!empty($cellBgColor)) {
                            $finalCellClasses .= ' ' . $cellBgColor;
                        }
                    @endphp
                    <div class="{{ $finalCellClasses }}">
                        <div class="text-sm font-semibold {{ $dayNumberTextColor }}">{{ $date->day }}</div>
                        @if($isCurrentMonth)
                            @foreach($kegiatanHariIni as $event)
                                <div class="kegiatan-item text-xs mt-1 font-medium cursor-pointer hover:underline {{ $eventDetailsTextColor }}"
                                     data-judul="{{ $event->judul }}"
                                     data-deskripsi="{{ $event->deskripsi }}"
                                     data-tanggal="{{ \Carbon\Carbon::parse($event->tanggal)->translatedFormat('l, d F Y') }}">
                                    • {{ Str::limit($event->judul, 20) }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </section>
</div>

<div id="kegiatanModal" class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex justify-center items-center p-4 hidden z-50">
    <div id="modalContent" class="bg-white w-full max-w-2xl rounded-xl shadow-lg transform transition-all duration-300 scale-95 opacity-0">
        <div class="p-6 space-y-4">
            <h3 id="modalJudul" class="text-2xl font-bold text-gray-800">Judul Kegiatan</h3>
            <p id="modalTanggal" class="text-sm font-semibold text-gray-500">Tanggal</p>
            <div class="border-t border-gray-200 pt-4">
                <p id="modalDeskripsi" class="text-gray-700 whitespace-pre-wrap">Deskripsi lengkap kegiatan.</p>
            </div>
        </div>
        <div class="bg-gray-50 px-6 py-3 text-right rounded-b-xl">
            <button id="closeModalBtn" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-6 py-2 rounded-full transition duration-300">Tutup</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formKegiatan');
    const judulInput = document.getElementById('judulKegiatan');
    const isiInput = document.getElementById('deskripsiKegiatan');
    const tanggalInput = document.getElementById('tanggalKegiatan');
    const tanggalError = document.getElementById('tanggalError');
    const btnPublikasikan = document.getElementById('btnPublikasikan');

    function isValidDate() {
        if (!tanggalInput.value) return false;
        const selected = new Date(tanggalInput.value);
        const today = new Date();
        selected.setHours(0, 0, 0, 0);
        today.setHours(0, 0, 0, 0);
        return selected >= today;
    }

    function validateForm() {
        const isJudulValid = judulInput.value.trim().length >= 3;
        const isIsiValid = isiInput.value.trim().length >= 3;
        const isTanggalValid = isValidDate();

        if (tanggalInput.value && !isTanggalValid) {
            tanggalError.classList.remove('hidden');
        } else {
            tanggalError.classList.add('hidden');
        }

        const allValid = isJudulValid && isIsiValid && isTanggalValid;

        btnPublikasikan.disabled = !allValid;
        
        if (allValid) {
            btnPublikasikan.classList.remove('bg-gray-400', 'cursor-not-allowed');
            btnPublikasikan.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
        } else {
            btnPublikasikan.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            btnPublikasikan.classList.add('bg-gray-400', 'cursor-not-allowed');
        }
    }

    judulInput.addEventListener('input', validateForm);
    isiInput.addEventListener('input', validateForm);
    tanggalInput.addEventListener('change', validateForm);
    tanggalInput.addEventListener('input', validateForm);
    
    // Initial validation
    validateForm();
});
</script>


<!-- // Detail Acara // -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('kegiatanModal');
    const modalContent = document.getElementById('modalContent');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const modalJudul = document.getElementById('modalJudul');
    const modalTanggal = document.getElementById('modalTanggal');
    const modalDeskripsi = document.getElementById('modalDeskripsi');

    // Fungsi untuk membuka modal
    function openModal() {
        modal.classList.remove('hidden');
        // Efek transisi saat modal muncul
        setTimeout(() => {
            modalContent.classList.remove('scale-95', 'opacity-0');
            modalContent.classList.add('scale-100', 'opacity-100');
        }, 50); // delay singkat untuk memastikan transisi berjalan
    }

    // Fungsi untuk menutup modal
    function closeModal() {
        modalContent.classList.add('scale-95', 'opacity-0');
        modalContent.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300); // Sesuaikan dengan durasi transisi
    }

    // Event listener untuk setiap item kegiatan
    document.querySelectorAll('.kegiatan-item').forEach(item => {
        item.addEventListener('click', (e) => {
            // Mengambil data dari atribut data-*
            const judul = e.currentTarget.dataset.judul;
            const tanggal = e.currentTarget.dataset.tanggal;
            const deskripsi = e.currentTarget.dataset.deskripsi;

            // Memasukkan data ke dalam modal
            modalJudul.textContent = judul;
            modalTanggal.textContent = tanggal;
            modalDeskripsi.textContent = deskripsi;

            openModal();
        });
    });

    // Event listener untuk tombol tutup
    closeModalBtn.addEventListener('click', closeModal);

    // Event listener untuk menutup modal saat mengklik di luar konten modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Menutup modal dengan tombol Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>

@endsection
