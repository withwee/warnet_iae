@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" id="notifications-container">
    

    @if($notifikasis->isEmpty())
        <div class="bg-white p-8 rounded-xl shadow text-center">
            <iconify-icon icon="mdi:bell-off" width="64" class="text-gray-300 mx-auto mb-4"></iconify-icon>
            <p class="text-gray-500 text-lg">Belum ada notifikasi</p>
            <p class="text-gray-400 text-sm mt-2">Notifikasi akan muncul di sini ketika ada pengumuman baru</p>
        </div>
    @else
        <div class="space-y-3" id="notifications-list">
            @foreach($notifikasis as $notif)
                <div class="bg-white p-5 rounded-xl shadow hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            @if($notif->type === 'iuran')
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <iconify-icon icon="mdi:cash-multiple" width="24" class="text-green-600"></iconify-icon>
                                </div>
                            @elseif($notif->type === 'kalender')
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <iconify-icon icon="mdi:calendar" width="24" class="text-purple-600"></iconify-icon>
                                </div>
                            @elseif($notif->type === 'komentar')
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <iconify-icon icon="mdi:comment" width="24" class="text-blue-600"></iconify-icon>
                                </div>
                            @elseif($notif->type === 'forum')
                                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                                    <iconify-icon icon="mdi:forum" width="24" class="text-indigo-600"></iconify-icon>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <iconify-icon icon="mdi:bullhorn" width="24" class="text-blue-600"></iconify-icon>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-semibold text-gray-800 mb-1">
                                        @if($notif->type === 'iuran')
                                            Ayo bayar iuranmu!
                                        @elseif($notif->type === 'kalender')
                                            Acara Baru
                                        @elseif($notif->type === 'komentar')
                                            Komentar Baru
                                        @elseif($notif->type === 'forum')
                                            Postingan Forum Baru
                                        @else
                                            Pengumuman Baru
                                        @endif
                                    </p>
                                    <p class="text-gray-700">{{ $notif->message }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <iconify-icon icon="mdi:clock-outline" width="14" class="inline"></iconify-icon>
                                {{ $notif->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($notifikasis->count() >= 5)
            <div class="bg-blue-50 border border-blue-200 p-4 rounded-xl text-center">
                <p class="text-blue-700 text-sm">
                    <iconify-icon icon="mdi:information" width="18" class="inline"></iconify-icon>
                    Menampilkan 5 notifikasi terbaru. Notifikasi lama akan otomatis dihapus.
                </p>
            </div>
        @endif
    @endif

    <div class="text-center mt-6">
        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboardAdmin') : route('dashboard') }}" 
           class="inline-block bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-6 py-3 rounded-full transition-colors">
            <iconify-icon icon="mdi:arrow-left" width="20" class="inline"></iconify-icon>
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @auth
    // Listen for new notifications on this page
    window.Echo.private('notifications.{{ auth()->id() }}')
        .listen('.notification.sent', (e) => {
            console.log('New notification on page:', e);
            
            // Add new notification to the list
            addNotificationToList(e);
        });
    @endauth
});

function addNotificationToList(notification) {
    const notificationsList = document.getElementById('notifications-list');
    
    // If no notifications exist, reload the page to show the list
    if (!notificationsList) {
        location.reload();
        return;
    }
    
    // Determine icon and color based on type
    let icon = 'mdi:bullhorn';
    let iconBg = 'bg-blue-100';
    let iconColor = 'text-blue-600';
    let title = 'Pengumuman Baru';
    
    if (notification.type === 'iuran') {
        icon = 'mdi:cash-multiple';
        iconBg = 'bg-green-100';
        iconColor = 'text-green-600';
        title = 'Ayo bayar iuranmu!';
    } else if (notification.type === 'kalender') {
        icon = 'mdi:calendar';
        iconBg = 'bg-purple-100';
        iconColor = 'text-purple-600';
        title = 'Acara Baru';
    } else if (notification.type === 'komentar') {
        icon = 'mdi:comment';
        iconBg = 'bg-blue-100';
        iconColor = 'text-blue-600';
        title = 'Komentar Baru';
    } else if (notification.type === 'forum') {
        icon = 'mdi:forum';
        iconBg = 'bg-indigo-100';
        iconColor = 'text-indigo-600';
        title = 'Postingan Forum Baru';
    }
    
    // Create new notification element
    const notifElement = document.createElement('div');
    notifElement.className = 'bg-white p-5 rounded-xl shadow hover:shadow-md transition-shadow animate-slide-down';
    notifElement.innerHTML = `
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 ${iconBg} rounded-full flex items-center justify-center">
                    <iconify-icon icon="${icon}" width="24" class="${iconColor}"></iconify-icon>
                </div>
            </div>
            <div class="flex-1">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-gray-800 mb-1">${title}</p>
                        <p class="text-gray-700">${notification.message}</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    <iconify-icon icon="mdi:clock-outline" width="14" class="inline"></iconify-icon>
                    ${notification.created_at}
                </p>
            </div>
        </div>
    `;
    
    // Add to top of list
    notificationsList.insertBefore(notifElement, notificationsList.firstChild);
    
    // Remove oldest notification if more than 5
    const notifications = notificationsList.children;
    if (notifications.length > 5) {
        notificationsList.removeChild(notifications[notifications.length - 1]);
    }
}
</script>

<style>
@keyframes slide-down {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-slide-down {
    animation: slide-down 0.3s ease-out;
}
</style>
@endpush
