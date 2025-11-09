<aside class="bg-blue-600 text-white w-64 h-full fixed left-0 items-center gap-6 p-6 flex flex-col">
    <div class="text-3xl">
        <span class="font-extrabold">Warga</span><span class="text-gray-200">Net</span>
    </div>
    <nav class="flex flex-col gap-4">

        {{-- Dashboard --}}
         @if(auth()->user() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboardAdmin') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('admin.dashboardAdmin') || request()->routeIs('admin.pengeluaran.*') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:view-dashboard" class="text-xl"></iconify-icon>
                Dashboard
            </a>
        @else
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('dashboard') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:view-dashboard" class="text-xl"></iconify-icon>
                Dashboard
            </a>
        @endif

        {{-- Pengumuman --}}
        @if(auth()->user() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.pengumuman') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('admin.pengumuman') || request()->routeIs('pengumuman.*') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:bullhorn-outline" class="text-xl"></iconify-icon>
                Pengumuman
            </a>
        @else
            <a href="{{ route('pengumuman') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('pengumuman') || request()->routeIs('pengumuman.*') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:bullhorn-outline" class="text-xl"></iconify-icon>
                Pengumuman
            </a>
        @endif

        {{-- Forum --}}
        @if(auth()->user() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.forum') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('admin.forum*') || request()->routeIs('forum.*') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:forum-outline" class="text-xl"></iconify-icon>
                Forum
            </a>
        @else
            <a href="{{ route('forum') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('forum') || request()->routeIs('forum.*') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:forum-outline" class="text-xl"></iconify-icon>
                Forum
            </a>
        @endif

        {{-- Bayar Iuran --}}
        @if(auth()->user() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.bayar-iuran') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('admin.bayar-iuran') || request()->routeIs('iuran.*') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:cash-multiple" class="text-xl"></iconify-icon>
                Bayar Iuran
            </a>
        @else
            <a href="{{ route('bayar-iuran') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('bayar-iuran') || request()->routeIs('iuran.*') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:cash-multiple" class="text-xl"></iconify-icon>
                Bayar Iuran
            </a>
        @endif

        {{-- Kalender --}}
        @if(auth()->user() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.kalender') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('admin.kalender') || request()->routeIs('kegiatan.*') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:calendar-month-outline" class="text-xl"></iconify-icon>
                Kalender
            </a>
        @else
            <a href="{{ route('kalender') }}"
               class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold transition
               {{ request()->routeIs('kalender') || request()->routeIs('kegiatan.*') ? 'bg-white text-blue-600' : 'text-white hover:text-white hover:bg-blue-500' }}">
                <iconify-icon icon="mdi:calendar-month-outline" class="text-xl"></iconify-icon>
                Kalender
            </a>
        @endif

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST"
              class="flex items-center gap-3 py-2 px-4 rounded-xl font-semibold bg-white text-red-500 hover:bg-red-500 hover:text-white transition">
            @csrf
            <iconify-icon icon="mdi:logout" class="text-xl"></iconify-icon>
            <button type="submit" class="logout-button">Logout</button>
        </form>
    </nav>
</aside>