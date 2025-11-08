<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - WargaNet</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
        }
        .container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #2563eb;
            padding: 2rem 0;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
        }
        .logo {
            color: white;
            font-size: 1.75rem;
            font-weight: 700;
            padding: 0 1.5rem;
            margin-bottom: 2rem;
        }
        .menu {
            flex: 1;
            overflow-y: auto;
        }
        .menu-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 0.95rem;
        }
        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .menu-item.active {
            background: rgba(255, 255, 255, 0.2);
            border-left: 4px solid white;
        }
        .logout-btn {
            margin: 1rem 1.5rem;
            padding: 0.875rem 1rem;
            background: white;
            color: #ef4444;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            justify-content: center;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: #fee2e2;
        }
        .main-content {
            margin-left: 250px;
            flex: 1;
            padding: 2rem;
            background: #f0f4f8;
            min-height: 100vh;
        }
        .header {
            background: white;
            padding: 1.25rem 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .header-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1f2937;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #6b7280;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">WargaNet</div>
            
            <div class="menu">
                <a href="{{ route('admin.dashboardAdmin') }}" class="menu-item {{ Request::routeIs('admin.dashboardAdmin') ? 'active' : '' }}">
                    <iconify-icon icon="mdi:view-dashboard" width="20"></iconify-icon>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('admin.pengumuman') }}" class="menu-item {{ Request::routeIs('admin.pengumuman') ? 'active' : '' }}">
                    <iconify-icon icon="mdi:bullhorn" width="20"></iconify-icon>
                    <span>Pengumuman</span>
                </a>
                
                <a href="{{ route('admin.forum') }}" class="menu-item {{ Request::routeIs('admin.forum') ? 'active' : '' }}">
                    <iconify-icon icon="mdi:forum" width="20"></iconify-icon>
                    <span>Forum</span>
                </a>
                
                <a href="{{ route('admin.bayar-iuran') }}" class="menu-item {{ Request::routeIs('admin.bayar-iuran') ? 'active' : '' }}">
                    <iconify-icon icon="mdi:cash-multiple" width="20"></iconify-icon>
                    <span>Bayar Iuran</span>
                </a>
                
                <a href="{{ route('admin.kalender') }}" class="menu-item {{ Request::routeIs('admin.kalender') ? 'active' : '' }}">
                    <iconify-icon icon="mdi:calendar" width="20"></iconify-icon>
                    <span>Kalender</span>
                </a>
            </div>
            
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <iconify-icon icon="mdi:logout" width="20"></iconify-icon>
                    <span>Logout</span>
                </button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <h1 class="header-title">@yield('title', 'Dashboard')</h1>
                <div class="user-info">
                    <span>{{ auth()->user()->name ?? 'SuperAdmin' }}</span>
                    <iconify-icon icon="mdi:account-circle" width="32" class="text-blue-600"></iconify-icon>
                    <a href="{{ route('notifikasi') }}" class="hover:opacity-70 transition-opacity" title="Notifikasi">
                        <iconify-icon icon="mdi:bell" width="24" class="text-gray-600"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>
</body>
</html>
