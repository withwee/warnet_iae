@extends('layouts.app')

@section('title', 'Bayar Iuran')

@section('content')


<div class="max-w-screen space-y-6">

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Bayar Iuran --}}
    {{-- <h2 class="text-2xl font-bold text-gray-800">Bayar Iuran</h2> --}}

    <div class="bg-white p-6 rounded-xl shadow space-y-6 mt-6">
        <!-- Search -->
        <form action="{{ route('iuran.cari') }}" method="GET" class="mb-6">
            <div class="flex items-center gap-2">
                <input type="text" name="no_kk" class="w-full border border-gray-300 rounded-full px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800" placeholder="Masukkan Nomor Kartu Keluarga" value="{{ request('no_kk') }}">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-full px-6 py-3"><i class="fas fa-search"></i> Cari</button>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">ID Iuran</th>
                        <th class="border border-gray-300 px-4 py-2">Nama</th>
                        <th class="border border-gray-300 px-4 py-2">Jenis Iuran</th>
                        <th class="border border-gray-300 px-4 py-2">Total Bayar</th>
                        <th class="border border-gray-300 px-4 py-2">Status</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($iurans) && count($iurans) > 0)
                    @foreach ($iurans as $iuran)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">#{{ $iuran->id_bayar }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $iuran->user->name ?? 'Tidak Ditemukan' }}</td>
                            <td class="border border-gray-300 px-4 py-2">{{ $iuran->jenis_iuran }}</td>
                            <td class="border border-gray-300 px-4 py-2">Rp {{ number_format($iuran->total_bayar, 0, ',', '.') }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                @if ($iuran->status === 'Sudah Bayar')
                                    <span class="bg-green-100 text-green-800 text-sm font-semibold px-4 py-2 rounded-full">
                                        Sudah Bayar
                                    </span>
                                @else
                                    <button id="pay-button-{{ $iuran->id_bayar }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold text-sm px-10 py-2 rounded-full transition" data-id="{{ $iuran->id_bayar }}">
                                        <i class="fas fa-wallet"></i> Bayar
                                    </button>
                                    <span id="payment-status-{{ $iuran->id_bayar }}" class="text-sm font-semibold ml-2"></span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="border border-gray-300 text-center px-4 py-4">Silahkan cari menggunakan nomor KK</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Buat Iuran --}}
    @if(auth()->user() && auth()->user()->role === 'admin')
    <h2 class="text-2xl font-bold text-gray-800">Buat Iuran</h2>

    <div class="bg-white p-6 rounded-xl shadow space-y-6">
        <form action="{{ route('iuran.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="jenis_iuran" class="block text-base font-semibold mb-1 text-gray-700">Jenis Iuran</label>
                <input type="text" name="jenis_iuran" id="jenis_iuran" class="w-full border border-gray-300 rounded-full px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800" required>
            </div>
            <div>
                <label for="total_bayar" class="block text-base font-semibold mb-1 text-gray-700">Total Bayar</label>
                <input type="number" name="total_bayar" id="total_bayar" class="w-full border border-gray-300 rounded-full px-5 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-800" required>
            </div>
            <div class="text-center">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-32 py-3 rounded-full transition duration-300">
                    Publikasikan Iuran
                </button>
            </div>
        </form>
    </div>
    @endif

</div>

@endsection

@push('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
document.querySelectorAll('button[id^="pay-button-"]').forEach(button => {
button.addEventListener('click', function () {
        const idBayar = this.getAttribute('data-id');
        console.log('Bayar button clicked', idBayar);
        const statusSpan = document.getElementById('payment-status-' + idBayar);
        statusSpan.textContent = 'Memproses pembayaran...';
        this.disabled = true;
        const currentButton = this;

        fetch('/pay/snap-token/' + idBayar, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mendapatkan Snap Token. Silakan coba lagi.');
                }
                return response.json();
            })
            .then(data => {
                console.log('Fetch response:', data);
                if (!data.snapToken) {
                    throw new Error('Snap Token tidak tersedia. Silakan coba lagi.');
                }

                snap.pay(data.snapToken, {
                    onSuccess: function(result) {
                        statusSpan.textContent = 'Pembayaran berhasil, memuat ulang...';
                        setTimeout(() => {
                            window.location.href = `/bayar-iuran/success/${result.order_id}`;
                        }, 1000);
                    },
                    onPending: function(result) {
                        statusSpan.textContent = 'Menunggu pembayaran...';
                        alert('Menunggu pembayaran...');
                    },
                    onError: function(result) {
                        statusSpan.textContent = 'Pembayaran gagal!';
                        alert('Pembayaran gagal!');
                        currentButton.disabled = false;
                    },
                    onClose: function() {
                        statusSpan.textContent = '';
                        alert('Anda menutup popup pembayaran.');
                        currentButton.disabled = false;
                    }
                });
            })
            .catch(error => {
                console.error('Fetch error:', error);
                statusSpan.textContent = '';
                alert(error.message);
                currentButton.disabled = false;
            });
    });
});
</script>
@endpush
