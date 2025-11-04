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
            <img src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('images/Foto Profil.jpg') }}" 
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
            <div class="mb-2"><label>Jumlah Anggota Keluarga:</label> Laki-laki: {{ auth()->user()->jumlah_LK ?? 0 }} | Perempuan: {{ auth()->user()->jumlah_PR ?? 0 }}</div>
            
            <button onclick="toggleEdit()" class="mt-6 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit Profile</button>
        </div>
    </div>

    <!-- Form Edit Profile -->
    <div id="editForm" class="hidden mt-8 bg-white rounded-lg shadow-md p-6">
        <form method="POST" action="{{ route('profile.update', auth()->user()->id) }}" enctype="multipart/form-data" onsubmit="return confirmSubmit()">
            @csrf
            @method('PUT')

            <!-- Upload Foto -->
            <div class="mb-4">
                <label for="photo" class="block font-bold mb-1">Ganti Foto Profile</label>
                <input type="file" name="photo" id="photo" accept="image/*" onchange="previewPhotoEdit(event)" class="block">
                
                <!-- Preview Foto -->
                <div class="mt-4">
                    <img id="photoPreviewEdit" 
                         src="{{ auth()->user()->photo ? asset('storage/' . auth()->user()->photo) : asset('images/Foto Profil.jpg') }}" 
                         alt="Photo Preview" 
                         class="w-20 h-20 rounded-full object-cover shadow-md">
                </div>

                <!-- Tombol Hapus Foto -->
                @if(auth()->user()->photo)
                <div class="mt-2">
                    <button type="button" onclick="deletePhotoProfile()" class="text-red-500 hover:text-red-700 text-sm font-semibold">
                        🗑️ Hapus Foto Profil
                    </button>
                </div>
                @endif
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

            <!-- Jumlah Anggota Keluarga -->
            <div class="mb-4">
                <h3 class="block font-bold mb-2">Jumlah Anggota Keluarga (Termasuk Diri Sendiri)</h3>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block font-bold mb-1">Laki-laki</label>
                        <input type="number" name="jumlah_LK" min="0" value="{{ auth()->user()->jumlah_LK ?? 0 }}" class="w-full border rounded p-2" required>
                    </div>
                    <div class="flex-1">
                        <label class="block font-bold mb-1">Perempuan</label>
                        <input type="number" name="jumlah_PR" min="0" value="{{ auth()->user()->jumlah_PR ?? 0 }}" class="w-full border rounded p-2" required>
                    </div>
                </div>
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

    function previewPhotoEdit(event) {
        const input = event.target;
        const preview = document.getElementById('photoPreviewEdit');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function deletePhotoProfile() {
        if (confirm('Apakah Anda yakin ingin menghapus foto profil?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("profile.deletePhoto", auth()->user()->id) }}';
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection
