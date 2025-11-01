@extends('layouts.app')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>

    <form action="{{ route('profile.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl p-8 shadow-md">
        @csrf
        @method('PUT')

     <!-- Photo Upload -->
<div class="mb-4">
    <label class="block text-sm font-bold mb-2" for="photo">Foto Profile</label>
    <input type="file" name="photo" id="photo" accept="image/*" onchange="previewPhoto(event)" class="border rounded p-2 w-full">

    <!-- Preview Foto -->
    <div class="mt-4">
        <img id="photoPreview" 
             src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/profile.png') }}" 
             alt="Current Photo" 
             class="w-20 h-20 rounded-full object-cover shadow-md">
    </div>

    @error('photo')
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>


        <!-- Name -->
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2" for="name">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="border rounded p-2 w-full" required>
            @error('name')
                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2" for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="border rounded p-2 w-full" required>
        </div>

        <!-- NIK -->
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2" for="nik">Nomor Induk Kependudukan (NIK)</label>
            <input type="text" name="nik" id="nik" value="{{ old('nik', $user->nik) }}" class="border rounded p-2 w-full" required>
        </div>

        <!-- No KK -->
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2" for="no_kk">Nomor Kartu Keluarga</label>
            <input type="text" name="no_kk" id="no_kk" value="{{ old('no_kk', $user->no_kk) }}" class="border rounded p-2 w-full" required>
        </div>

        <!-- Phone -->
        <div class="mb-4">
            <label class="block text-sm font-bold mb-2" for="phone">Nomor Handphone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" class="border rounded p-2 w-full" required>
        </div>

        <!-- Jumlah Anggota Keluarga -->
        <div class="mb-4">
            <h3 class="text-sm font-bold mb-2">Jumlah Anggota Keluarga (Termasuk Diri Sendiri)</h3>
            <div class="flex gap-16 mt-2">
                <div>
                    <label class="block text-sm font-bold mb-2" for="jumlah_LK">Laki-laki</label>
                    <input type="number" name="jumlah_LK" id="jumlah_LK" min="0" value="{{ old('jumlah_LK', $user->jumlah_LK) }}" class="border rounded p-2 w-full" required>
                    @error('jumlah_LK')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold mb-2" for="jumlah_PR">Perempuan</label>
                    <input type="number" name="jumlah_PR" id="jumlah_PR" min="0" value="{{ old('jumlah_PR', $user->jumlah_PR) }}" class="border rounded p-2 w-full" required>
                    @error('jumlah_PR')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center">
            <button type="submit" class="bg-blue-500 text-white rounded-lg px-6 py-2 hover:bg-blue-600 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
<script>
    function previewPhoto(event) {
        const input = event.target;
        const preview = document.getElementById('photoPreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection