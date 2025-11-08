@extends('layouts.user-layout')

@section('title', 'Profile')

@section('content')
@php
    $currentUser = $user ?? auth()->user();
@endphp

<div class="space-y-6">
    <!-- Profile Show Section -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Profile Show</h2>
        
        <div class="flex items-start gap-6">
            <!-- Photo -->
            <div class="flex-shrink-0">
                @if($currentUser && $currentUser->photo)
                    <img src="{{ asset('storage/' . $currentUser->photo) }}" alt="Profile Photo" class="w-32 h-32 rounded-full object-cover border-4 border-blue-500">
                @else
                    <img src="{{ asset('images/Foto Profil.jpg') }}" alt="Default Photo" class="w-32 h-32 rounded-full object-cover border-4 border-gray-300">
                @endif
            </div>
            
            <!-- Info -->
            <div class="flex-1 space-y-4">
                <div class="grid grid-cols-2 gap-6">
                    <div class="border-b pb-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nama Lengkap</p>
                        <p class="text-base font-semibold text-gray-800">{{ $currentUser->name ?? '-' }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Email</p>
                        <p class="text-base font-semibold text-gray-800">{{ $currentUser->email ?? '-' }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">NIK</p>
                        <p class="text-base font-semibold text-gray-800">{{ $currentUser->nik ?? '-' }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">No. Kartu Keluarga</p>
                        <p class="text-base font-semibold text-gray-800">{{ $currentUser->no_kk ?? '-' }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Nomor Handphone</p>
                        <p class="text-base font-semibold text-gray-800">{{ $currentUser->phone ?? '-' }}</p>
                    </div>
                    <div class="border-b pb-3">
                        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Jumlah Keluarga</p>
                        <p class="text-base font-semibold text-gray-800">
                            <span class="inline-flex items-center gap-3">
                                <span>👨 {{ $currentUser->jumlah_LK ?? 0 }}</span>
                                <span>👩 {{ $currentUser->jumlah_PR ?? 0 }}</span>
                                <span class="text-sm text-gray-600">(Total: {{ ($currentUser->jumlah_LK ?? 0) + ($currentUser->jumlah_PR ?? 0) }})</span>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Profile Section -->
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Profile</h2>
        
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PATCH')
            
            <!-- Foto Profile -->
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Foto Profile</label>
                <input type="file" name="photo" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 1MB</p>
                @error('photo')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                @if($currentUser && $currentUser->photo)
                    <img src="{{ asset('storage/' . $currentUser->photo) }}" alt="Current Photo" class="mt-3 w-24 h-24 rounded-full object-cover">
                @else
                    <img src="{{ asset('images/Foto Profil.jpg') }}" alt="Default Photo" class="mt-3 w-24 h-24 rounded-full object-cover">
                @endif
            </div>
            
            <!-- Nama Lengkap -->
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $currentUser->name ?? '') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $currentUser->email ?? '') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Nomor Induk Kependudukan (NIK) -->
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Nomor Induk Kependudukan (NIK)</label>
                <input type="text" name="nik" value="{{ old('nik', $currentUser->nik ?? '') }}" required maxlength="16" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nik')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Nomor Kartu Keluarga -->
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Nomor Kartu Keluarga</label>
                <input type="text" name="no_kk" value="{{ old('no_kk', $currentUser->no_kk ?? '') }}" required maxlength="16" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('no_kk')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Nomor Handphone -->
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Nomor Handphone</label>
                <input type="text" name="phone" value="{{ old('phone', $currentUser->phone ?? '') }}" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('phone')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Jumlah Anggota Keluarga -->
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Jumlah Anggota Keluarga (Termasuk Diri Sendiri)</label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Laki-laki</label>
                        <input type="number" name="jumlah_LK" value="{{ old('jumlah_LK', $currentUser->jumlah_LK ?? 0) }}" required min="0" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Perempuan</label>
                        <input type="number" name="jumlah_PR" value="{{ old('jumlah_PR', $currentUser->jumlah_PR ?? 0) }}" required min="0" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
    
    @if(session('status'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('status') }}
        </div>
    @endif
</div>
@endsection
