<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Pendaftaran Warga</h2>
            <p class="text-sm text-gray-600">Lengkapi data diri Anda</p>
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Masukkan nama lengkap" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- NIK -->
        <div class="mt-4">
            <x-input-label for="nik" :value="__('NIK (16 digit)')" />
            <x-text-input id="nik" class="block mt-1 w-full" type="text" name="nik" :value="old('nik')" required maxlength="16" pattern="[0-9]{16}" placeholder="Masukkan NIK 16 digit" />
            <x-input-error :messages="$errors->get('nik')" class="mt-2" />
        </div>

        <!-- No KK -->
        <div class="mt-4">
            <x-input-label for="no_kk" :value="__('Nomor KK (16 digit)')" />
            <x-text-input id="no_kk" class="block mt-1 w-full" type="text" name="no_kk" :value="old('no_kk')" required maxlength="16" pattern="[0-9]{16}" placeholder="Masukkan Nomor KK 16 digit" />
            <x-input-error :messages="$errors->get('no_kk')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Nomor Telepon')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" required maxlength="15" placeholder="Contoh: 081234567890" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="contoh@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Jumlah Keluarga -->
        <div class="grid grid-cols-2 gap-4 mt-4">
            <!-- Jumlah Laki-laki -->
            <div>
                <x-input-label for="jumlah_LK" :value="__('Jumlah Laki-laki')" />
                <x-text-input id="jumlah_LK" class="block mt-1 w-full" type="number" name="jumlah_LK" :value="old('jumlah_LK', 0)" required min="0" />
                <x-input-error :messages="$errors->get('jumlah_LK')" class="mt-2" />
            </div>

            <!-- Jumlah Perempuan -->
            <div>
                <x-input-label for="jumlah_PR" :value="__('Jumlah Perempuan')" />
                <x-text-input id="jumlah_PR" class="block mt-1 w-full" type="number" name="jumlah_PR" :value="old('jumlah_PR', 0)" required min="0" />
                <x-input-error :messages="$errors->get('jumlah_PR')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
