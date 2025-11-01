@extends('layouts.app')

@section("content")
<div class="container mx-auto p-0">

    <!-- Card -->
    <div class="bg-white rounded-2xl p-6 shadow">

         <!-- Tombol Kembali -->
         <a href="{{ route('pengumuman') }}"
           class="border border-blue-500 text-gray-500 px-4 py-1 rounded-full text-sm mb-6 inline-block hover:bg-blue-500 hover:text-white transition">
            &lt; Kembali
        </a>

        <!-- Flex Layout -->
        <div class="flex items-start gap-10">

            <!-- Foto Profil dan Nama -->
            <div class="flex flex-col items-center w-1/3">
                <img src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/profile.png') }}" 
                     alt="Profile" 
                     class="w-32 h-32 rounded-full object-cover">
                <h2 class="mt-4 font-bold text-xl text-center">{{ $user->name }}</h2>
            </div>

            <!-- Detail User -->
            <div class="w-2/3 flex flex-col gap-2">
                <div>
                    <p class="font-bold text-sm">Nama Lengkap</p>
                    <p class="text-gray-500">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="font-bold text-sm">Email</p>
                    <p class="text-gray-500">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="font-bold text-sm">Nomor Induk Kependudukan (NIK)</p>
                    <p class="text-gray-500">{{ $user->nik }}</p>
                </div>
                <div>
                    <p class="font-bold text-sm">Nomor Kartu Keluarga</p>
                    <p class="text-gray-500">{{ $user->no_kk }}</p>
                </div>
                <div>
                    <p class="font-bold text-sm">Nomor Handphone</p>
                    <p class="text-gray-500">{{ $user->phone }}</p>
                </div>

                <!-- Garis -->
                <hr class="my-4 border-gray-300">

                <!-- Jumlah Anggota Keluarga -->
                <div>
                    <p class="text-sm font-bold text-gray-400 mb-2">Jumlah Anggota Keluarga (Termasuk Diri Sendiri)</p>
                    <div class="flex gap-20 mt-2">
                        <div>
                            <p class="font-bold text-sm">Laki-laki</p>
                            <p class="text-gray-500">{{ $user->jumlah_LK ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="font-bold text-sm">Perempuan</p>
                            <p class="text-gray-500">{{ $user->jumlah_PR ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Edit Profile -->
                <div class="mt-6">
                    <a href="{{ route('profile.edit') }}" 
                       class="border border-blue-500 text-gray-500 font-semibold px-6 py-2 rounded-full hover:bg-blue-500 hover:text-white transition">
                        Edit Profile
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
