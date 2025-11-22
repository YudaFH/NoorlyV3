<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>@yield('title', config('app.name', 'Noorly'))</title>

    {{-- Favicon Noorly --}}
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logo_noorly.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo/logo_noorly.png') }}">


    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
</head>
<body class="overflow-x-hidden bg-white text-slate-900">

    
    {{-- Navbar umum (bukan yang sidebar dashboard) --}}
    @include('partials.navbar')

    <main class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    @include('partials.footer')

    @livewireScripts
</body>
</html>
