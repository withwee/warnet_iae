@extends('layouts.auth')

@section('title', 'Register')

@section('content')

<div class="flex flex-col w-full scale-90 h-full gap-2">
    <div class="flex items-center justify-center gap-0.5 text-5xl text-[#2C79FF]">
        <h1 class="font-extrabold">Warga</h1>
        <h1>Net</h1>
      </div>
    <form action="{{ route('register.submit') }}" method="POST" class="space-y-2 w-full h-full" id="registerForm">
        @csrf

        {{-- Nama Lengkap --}}
        <div class="relative">
            <input
                type="text"
                name="name"
                id="name"
                placeholder="Nama Lengkap"
                value="{{ old('name') }}"
                class="border border-gray-300 p-3 pl-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
                oninvalid="this.setCustomValidity('Nama Lengkap wajib diisi.')"
                oninput="this.setCustomValidity('')"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('icon/user.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
            </div>
        </div>
        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

        {{-- Email --}}
        <div class="relative">
            <input
                type="email"
                name="email"
                id="email"
                placeholder="Email"
                value="{{ old('email') }}"
                class="border border-gray-300 p-3 pl-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
                oninvalid="this.setCustomValidity('Email wajib diisi dengan format yang benar.')"
                oninput="this.setCustomValidity('')"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('icon/email.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
            </div>
        </div>
        @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

        {{-- NIK --}}
        <div class="relative">
            <input
                type="text"
                name="nik"
                id="nik"
                placeholder="NIK"
                value="{{ old('nik') }}"
                class="border border-gray-300 p-3 pl-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
                pattern="\d{16}"
                oninvalid="this.setCustomValidity('NIK wajib diisi dan harus terdiri dari 16 digit angka.')"
                oninput="this.setCustomValidity('')"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('icon/ktp.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
            </div>
        </div>
        @error('nik') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

        {{-- Nomor KK --}}
        <div class="relative">
            <input
                type="text"
                name="no_kk"
                id="no_kk"
                placeholder="Nomor KK"
                value="{{ old('no_kk') }}"
                class="border border-gray-300 p-3 pl-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
                pattern="\d{16}"
                oninvalid="this.setCustomValidity('Nomor KK wajib diisi dan harus terdiri dari 16 digit angka.')"
                oninput="this.setCustomValidity('')"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('icon/keluarga.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
            </div>
        </div>
        @error('no_kk') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

        {{-- Nomor HP --}}
        <div class="relative">
            <input
                type="text"
                name="phone"
                id="no_hp"
                placeholder="Nomor HP"
                value="{{ old('phone') }}"
                class="border border-gray-300 p-3 pl-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
                pattern="^08[0-9]{8,11}$"
                oninvalid="this.setCustomValidity('Nomor HP wajib diisi, diawali 08 dan terdiri dari 10-13 digit angka.')"
                oninput="this.setCustomValidity('')"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('icon/phone.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
            </div>
        </div>
        @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

        {{-- Jumlah Anggota Keluarga --}}
        <label class="block text-sm text-gray-600 font-semibold pt-2">Jumlah anggota keluarga (termasuk diri sendiri):</label>
        <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0 mt-1"> 
            <div class="relative w-full">
                <input
                    type="number"
                    name="jumlah_LK"
                    id="jumlah_l"
                    placeholder="Laki-laki"
                    value="{{ old('jumlah_LK') }}"
                    class="border border-gray-300 p-3 pl-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                    min="0"
                    oninvalid="this.setCustomValidity('Jumlah Laki-laki wajib diisi dan tidak boleh kurang dari 0.')"
                    oninput="this.setCustomValidity('')"
                >
                <div class="absolute left-4 top-1/2 -translate-y-1/2">
                    <img src="{{ asset('icon/keluargacwo.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
                </div>
            </div>
            @error('jumlah_LK') <p class="text-red-500 text-sm mt-1 w-full sm:w-auto">{{ $message }}</p> @enderror

            <div class="relative w-full">
                <input
                    type="number"
                    name="jumlah_PR"
                    id="jumlah_p"
                    placeholder="Perempuan"
                    value="{{ old('jumlah_PR') }}"
                    class="border border-gray-300 p-3 pl-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                    min="0"
                    oninvalid="this.setCustomValidity('Jumlah Perempuan wajib diisi dan tidak boleh kurang dari 0.')"
                    oninput="this.setCustomValidity('')"
                >
                <div class="absolute left-4 top-1/2 -translate-y-1/2">
                    <img src="{{ asset('icon/keluargacwe.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
                </div>
            </div>
            @error('jumlah_PR') <p class="text-red-500 text-sm mt-1 w-full sm:w-auto">{{ $message }}</p> @enderror
        </div>


        {{-- Kata Sandi --}}
        <div class="relative">
            <input
                type="password"
                name="password"
                id="password"
                placeholder="Kata Sandi"
                class="border border-gray-300 p-3 pl-12 pr-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
                minlength="8"
                oninvalid="this.setCustomValidity('Kata Sandi wajib diisi (minimal 8 karakter).')"
                oninput="this.setCustomValidity(''); validatePasswordConfirmation();"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('icon/lock.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
            </div>
            <button type="button" onclick="togglePassword('password', 'eyeIcon')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                <iconify-icon id="eyeIcon" icon="mdi:eye-outline" width="20" height="20"></iconify-icon>
            </button>
        </div>
        @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror

        {{-- Konfirmasi Kata Sandi --}}
        <div class="relative">
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                placeholder="Konfirmasi Kata Sandi"
                class="border border-gray-300 p-3 pl-12 pr-12 rounded-lg bg-gray-100 w-full focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
                oninvalid="this.setCustomValidity('Konfirmasi Kata Sandi wajib diisi.')"
                oninput="validatePasswordConfirmation();"
            >
            <div class="absolute left-4 top-1/2 -translate-y-1/2">
                <img src="{{ asset('icon/lock.svg') }}" alt="icon" class="w-5 h-5 text-gray-400">
            </div>
            <button type="button" onclick="togglePassword('password_confirmation', 'eyeIconConfirm')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                <iconify-icon id="eyeIconConfirm" icon="mdi:eye-outline" width="20" height="20"></iconify-icon>
            </button>
        </div>
        <p id="password_confirm_error" class="text-red-500 text-sm mt-1"></p>


        <button
            type="submit"
            class="bg-blue-600 hover:bg-blue-700 transition-colors text-white font-semibold px-4 py-3 rounded-lg w-full">
            Daftar
        </button>
        <div class="text-center">
            <p class="mt-4 text-sm text-gray-700">Sudah punya akun?</p>
            <a href="{{ route('login') }}" class="text-blue-500 font-bold hover:underline">Masuk di sini</a>
        </div>
    </form>

</div>

<script>
    function togglePassword(fieldId, iconId) {
        const input = document.getElementById(fieldId);
        const iconElement = document.getElementById(iconId); // Menggunakan nama variabel yang lebih jelas
        if (input.type === 'password') {
            input.type = 'text';
            iconElement.setAttribute('icon', 'mdi:eye-off-outline');
        } else {
            input.type = 'password';
            iconElement.setAttribute('icon', 'mdi:eye-outline');
        }
    }

    function validatePasswordConfirmation() {
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');
        const errorElement = document.getElementById('password_confirm_error');

        if (passwordInput.value !== passwordConfirmationInput.value) {
            passwordConfirmationInput.setCustomValidity("Konfirmasi kata sandi tidak cocok dengan kata sandi.");
            errorElement.textContent = "Konfirmasi kata sandi tidak cocok dengan kata sandi.";
        } else {
            passwordConfirmationInput.setCustomValidity('');
            errorElement.textContent = '';
        }
    }

    // Tambahkan event listener untuk memvalidasi saat form disubmit (opsional, karena oninput sudah menangani)
    // document.getElementById('registerForm').addEventListener('submit', function(event) {
    //     validatePasswordConfirmation();
    //     // Jika ada custom validity, form tidak akan submit
    //     if (!this.checkValidity()) {
    //         event.preventDefault();
    //     }
    // });

</script>

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

@endsection