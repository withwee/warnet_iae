<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - WargaNet</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #e8f4f8;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .dashboard-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }
        .sidebar {
            width: 250px;
            background: linear-gradient(180deg, #2563eb 0%, #1e40af 100%);
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
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
            margin-left: 250px;
            flex: 1;
            padding: 2rem;
            min-height: 100vh;
            width: calc(100% - 250px);
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1rem;
            }
            .main-content.shifted {
                margin-left: 280px;
                width: calc(100% - 280px);
            }
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
    </style>
</head>
<body>
<div class="dashboard-container">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">WargaNet</div>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboardAdmin') }}" class="sidebar-item {{ Request::is('admin/dashboard*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.pengumuman') }}" class="sidebar-item {{ Request::is('admin/pengumuman*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12,8H4A2,2 0 0,0 2,10V14A2,2 0 0,0 4,16H5V20A1,1 0 0,0 6,21H8A1,1 0 0,0 9,20V16H12L17,20V4L12,8M21.5,12C21.5,13.71 20.54,15.26 19,16V8C20.54,8.74 21.5,10.29 21.5,12Z"/>
                </svg>
                Pengumuman
            </a>
            <a href="{{ route('admin.forum') }}" class="sidebar-item {{ Request::is('admin/forum*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                    <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                </svg>
                Forum
            </a>
            <a href="{{ route('admin.bayar-iuran') }}" class="sidebar-item {{ Request::is('admin/bayar-iuran*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5,6H23V18H5V6M14,9A3,3 0 0,1 17,12A3,3 0 0,1 14,15A3,3 0 0,1 11,12A3,3 0 0,1 14,9M9,8A2,2 0 0,1 7,10V14A2,2 0 0,1 9,16H19A2,2 0 0,1 21,14V10A2,2 0 0,1 19,8H9M1,10H3V20H19V22H1V10Z"/>
                </svg>
                Kelola Iuran
            </a>
            <a href="{{ route('admin.kalender') }}" class="sidebar-item {{ Request::is('admin/kalender*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Kalender
            </a>
        @else
            <a href="{{ route('dashboard') }}" class="sidebar-item {{ Request::is('dashboard*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('pengumuman') }}" class="sidebar-item {{ Request::is('pengumuman*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12,8H4A2,2 0 0,0 2,10V14A2,2 0 0,0 4,16H5V20A1,1 0 0,0 6,21H8A1,1 0 0,0 9,20V16H12L17,20V4L12,8M21.5,12C21.5,13.71 20.54,15.26 19,16V8C20.54,8.74 21.5,10.29 21.5,12Z"/>
                </svg>
                Pengumuman
            </a>
            <a href="{{ route('forum') }}" class="sidebar-item {{ Request::is('forum*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                    <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                </svg>
                Forum
            </a>
            <a href="{{ route('bayar-iuran') }}" class="sidebar-item {{ Request::is('bayar-iuran*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 24 24">
                    <path d="M5,6H23V18H5V6M14,9A3,3 0 0,1 17,12A3,3 0 0,1 14,15A3,3 0 0,1 11,12A3,3 0 0,1 14,9M9,8A2,2 0 0,1 7,10V14A2,2 0 0,1 9,16H19A2,2 0 0,1 21,14V10A2,2 0 0,1 19,8H9M1,10H3V20H19V22H1V10Z"/>
                </svg>
                Bayar Iuran
            </a>
            <a href="{{ route('kalender') }}" class="sidebar-item {{ Request::is('kalender*') ? 'active' : '' }}">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 00 2 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Kalender
            </a>
        @endif

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
            <h1 class="header-title">@yield('title', 'Dashboard')</h1>
            <div class="user-info">
                <span>{{ auth()->user()->name ?? 'User' }}</span>

                <a href="{{ route('profile.edit') }}" class="hover:opacity-70 transition-opacity" title="Edit Profil">
                    @if(auth()->user() && auth()->user()->photo)
                        <img src="{{ asset('storage/' . auth()->user()->photo) . '?v=' . auth()->user()->updated_at->timestamp }}"
                             alt="Profile" class="w-8 h-8 rounded-full object-cover border-2 border-gray-300">
                    @else
                        <svg class="w-8 h-8 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    @endif
                </a>

                <a href="{{ route('notifikasi') }}" class="relative hover:opacity-70 transition-opacity" title="Notifikasi" id="notification-bell">
                    <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                    </svg>
                    <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                </a>
            </div>
        </div>

        <!-- Page Content -->
        @yield('content')
    </div>
</div>

@stack('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    @auth
    // Listen for real-time notifications
    window.Echo.private('notifications.{{ auth()->id() }}')
        .listen('.notification.sent', (e) => {
            console.log('New notification received:', e);
            
            // Show browser notification if permitted
            if (Notification.permission === 'granted') {
                new Notification('WargaNet - Notifikasi Baru', {
                    body: e.message,
                    icon: '/favicon.ico'
                });
            }
            
            // Update notification badge
            const badge = document.getElementById('notification-badge');
            if (badge) {
                let count = parseInt(badge.textContent) || 0;
                count++;
                badge.textContent = count;
                badge.classList.remove('hidden');
            }
            
            // Show toast notification
            showToast(e.type, e.message);
        });
    
    // Request notification permission
    if (Notification.permission === 'default') {
        Notification.requestPermission();
    }
    @endauth
});

// Toast notification function
function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 max-w-sm z-50 animate-slide-in';
    
    let icon = '';
    let iconColor = '';
    
    if (type === 'iuran') {
        icon = 'mdi:cash-multiple';
        iconColor = 'text-green-600';
    } else if (type === 'kalender') {
        icon = 'mdi:calendar';
        iconColor = 'text-purple-600';
    } else if (type === 'komentar') {
        icon = 'mdi:comment';
        iconColor = 'text-blue-600';
    } else {
        icon = 'mdi:bullhorn';
        iconColor = 'text-blue-600';
    }
    
    toast.innerHTML = `
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <iconify-icon icon="${icon}" width="24" class="${iconColor}"></iconify-icon>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-800 mb-1">Notifikasi Baru</p>
                <p class="text-sm text-gray-600">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                <iconify-icon icon="mdi:close" width="20"></iconify-icon>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}
</script>

<style>
@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
    transition: all 0.3s ease-out;
}
</style>
</body>
</html>
