<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warganet Dashboard</title>
</head>
<body>
@extends('layouts.app')

@section('content')
  
<main>
    <section class="space-y-4">
            @php
    use Carbon\Carbon;
    App::setLocale('id');
    $current = Carbon::create($year, $month, 1);
    $prev = $current->copy()->subMonth();
    $next = $current->copy()->addMonth();

    $startOfMonth = $current;
    $daysInMonth = $startOfMonth->daysInMonth;
    $firstDayOfWeek = $startOfMonth->dayOfWeek;

    $days = array_fill(0, $firstDayOfWeek, '');
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $days[] = $day;
    }

    $eventsByDate = [];
    foreach ($kalenderKegiatan as $event) {
        $date = Carbon::parse($event->tanggal)->format('Y-m-d');
        $eventsByDate[$date] = $event->judul;
    }
@endphp
        <div class="">
            <div class="flex justify-between items-center mb-8 gap-6">
                <div class="bg-blue-500 rounded-lg w-full relative flex justify-between items-center px-4 py-6">
                    @if ($pengumumanTerbaru)
                    <div class="text-white space-y-3">
                        <h1 class="font-bold w-[80%] text-2xl">
                            {{ $pengumumanTerbaru->judulPengumuman }}
                        </h1>
                        <p class="w-[80%]">{{ $pengumumanTerbaru->isiPengumuman }}</p>
                        <div class="bg-white w-32 text-blue-500 text-center rounded-3xl p-2">
                {{ Carbon::parse($pengumumanTerbaru->created_at)->translatedFormat('d F Y') }}
            </div>
                    </div>
                    @else  
                    <div class="text-white space-y-3">
                        <h1 class="font-bold w-[80%] text-2xl">Tidak ada pemberitahuan</h1>
                        <p class="w-[80%]">Ditunggu saja jika ada pemberitahuan terbaru dai pak rt.</p>
                        <div class="bg-white w-32 text-blue-500 text-center rounded-3xl p-2">
                            3 Maret 2025
                        </div>
                    </div>
                    @endif
                    <div class="w-40 absolute bottom-0 right-0">
                        <img src="{{ asset('images/toa.png') }}" alt="toa">
                    </div>
                </div>
                
            
            <div class="space-y-2">

<div class="flex justify-between items-center">
    <a href="{{ route('dashboard', ['year' => $prev->year, 'month' => $prev->month]) }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
    </a>
    <h1>{{ $current->translatedFormat('F, Y') }}</h1>
    <a href="{{ route('dashboard', ['year' => $next->year, 'month' => $next->month]) }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
    </a>
</div>

                {{-- Kalender --}}
                <div class="bg-white rounded-lg shadow-md p-4 w-80">
                    <div class="grid grid-cols-7 gap-2 text-gray-500 font-semibold text-[8px] mb-2">
                        <div>Minggu</div>
    <div>Senin</div>
    <div>Selasa</div>
    <div>Rabu</div>
    <div>Kamis</div>
    <div>Jumat</div>
    <div>Sabtu</div>
                    </div>
                    <div class="grid grid-cols-7 gap-2 text-center text-gray-700 text-sm">
                
                        @foreach ($days as $day)
    @php
        $dateString = $day ? Carbon::create($year, $month, $day)->format('Y-m-d') : null;
        $eventTitle = $dateString && isset($eventsByDate[$dateString]) ? $eventsByDate[$dateString] : null;
        $isToday = $dateString && Carbon::parse($dateString)->isToday(); // <-- Tambahkan ini
        $colors = ['bg-sky-100', 'bg-amber-100', 'bg-rose-100', 'bg-teal-100', 'bg-indigo-100'];
    @endphp

   @if ($day == '')
        <div></div>
    @elseif ($eventTitle)
        @php $randomColor = $colors[array_rand($colors)]; @endphp
        <div class="{{ $randomColor }} {{ $isToday ? 'ring-2 ring-blue-500' : 'py-1' }} rounded-md py-1  font-bold">
            {{ $day }}
        </div>
    @else
        <div class="{{ $isToday ? 'ring-2 ring-blue-500 rounded-md font-semibold' : '' }} py-1">
            {{ $day }}
        </div>
    @endif
@endforeach

                    </div>
                </div>
            </div>
            </div>
        </div>

        <div class="space-y-3">
        <h1 class="font-bold text-xl">Data Warga</h1>
        <div class="grid grid-cols-3 place-items-start  gap-3">
            <div class="bg-white px-4 py-6 w-full rounded-xl gap-2 flex items-center justify-center">
                <div class="space-y-1 text-center">
                    <h1 class="text-4xl font-extrabold text-[#26BFA3]">{{ $totalLakiLaki }}</h1>
                    <p class="font-bold text-xl">Laki-Laki</p>
                </div>
                    <img src="icon/man.svg" alt="man" class="w-10">
            </div>

            <div class="bg-white px-4 py-6 w-full rounded-xl gap-2 flex items-center justify-center">
                <div class="space-y-1 text-center">
                    <h1 class="text-4xl font-extrabold text-[#F27EA9]">{{$totalPerempuan}}</h1>
                    <p class="font-bold text-xl">Perempuan</p>
                </div>
                    <img src="icon/woman.svg" alt="woman" class="w-10">
            </div>

            <div class="bg-white px-4 py-6 w-full rounded-xl gap-2 flex items-center justify-center">
                <div class="space-y-1 text-center">
                    <h1 class="text-4xl font-extrabold text-[#26BFA3]">{{ $totalWarga  }}</h1>
                    <p class="font-bold text-xl">Warga</p>
                </div>
                <div class="flex items-center gap-3">
                <img src="icon/woman.svg" alt="woman" class="w-10">
                    <img src="icon/man.svg" alt="man" class="w-10">
                </div>
            </div>


            <div class="bg-white px-4 py-6 w-full col-span-3 rounded-xl gap-2 flex items-center justify-center">
                    <div class="space-y-1 text-center">
                        <h1 class="text-5xl font-extrabold text-[#26BFA3]">{{ $jumlahKK }}</h1>
                        <p class="font-bold text-2xl">Kartu Keluarga</p>
                    </div>
                        <img src="icon/totalKeluarga.svg" alt="family" class="w-20">
                </div>
        </div>
        </div>

        <div class="space-y-3">
        <h1 class="font-bold text-xl">Keuangan RT</h1>
        <div class="flex justify-between gap-3 items-center">
            <div class="bg-white px-4 py-6 w-full col-span-3 rounded-xl gap-2 flex flex-col items-center justify-center">
                <h1 class="text-blue-500 font-extrabold text-4xl">Rp {{ number_format($totalIuran, 0, ',', '.') }}</h1>
                <p class="font-bold">Total Iuran Terkumpul</p>
            </div>
            <div class="bg-white px-4 py-6 w-full col-span-3 rounded-xl gap-2 flex flex-col items-center justify-center">
                <h1 class="text-red-500 font-extrabold text-4xl">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h1>
                <p class="font-bold">Total Pengeluaran</p>
            </div>
        </div>
        </div>
    </section>
</main>

@endsection
</body>
</html>