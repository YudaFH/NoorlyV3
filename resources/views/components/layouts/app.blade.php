<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ config('app.name', 'Noorly') }}</title>

    {{-- Favicon Noorly --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logo_noorly.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/logo_noorly.png') }}">

    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-slate-50 text-slate-900 antialiased">

    <div class="flex min-h-screen">
        {{-- Kolom kiri: kalau nanti mau ada navbar Livewire global, taruh di sini --}}
        {{-- <x-livewire-navbar /> --}}

        {{-- Kolom kanan: isi halaman (slot Livewire) --}}
        <main class="flex-1 overflow-auto">
            {{-- PERHATIKAN: tidak ada lagi div p-6 di sini --}}
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
