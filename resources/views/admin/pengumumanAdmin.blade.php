@extends('layouts.app')
@section('content')

<div class="max-w-screen space-y-6">

    {{-- Buat Pengumuman --}}
    <h2 class="text-2xl font-bold text-gray-800">Buat Pengumuman</h2>
    <div class="bg-white p-6 rounded-xl shadow space-y-6">
        <form method="POST" action="{{ route('pengumuman.store') }}" class="space-y-4" id="formPengumuman">
            @csrf
            <div>
                <label class="block text-base font-semibold mb-1 text-gray-700">Judul Pengumuman</label>
                <input type="text" name="judulPengumuman" id="judulPengumuman" minlength="5"
                       placeholder="Ketik judul pengumuman yang mau dibuat di sini ..."
                       class="w-full border border-gray-300 rounded-full px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800" required>
            </div>
            <div>
                <label class="block text-base font-semibold mb-1 text-gray-700">Isi Pengumuman</label>
                <textarea name="isiPengumuman" id="isiPengumuman" minlength="10"
                          placeholder="Ketik isi pengumuman yang mau dibuat di sini ..."
                          class="w-full border border-gray-300 rounded-2xl px-5 py-3 h-40 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800" required></textarea>
            </div>
            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="pengumuman_khusus" value="1" class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="ml-2 text-gray-700">Apakah ingin dijadikan pengumuman khusus?</span>
                </label>
            </div>
            <div class="text-center">
                <button type="submit" id="btnPublikasi"
                        class="bg-gray-400 text-white font-semibold px-32 py-3 rounded-full transition duration-300 cursor-not-allowed"
                        disabled>Publikasikan</button>
            </div>
        </form>
    </div>

    {{-- Daftar Pengumuman --}}
    <h1 class="text-2xl font-bold mb-2">Daftar Pengumuman</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @forelse($pengumumans as $p)
        <div class="bg-white p-6 rounded-xl shadow space-y-2">
            <h2 class="text-lg font-bold text-gray-900">
                {{ $p->judulPengumuman }}
                @if($p->pengumuman_khusus)
                    <span class="ml-2 px-5 py-1 bg-yellow-400 text-white rounded-full text-xs align-middle">KHUSUS</span>
                @endif
            </h2>
            <p class="text-gray-700 mt-1">{{ $p->isiPengumuman }}</p>

            <div class="flex items-center justify-between mt-4">
                <span class="bg-blue-600 text-white font-reguler px-12 py-1 text-sm rounded-full shadow border border-blue-200">
                    {{ $p->created_at->format('d M Y') }}
                </span>
                <div class="space-x-2">
                    {{-- Tombol Hapus --}}
                    <form action="{{ route('pengumuman.destroy', $p->id) }}" method="POST" class="inline-block"
                          onsubmit="return confirm('Yakin ingin menghapus pengumuman ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-500 hover:bg-red-600 text-white font-reguler px-9 py-1.5 rounded-full text-sm">Hapus</button>
                    </form>
                    {{-- Tombol Khusus --}}
                    <form action="{{ route('pengumuman.toggleKhusus', $p->id) }}" method="POST" class="inline-block">
                        @csrf
                        <button type="submit"
                                class="bg-purple-500 hover:bg-purple-600 text-white font-reguler px-6 py-1.5 rounded-full text-sm">
                            {{ $p->pengumuman_khusus ? 'Ubah ke Biasa' : 'Jadikan Khusus' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <p class="text-gray-600">Belum ada pengumuman.</p>
    @endforelse
</div>

<script>
    const judulInput = document.getElementById('judulPengumuman');
    const isiInput = document.getElementById('isiPengumuman');
    const submitBtn = document.getElementById('btnPublikasi');

    function toggleButton() {
        const judulValid = judulInput.value.trim().length >= 3;
        const isiValid = isiInput.value.trim().length >= 10;

        submitBtn.disabled = !(judulValid && isiValid);

        if (submitBtn.disabled) {
            submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
            submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
        } else {
            submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700', 'cursor-pointer');
        }
    }

    judulInput.addEventListener('input', toggleButton);
    isiInput.addEventListener('input', toggleButton);
</script>

@endsection
