<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WargaNet - Platform Digital RT</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Figtree', sans-serif;
        }
        .landing-container {
            min-height: 100vh;
            background: url('{{ asset('images/Background.png') }}') no-repeat center center;
            background-size: cover;
            position: relative;
            overflow: hidden;
        }
        .landing-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.1);
            z-index: 1;
        }
        .content-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }
        .logo-text {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        .logo-warga {
            color: #2563eb;
        }
        .logo-net {
            color: #60a5fa;
        }
        .subtitle {
            color: #2563eb;
            font-size: 1.25rem;
            text-align: center;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .welcome-text {
            color: #0ea5e9;
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 500;
        }
        .description-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 1rem;
            max-width: 700px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .description-text {
            color: #374151;
            text-align: center;
            line-height: 1.8;
            font-size: 1rem;
        }
        .button-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
            padding: 0.875rem 3rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.3);
        }
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(37, 99, 235, 0.4);
        }
        .footer {
            position: absolute;
            bottom: 1rem;
            text-align: center;
            width: 100%;
            color: #6b7280;
            font-size: 0.875rem;
        }
        .footer a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .logo-text {
                font-size: 2.5rem;
            }
            .subtitle {
                font-size: 1rem;
            }
            .button-group {
                flex-direction: column;
                width: 100%;
            }
            .btn-primary {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <!-- Header Logo -->
        <div style="position: absolute; top: 1.5rem; left: 2rem; z-index: 20;">
            <h1 class="text-2xl font-bold">
                <span class="logo-warga">Warga</span><span class="logo-net">Net</span>
            </h1>
        </div>

        <!-- Main Content -->
        <div class="content-wrapper">
            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <h1 class="logo-text">
                    <span class="logo-warga">Warga</span><span class="logo-net">Net</span>
                </h1>
                <p class="subtitle">Satu Platform Digital, Satu Komunitas yang Terhubung</p>
                <p class="welcome-text">Selamat Datang Warga Desa Konoha!</p>
            </div>

            <!-- Description Box -->
            <div class="description-box">
                <p class="description-text">
                    Kini, urusan warga jadi lebih mudah, cepat, dan transparan. Mulai dari informasi penting, pembayaran iuran, hingga diskusi warga semua terintegrasi dalam satu sistem!
                </p>
            </div>

            <!-- Login & Register Buttons -->
            <div class="button-group">
                <a href="{{ route('login') }}" class="btn-primary">Masuk</a>
                <a href="{{ route('register') }}" class="btn-primary">Daftar</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Copyright {{ date('Y') }} <a href="/">WargaNet</a></p>
        </div>
    </div>
</body>
</html>
