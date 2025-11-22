<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <title>@yield('title', 'Noorly — Masuk')</title>

        {{-- Favicon Noorly --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logo_noorly.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/logo_noorly.png') }}">

    {{-- CSS utama --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <link rel="icon" type="image/png" href="{{ asset('images/icon/logo_favicon.png') }}">
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">

    

    {{-- Wrapper auth full height tanpa navbar/footer --}}
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        @yield('content')
    </div>

    @livewireScripts
</body>
</html>
