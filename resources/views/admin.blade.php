@extends('layouts.auth-admin')

@section('title', 'Login Admin')

@section('content')

<div class="flex flex-col w-full mt-8 space-y-6">
    <form action="{{ route('admin') }}" method="POST" class="space-y-4 w-full" onsubmit="return validateAdminForm();">
        @csrf

        <!-- Input Username -->
        <div class="relative">
            <input 
                type="text" 
                name="name" 
                id="name"
                placeholder="Masukkan Username" 
                value="{{ old('name') }}" 
                class="border border-gray-300 p-3 pl-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                required 
                oninvalid="this.setCustomValidity('Harap isi Username Anda terlebih dahulu')" 
                oninput="this.setCustomValidity('')"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('icon/user.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
            </div>
        </div>  
        @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        <!-- Input Password -->
        <div class="relative">
            <input 
                type="password" 
                name="password" 
                id="password" 
                placeholder="Masukkan Kata Sandi" 
                class="border border-gray-300 p-3 pl-12 pr-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
                oninvalid="this.setCustomValidity('Harap isi Kata Sandi Anda terlebih dahulu')" 
                oninput="this.setCustomValidity('')" 
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('icon/lock.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
            </div>
            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 focus:outline-none">
                <iconify-icon id="eyeIcon" icon="mdi:eye-outline" width="20" height="20" class="text-black"></iconify-icon>
            </button>
        </div>
        @error('password')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        @if ($errors->has('error'))
            <p class="text-red-500 text-sm mt-2">{{ $errors->first('error') }}</p>
        @endif

        <!-- Tombol Login -->
        <button 
            type="submit" 
            class="bg-blue-600 hover:bg-blue-700 transition-colors text-white font-semibold px-4 py-3 rounded-lg w-full">
            Masuk Sebagai Admin
        </button>
    </form>
</div>

<!-- CDN SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- JS: Toggle Password + Validasi Form Kosong -->
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.setAttribute('icon', 'mdi:eye-off-outline');
        } else {
            passwordInput.type = 'password';
            eyeIcon.setAttribute('icon', 'mdi:eye-outline');
        }
    }

    function validateAdminForm() {
        const name = document.getElementById('name').value.trim();
        const password = document.getElementById('password').value.trim();

        if (!name && !password) {
            Swal.fire({
                icon: 'warning',
                title: 'Data Kosong',
                text: 'Harap isi Username dan Kata Sandi Anda terlebih dahulu.',
            });
            return false;
        } else if (!name) {
            Swal.fire({
                icon: 'warning',
                title: 'Username Kosong',
                text: 'Harap isi Username Anda sebelum melanjutkan.',
            });
            return false;
        } else if (!password) {
            Swal.fire({
                icon: 'warning',
                title: 'Kata Sandi Kosong',
                text: 'Harap isi Kata Sandi Anda sebelum melanjutkan.',
            });
            return false;
        }

        return true;
    }
</script>

@endsection
