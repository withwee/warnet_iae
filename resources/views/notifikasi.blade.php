@extends('layouts.user-layout')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    

    @if($notifikasis->isEmpty())
        <div class="bg-white p-8 rounded-xl shadow text-center">
            <iconify-icon icon="mdi:bell-off" width="64" class="text-gray-300 mx-auto mb-4"></iconify-icon>
            <p class="text-gray-500 text-lg">Belum ada notifikasi</p>
            <p class="text-gray-400 text-sm mt-2">Notifikasi akan muncul di sini ketika ada pengumuman baru</p>
        </div>
    @else
        <div class="space-y-3">
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
