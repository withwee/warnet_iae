@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
            <!-- Announcement -->
            @if($pengumumanTerbaru)
            <div class="announcement-box">
                <div class="announcement-content">
                    <h3>{{ $pengumumanTerbaru->judul ?? 'Tidak ada pemberitahuan' }}</h3>
                    <p>{{ $pengumumanTerbaru->isi ?? 'Ditunggu saja jika ada pemberitahuan terbaru dari pak rt.' }}</p>
                    <span class="announcement-date">{{ $pengumumanTerbaru->created_at ? $pengumumanTerbaru->created_at->format('d F Y') : '3 Maret 2025' }}</span>
                </div>
                <svg class="announcement-icon" viewBox="0 0 100 100" fill="none">
                    <circle cx="50" cy="50" r="45" fill="white" opacity="0.2"/>
                    <path d="M30 40L50 25L70 40V70L50 85L30 70V40Z" fill="white" opacity="0.3"/>
                    <circle cx="50" cy="50" r="15" fill="white"/>
                </svg>
            </div>
            @else
            <div class="announcement-box">
                <div class="announcement-content">
                    <h3>Tidak ada pemberitahuan</h3>
                    <p>Ditunggu saja jika ada pemberitahuan terbaru dari pak rt.</p>
                    <span class="announcement-date">{{ now()->format('d F Y') }}</span>
                </div>
                <svg class="announcement-icon" viewBox="0 0 100 100" fill="none">
                    <circle cx="50" cy="50" r="45" fill="white" opacity="0.2"/>
                    <path d="M30 40L50 25L70 40V70L50 85L30 70V40Z" fill="white" opacity="0.3"/>
                    <circle cx="50" cy="50" r="15" fill="white"/>
                </svg>
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