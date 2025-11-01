@extends('layouts.app')

@section('content')
<div class="container text-center mt-5">
    <h2>Pembayaran Berhasil!</h2>
    <p>Terima kasih, pembayaran Anda telah berhasil diproses.</p>
    <p>Anda akan diarahkan kembali ke halaman pembayaran dalam <span id="countdown">5</span> detik.</p>

    <a href="/bayar-iuran" class="btn btn-primary mt-3">Kembali Sekarang</a>
</div>

<script>
    let seconds = 5;
    const countdown = document.getElementById('countdown');

    const interval = setInterval(() => {
        seconds--;
        countdown.textContent = seconds;

        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = "/bayar-iuran";
        }
    }, 1000);
</script>
@endsection
