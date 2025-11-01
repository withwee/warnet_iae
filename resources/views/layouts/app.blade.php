<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard - Warganet')</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
        <script src="//unpkg.com/alpinejs" defer></script>
    
    @stack('styles')
<body class="bg-sky-100 font-[Poppins]">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <x-sidebar />

        {{-- Main content --}}
        <main class="flex-1 p-6 ml-64">
        {{-- Header --}}
            <header class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold capitalize text-gray-800">
                    @php
                        $routeName = Route::currentRouteName();
                        $routeTitles = [
                            'iuran.cari' => 'Bayar Iuran',
                            'iuran.store' => '',
                            'iuran.bayar' => 'Bayar Iuran',
                            'pay.index' => 'Bayar Iuran',
                            'pay.bayar' => 'Bayar Iuran',
                            // Add other route mappings as needed
                        ];
                        $title = $routeTitles[$routeName] ?? ucfirst(str_replace(['-', '.'], ' ', $routeName));
                    @endphp
                    {{ $title }}
                </h1>

                <div class="flex items-center gap-3">
                    <a href="{{ route('profile.show') }}" class="flex items-center gap-2">
                        <span class="font-semibold text-gray-800">{{ $user->name ?? 'Guest' }}</span>
                        <img src="{{ auth()->user() && auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('images/profile.png') }}" 
                        alt="Profile Photo" 
                        class="w-10 h-10 rounded-full border-2 border-white shadow-md object-cover">
                    </a>
                @php
                    $notifCount = 0;
                    $notifList = \App\Models\notification::where('user_id', auth()->id())->latest()->take(5)->get();
                @endphp
                <div 
                    x-data="{
                        open: false, 
                        notifCount: {{ $notifCount }},
                        fetchUnreadCount() {
                            fetch('{{ url('/notifikasi/unread-count') }}')
                                .then(response => response.json())
                                .then(data => { this.notifCount = data.unread_count; });
                        }
                    }" 
                    x-effect="if(open && notifCount > 0){ 
                        fetch('{{ url('/notifikasi/read') }}', { 
                            method: 'POST', 
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                                'Accept': 'application/json' 
                            } 
                        }).then(response => {
                            console.log('Mark as read response:', response);
                            notifCount = 0; 
                            fetchUnreadCount();
                        }).catch(error => {
                            console.error('Error marking notifications as read:', error);
                        });
                    }"
                    class="relative"
                >
                    <a href="#" @click.prevent="open = !open" class="relative ml-2">
                        <iconify-icon icon="mdi:bell-outline" class="text-2xl text-blue-600"></iconify-icon>
                        <template x-if="notifCount > 0">
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full px-1 text-xs" x-text="notifCount"></span>
                        </template>
                    </a>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-lg z-50 p-4">
                        <h2 class="font-bold mb-2">Notifikasi</h2>
                        <ul>
                            @forelse($notifList as $notif)
                                <li class="mb-2 p-2 rounded {{ $notif->read ? 'bg-gray-100' : 'bg-blue-100' }}">
                                    {{ $notif->message }}
                                    <span class="text-xs text-gray-400 ml-2">{{ $notif->created_at->diffForHumans() }}</span>
                                </li>
                            @empty
                                <li class="text-gray-500">Tidak ada notifikasi.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
                </div>
            </header>

            {{-- Page content --}}
            <section>
                @yield('content')
            </section>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
