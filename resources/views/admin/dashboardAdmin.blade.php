@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Kelola Boxes --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-100 p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-bold mb-2">Kelola Pengumuman</h2>
            <p class="text-blue-700 text-sm mb-4">Kelola pengumuman RT dengan mudah</p>
            <a href="{{ route('admin.pengumuman') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors">Kelola</a>
        </div>
        <div class="bg-yellow-100 p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-bold mb-2">Kelola Acara</h2>
            <p class="text-yellow-700 text-sm mb-4">Atur dan kelola acara RT</p>
            <a href="{{ route('admin.kalender') }}" class="inline-block bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors">Kelola</a>
        </div>
        <div class="bg-green-100 p-6 rounded-xl shadow-md text-center">
            <h2 class="text-lg font-bold mb-2">Kelola Iuran</h2>
            <p class="text-green-700 text-sm mb-4">Kelola pembayaran iuran warga</p>
            <a href="{{ route('admin.bayar-iuran') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition-colors">Kelola</a>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="space-y-4">
        <h1 class="font-bold text-xl text-gray-800">Statistik</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div class="bg-blue-100 p-6 rounded-xl shadow-md text-center">
                <h2 class="text-base font-semibold mb-2 text-gray-700">Jumlah Pengumuman</h2>
                <p class="text-blue-700 text-4xl font-bold">{{ $jumlahPengumuman ?? 0 }}</p>
            </div>
            <div class="bg-yellow-100 p-6 rounded-xl shadow-md text-center">
                <h2 class="text-base font-semibold mb-2 text-gray-700">Jumlah Acara</h2>
                <p class="text-yellow-700 text-4xl font-bold">{{ $jumlahAcara ?? 0 }}</p>
            </div>
            <div class="bg-green-100 p-6 rounded-xl shadow-md text-center">
                <h2 class="text-base font-semibold mb-2 text-gray-700">Jumlah Iuran</h2>
                <p class="text-green-700 text-4xl font-bold">{{ $jumlahIuran ?? 0 }}</p>
            </div>
        </div>
    </div>

    {{-- Keuangan RT --}}
    <div class="space-y-4">
        <h1 class="font-bold text-xl text-gray-800">Keuangan RT</h1>

        <form action="{{ route('admin.pengeluaran.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-6 rounded-xl shadow-md">
            @csrf
            <div>
                <label for="description" class="block text-sm font-semibold mb-2 text-gray-700">Jenis Pengeluaran</label>
                <input type="text" name="description" id="description" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ketik jenis pengeluaran di sini..." required>
            </div>
            <div>
                <label for="amount" class="block text-sm font-semibold mb-2 text-gray-700">Nominal Pengeluaran</label>
                <input type="number" name="amount" id="amount" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Ketik nominal pengeluaran di sini..." required>
            </div>
            <div class="col-span-1 md:col-span-2 text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition-colors">Publikasikan</button>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white px-6 py-8 rounded-xl flex flex-col items-center justify-center shadow-md">
                <h1 class="text-blue-500 font-extrabold text-3xl mb-2">Rp {{ number_format($totalIuran, 0, ',', '.') }}</h1>
                <p class="font-semibold text-gray-600">Total Iuran Terkumpul</p>
            </div>
            <div class="bg-white px-6 py-8 rounded-xl flex flex-col items-center justify-center shadow-md">
                <h1 class="text-red-500 font-extrabold text-3xl mb-2">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</h1>
                <p class="font-semibold text-gray-600">Total Pengeluaran</p>
            </div>
            <div class="bg-white px-6 py-8 rounded-xl flex flex-col items-center justify-center shadow-md">
                <h1 class="text-blue-500 font-extrabold text-3xl mb-2">Rp {{ number_format($jumlahIuranTersisa, 0, ',', '.') }}</h1>
                <p class="font-semibold text-gray-600">Sisa Iuran</p>
            </div>
        </div>
    </div>
    {{-- Detail Pengeluaran --}}
    <div class="bg-white rounded-xl shadow-md p-6">
        <h2 class="text-lg font-bold mb-4 text-gray-800">Detail Pengeluaran</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-3 px-4 border font-semibold text-gray-700">ID Pengeluaran</th>
                        <th class="py-3 px-4 border font-semibold text-gray-700">Jenis Pengeluaran</th>
                        <th class="py-3 px-4 border font-semibold text-gray-700">Jumlah Pengeluaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengeluarans as $pengeluaran)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border text-center">#{{ $pengeluaran->id }}</td>
                            <td class="py-3 px-4 border">{{ $pengeluaran->description}}</td>
                            <td class="py-3 px-4 border text-right font-semibold">Rp {{ number_format($pengeluaran->amount, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 px-4 border text-center text-gray-500">Belum ada data pengeluaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection