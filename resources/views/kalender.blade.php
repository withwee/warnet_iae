@extends('layouts.app')

@section('title', 'Kalender')

@section('content')

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

    {{-- Section: Tampilan Kalender Kegiatan --}}
    <section aria-labelledby="kalender-kegiatan-section-title">
        <h2 id="kalender-kegiatan-section-title" class="text-2xl font-bold text-gray-800 mb-6">Kalender Kegiatan</h2>
        <div class="bg-white p-6 rounded-xl shadow space-y-6">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                <p class="font-bold text-xl md:text-2xl text-blue-600 order-2 sm:order-1">{{ $currentDate->translatedFormat('F, Y') }}</p>
                <div class="flex items-center space-x-2 order-1 sm:order-2 self-end sm:self-center">
                    <a href="{{ route('kalender', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" title="Bulan Sebelumnya" class="text-gray-600 hover:text-blue-600 p-1.5 rounded-md hover:bg-slate-100 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M15.41 7.41L14 6l-6 6l6 6l1.41-1.41L10.83 12z"/></svg>
                    </a>
                    <a href="{{ route('kalender', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" title="Bulan Berikutnya" class="text-gray-600 hover:text-blue-600 p-1.5 rounded-md hover:bg-slate-100 transition-colors duration-150">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M8.59 16.59L10 18l6-6l-6-6l-1.41 1.41L13.17 12z"/></svg>
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

                        // Logika Pewarnaan
                        $cellBgColor = '';
                        $dayNumberTextColor = 'text-gray-700';
                        $eventDetailsTextColor = 'text-gray-700';

                        if ($isCurrentMonth && $kegiatanHariIni->isNotEmpty()) {
                            $hasHardcodedColor = false;
                            // Contoh hardcode dari Figma (bisa dihapus jika tidak perlu)
                            if ($date->month == 3 && $date->year == 2025) {
                                if (in_array($date->day, [1, 2])) {
                                    $cellBgColor = 'bg-pink-100'; $dayNumberTextColor = 'text-pink-800'; $eventDetailsTextColor = 'text-pink-700'; $hasHardcodedColor = true;
                                } elseif (in_array($date->day, [11, 12])) {
                                    $cellBgColor = 'bg-green-100'; $dayNumberTextColor = 'text-green-800'; $eventDetailsTextColor = 'text-green-700'; $hasHardcodedColor = true;
                                } elseif ($date->day == 21) {
                                    $cellBgColor = 'bg-purple-100'; $dayNumberTextColor = 'text-purple-800'; $eventDetailsTextColor = 'text-purple-700'; $hasHardcodedColor = true;
                                }
                            }

                            // Fallback pewarnaan jika tidak ada hardcode di atas
                            if (!$hasHardcodedColor) {
                                $colors = [
                                    ['bg' => 'bg-sky-100',    'day_text' => 'text-sky-800',    'event_text' => 'text-sky-700'],
                                    ['bg' => 'bg-amber-100',  'day_text' => 'text-amber-800',  'event_text' => 'text-amber-700'],
                                    ['bg' => 'bg-rose-100',   'day_text' => 'text-rose-800',   'event_text' => 'text-rose-700'],
                                    ['bg' => 'bg-teal-100',   'day_text' => 'text-teal-800',   'event_text' => 'text-teal-700'],
                                    ['bg' => 'bg-indigo-100', 'day_text' => 'text-indigo-800', 'event_text' => 'text-indigo-700']
                                ];
                                $selectedColor = $colors[$kegiatanHariIni->first()->id % count($colors)];
                                $cellBgColor = $selectedColor['bg'];
                                $dayNumberTextColor = $selectedColor['day_text'];
                                $eventDetailsTextColor = $selectedColor['event_text'];
                            }
                        }

                        // Penentuan kelas akhir untuk sel dan angka tanggal
                        $finalCellClasses = 'border-b border-r border-slate-200 p-2 h-28 sm:h-32 text-left overflow-y-auto relative';
                        if (!$isCurrentMonth) {
                            $finalCellClasses .= ' bg-slate-50';
                            $dayNumberTextColor = 'text-gray-400';
                        } elseif (!empty($cellBgColor)) {
                            $finalCellClasses .= ' ' . $cellBgColor;
                        }
                        $finalDayNumberColorClass = $isCurrentMonth ? ($isToday ? 'text-blue-600 font-bold' : $dayNumberTextColor) : 'text-gray-400';
                    @endphp

                    <div class="{{ $finalCellClasses }}">
                        <span class="font-semibold mb-1 block text-sm {{ $finalDayNumberColorClass }}">{{ $date->day }}</span>
                        @if ($kegiatanHariIni->isNotEmpty() && $isCurrentMonth)
                            <div class="mt-1 space-y-1">
                                @foreach ($kegiatanHariIni as $kegiatan)
                                    <div class="kegiatan-item cursor-pointer group"
                                         data-judul="{{ e($kegiatan->judul) }}"
                                         data-deskripsi="{{ e($kegiatan->deskripsi) }}"
                                         data-tanggal="{{ \Carbon\Carbon::parse($kegiatan->tanggal)->translatedFormat('l, d F Y') }}">
                                        <p class="text-base font-semibold {{ $eventDetailsTextColor }} leading-tight group-hover:opacity-75">{{ $kegiatan->judul }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endfor
            </div>
        </div>
    </section>

    <div id="kegiatanModal" class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm items-center justify-center p-4 hidden z-50">
    <div id="modalContent" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
        {{-- Header Modal --}}
        <div class="p-6 border-b border-gray-200">
            <h3 id="modalJudul" class="text-2xl font-bold text-gray-900">Judul Kegiatan</h3>
            <p id="modalTanggal" class="mt-1 text-sm text-gray-500">Tanggal Kegiatan</p>
        </div>
        {{-- Body Modal --}}
        <div class="p-6 max-h-60 overflow-y-auto">
            <p id="modalDeskripsi" class="text-base text-gray-600 leading-relaxed">Deskripsi lengkap kegiatan...</p>
        </div>
        {{-- Footer Modal --}}
        <div class="px-6 py-4 bg-gray-50 rounded-b-2xl text-right">
            <button id="closeModal" class="px-6 py-2 bg-blue-600 text-white text-base font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('kegiatanModal');
        const modalContent = document.getElementById('modalContent');
        const modalJudul = document.getElementById('modalJudul');
        const modalDeskripsi = document.getElementById('modalDeskripsi');
        const modalTanggal = document.getElementById('modalTanggal');
        const closeModalBtn = document.getElementById('closeModal');
        const kegiatanItems = document.querySelectorAll('.kegiatan-item');

        const openModal = (judul, deskripsi, tanggal) => {
            modalJudul.textContent = judul;
            modalDeskripsi.textContent = deskripsi || 'Tidak ada deskripsi.';
            modalTanggal.textContent = tanggal;
            modal.classList.remove('hidden');
            modal.classList.add('flex'); 
        };

        const closeModal = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex'); 
        };

        kegiatanItems.forEach(item => {
            item.addEventListener('click', function (event) {
                event.stopPropagation();
                const judul = this.dataset.judul;
                const deskripsi = this.dataset.deskripsi;
                const tanggal = this.dataset.tanggal;
                openModal(judul, deskripsi, tanggal);
            });
        });

        closeModalBtn.addEventListener('click', closeModal);

        modal.addEventListener('click', function (event) {
            if (!modalContent.contains(event.target)) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    });
</script>
@endsection