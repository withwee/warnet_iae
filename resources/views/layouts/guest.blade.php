<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: url('{{ asset('images/Background.png') }}') no-repeat center center; background-size: cover;">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
            
            <div class="relative z-10">
                <a href="/">
                    <div class="text-center mb-4">
                        <h1 class="text-4xl font-bold">
                            <span class="text-blue-600">Warga</span><span class="text-blue-400">Net</span>
                        </h1>
                    </div>
                </a>
            </div>

            <div class="relative z-10 w-full sm:max-w-md mt-6 px-6 py-4 bg-white/95 backdrop-blur-md shadow-xl overflow-hidden sm:rounded-lg">
                <!-- Back Button -->
                <div class="mb-4">
                    <a href="/" class="inline-flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
                
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
