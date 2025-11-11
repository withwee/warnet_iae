@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
            <!-- Announcement -->
            @if($pengumumanTerbaru)
            <div class="bg-blue-500 rounded-2xl w-full relative flex justify-between items-center px-6 py-6 overflow-hidden">
                <div class="text-white space-y-3 z-10">
                    <h1 class="font-bold text-2xl md:text-[22px] w-full md:w-[36rem] leading-snug">
                        {{ $pengumumanTerbaru->judulPengumuman }}
                    </h1>
                    <p class="w-full md:w-[90%] text-sm md:text-base">
                        {{ $pengumumanTerbaru->isiPengumuman }}
                    </p>
                    <div class="bg-white w-fit px-8 py-1.5 text-blue-500 text-sm md:text-base text-center rounded-3xl font-semibold">
                        {{ $pengumumanTerbaru->created_at->format('d M Y') }}
                    </div>
                </div>
                <div class="w-48 absolute bottom-0 right-0">
                    <img src="http://127.0.0.1:8000/images/toa.png" alt="toa">
                </div>
            </div>
            @else
            <div class="bg-blue-500 rounded-2xl w-full relative flex justify-between items-center px-6 py-6 overflow-hidden">
                <div class="text-white space-y-3 z-10">
                    <h1 class="font-bold text-2xl md:text-[22px] w-full md:w-[36rem] leading-snug">
                        Tidak ada pemberitahuan
                    </h1>
                    <p class="w-full md:w-[90%] text-sm md:text-base">
                        Ditunggu saja jika ada pemberitahuan terbaru dari pak rt.
                    </p>
                    <div class="bg-white w-fit px-8 py-1.5 text-blue-500 text-sm md:text-base text-center rounded-3xl font-semibold">
                        {{ now()->format('d M Y') }}
                    </div>
                </div>
                <div class="w-48 absolute bottom-0 right-0">
                    <img src="http://127.0.0.1:8000/images/toa.png" alt="toa">
                </div>
            </div>
            @endif

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <div>
                    <!-- Data Warga -->
                    <h2 class="section-title">Data Warga</h2>
                    <div class="data-grid">
                        <div class="data-card">
                            <div class="data-number" style="color: #10b981;">{{ $totalLakiLaki }}</div>
                            <div class="data-label">Laki-Laki</div>
                            <svg class="data-icon mx-auto" fill="#10b981" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="data-card">
                            <div class="data-number" style="color: #ec4899;">{{ $totalPerempuan }}</div>
                            <div class="data-label">Perempuan</div>
                            <svg class="data-icon mx-auto" fill="#ec4899" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="data-card">
                            <div class="data-number" style="color: #3b82f6;">{{ $totalWarga }}</div>
                            <div class="data-label">Warga</div>
                            <svg class="data-icon mx-auto" fill="#3b82f6" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                        <div class="data-card">
                            <div class="data-number" style="color: #8b5cf6;">{{ $jumlahKK }}</div>
                            <div class="data-label">Kartu Keluarga</div>
                            <svg class="data-icon mx-auto" fill="#8b5cf6" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Keuangan RT -->
                    <h2 class="section-title">Keuangan RT</h2>
                    <div class="keuangan-grid">
                        <div class="keuangan-card">
                            <div class="keuangan-amount" style="color: #3b82f6;">Rp {{ number_format($totalIuran, 0, ',', '.') }}</div>
                            <div class="keuangan-label">Total Iuran Terkumpul</div>
                        </div>
                        <div class="keuangan-card">
                            <div class="keuangan-amount" style="color: #ef4444;">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                            <div class="keuangan-label">Total Pengeluaran</div>
                        </div>
                    </div>
                </div>

                <!-- Calendar Widget -->
                <div>
                    <div class="calendar-widget">
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <h3 style="font-weight: 700; color: #1f2937;">{{ now()->format('F, Y') }}</h3>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 0.5rem; text-align: center;">
                            <div style="font-weight: 600; color: #6b7280; font-size: 0.75rem;">Min</div>
                            <div style="font-weight: 600; color: #6b7280; font-size: 0.75rem;">Sen</div>
                            <div style="font-weight: 600; color: #6b7280; font-size: 0.75rem;">Sel</div>
                            <div style="font-weight: 600; color: #6b7280; font-size: 0.75rem;">Rab</div>
                            <div style="font-weight: 600; color: #6b7280; font-size: 0.75rem;">Kam</div>
                            <div style="font-weight: 600; color: #6b7280; font-size: 0.75rem;">Jum</div>
                            <div style="font-weight: 600; color: #6b7280; font-size: 0.75rem;">Sab</div>
                            
                            @php
                                $startOfMonth = \Carbon\Carbon::now()->startOfMonth();
                                $endOfMonth = \Carbon\Carbon::now()->endOfMonth();
                                $startDay = $startOfMonth->dayOfWeek;
                                $daysInMonth = $endOfMonth->day;
                                $today = now()->day;
                            @endphp
                            
                            @for($i = 0; $i < $startDay; $i++)
                                <div></div>
                            @endfor
                            
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                <div style="padding: 0.5rem; border-radius: 0.375rem; font-size: 0.875rem; {{ $day == $today ? 'background: #3b82f6; color: white; font-weight: 700;' : 'color: #374151;' }}">
                                    {{ $day }}
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
</html>
@endsection