<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WargaNet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            font-family: 'Figtree', sans-serif;
            background: #e8f4f8;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 200px;
            background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .sidebar-logo {
            color: white;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .sidebar-item.active {
            background: white;
            color: #2563eb;
            font-weight: 600;
        }
        .sidebar-item svg {
            width: 20px;
            height: 20px;
        }
        .logout-btn {
            margin-top: auto;
            background: white;
            color: #ef4444;
            font-weight: 600;
        }
        .logout-btn:hover {
            background: #fee2e2;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .announcement-box {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .announcement-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        .announcement-content p {
            opacity: 0.9;
            margin-bottom: 1rem;
        }
        .announcement-date {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            display: inline-block;
            font-size: 0.875rem;
        }
        .announcement-icon {
            width: 100px;
            height: 100px;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        .data-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .data-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .data-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        .data-label {
            font-size: 1rem;
            color: #6b7280;
            font-weight: 600;
        }
        .data-icon {
            width: 60px;
            height: 60px;
            margin-top: 0.5rem;
        }
        .keuangan-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        .keuangan-card {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .keuangan-amount {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        .keuangan-label {
            font-size: 0.875rem;
            color: #6b7280;
        }
        .calendar-widget {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-logo">WargaNet</div>
            
            <a href="{{ route('dashboard') }}" class="sidebar-item active">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                Dashboard
            </a>
            
            <a href="{{ route('pengumuman') }}" class="sidebar-item">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
                </svg>
                Pengumuman
            </a>
            
            <a href="{{ route('forum') }}" class="sidebar-item">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                    <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                </svg>
                Forum
            </a>
            
            <a href="{{ route('bayar-iuran') }}" class="sidebar-item">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                    <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                </svg>
                Bayar Iuran
            </a>
            
            <a href="{{ route('kalender') }}" class="sidebar-item">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Kalender
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-auto">
                @csrf
                <button type="submit" class="sidebar-item logout-btn w-full text-left">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <h1 class="header-title">Dashboard</h1>
                <div class="user-info">
                    <span>{{ isset($user) && $user ? $user->name : auth()->user()->name }}</span>
                    <a href="{{ route('profile.edit') }}" class="hover:opacity-70 transition-opacity" title="Edit Profile">
                        @if($user && $user->photo)
                            <img src="{{ asset('storage/' . $user->photo) . '?v=' . $user->updated_at->timestamp }}" alt="Profile" class="w-8 h-8 rounded-full object-cover border-2 border-gray-300">
                        @else
                            <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </a>
                    <a href="{{ route('notifikasi') }}" class="hover:opacity-70 transition-opacity" title="Notifikasi">
                        <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                        </svg>
                    </a>
                </div>
            </div>

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
            </div>
        </div>
    </div>
</body>
</html>
