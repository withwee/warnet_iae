@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <!-- Banner Pengumuman Khusus -->
    @if ($pengumumanKhusus)
    <div class="bg-blue-500 rounded-2xl w-full relative flex justify-between items-center px-6 py-6">
        <div class="text-white space-y-3">
            <h1 class="font-bold text-2xl md:text-[22px] w-full md:w-[36rem] leading-snug">
                {{ $pengumumanKhusus->judulPengumuman }}
            </h1>
            <p class="w-full md:w-[90%] text-sm md:text-base">
                {{ $pengumumanKhusus->isiPengumuman }}
            </p>
            <div class="bg-white w-fit px-8 py-1.5 text-blue-500 text-sm md:text-base text-center rounded-3xl font-semibold">
                {{ $pengumumanKhusus->created_at->format('d M Y') }}
            </div>
        </div>
        <div class="w-48 absolute bottom-0 right-0">
            <img src="{{ asset('images/toa.png') }}" alt="toa">
        </div>
    </div>
    @endif

    <!-- List Pengumuman -->
    <div class="space-y-4">
        <h1 class="font-bold text-lg">Pengumuman Hari Ini</h1>

        @foreach ($pengumumans as $pengumuman)
        <div class="bg-white p-5 rounded-2xl space-y-3 shadow-sm">
            <h1 class="text-xl font-bold">{{ $pengumuman->judulPengumuman }}</h1>
            <p class="text-sm leading-relaxed w-full md:w-[95%]">
                {{ $pengumuman->isiPengumuman }}
            </p>
            <div class="bg-blue-500 px-6 py-1.5 rounded-3xl text-white text-sm font-semibold w-fit">
                {{ $pengumuman->created_at->format('d M Y') }}
            </div>
        </div>
        @endforeach

    </div>

</div>
@endsection
