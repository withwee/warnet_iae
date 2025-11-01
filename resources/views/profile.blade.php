@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <!-- Tombol Kembali -->
    <div class="mb-4">
        <a href="{{ url()->previous() }}" class="text-blue-500 hover:text-blue-700">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 flex flex-col md:flex-row items-center gap-8">
        <div class="profile-pic text-center">
            <!-- Foto Profile Dinamis -->
            <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('default-profile.png') }}" 
                 alt="Profile Photo" 
                 class="rounded-full w-40 h-40 object-cover">
            <h2 class="mt-4 font-bold text-xl">{{ auth()->user()->name }}</h2>
        </div>

        <div class="info flex-1">
            <div class="mb-2"><label>Nama Lengkap:</label> {{ auth()->user()->name }}</div>
            <div class="mb-2"><label>Email:</label> {{ auth()->user()->email }}</div>
            <div class="mb-2"><label>NIK:</label> {{ auth()->user()->nik }}</div>
            <div class="mb-2"><label>Nomor KK:</label> {{ auth()->user()->no_kk }}</div>
            <div class="mb-2"><label>No HP:</label> {{ auth()->user()->phone }}</div>
            <div class="mb-2"><label>Jumlah Anggota Keluarga:</label> Laki-laki: {{ auth()->user()->male_family }} | Perempuan: {{ auth()->user()->female_family }}</div>
            
            <button onclick="toggleEdit()" class="mt-6 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit Profile</button>
        </div>
    </div>

    <!-- Form Edit Profile -->
    <div id="editForm" class="hidden mt-8 bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" onsubmit="return confirmSubmit()">
            @csrf
            @method('PUT')

            <!-- Upload Foto -->
            <div class="mb-4">
                <label for="photo" class="block font-bold mb-1">Ganti Foto Profile</label>
                <input type="file" name="photo" id="photo" accept="image/*" class="block">
            </div>

            <!-- Form Fields -->
            <div class="mb-4">
                <label class="block font-bold mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ auth()->user()->name }}" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-1">Email</label>
                <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-1">NIK</label>
                <input type="text" name="nik" value="{{ auth()->user()->nik }}" class="w-full border rounded p-2" required pattern="\d{16}" title="NIK harus 16 angka">
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-1">Nomor KK</label>
                <input type="text" name="no_kk" value="{{ auth()->user()->no_kk }}" class="w-full border rounded p-2" required pattern="\d{16}" title="Nomor KK harus 16 angka">
            </div>
            <div class="mb-4">
                <label class="block font-bold mb-1">No HP</label>
                <input type="text" name="phone" value="{{ auth()->user()->phone }}" class="w-full border rounded p-2" required pattern="\d+" title="No HP hanya boleh angka">
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 mt-6">
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Submit</button>
                <button type="button" onclick="confirmCancel()" class="bg-white border px-6 py-2 rounded hover:bg-gray-100">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleEdit() {
        document.getElementById('editForm').classList.toggle('hidden');
    }

    function confirmSubmit() {
        return confirm('Apakah Anda yakin ingin menyimpan perubahan?');
    }

    function confirmCancel() {
        if (confirm('Apakah Anda yakin ingin membatalkan perubahan profile?')) {
            window.location.reload();
        }
    }
</script>
@endsection
