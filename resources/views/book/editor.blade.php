<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Editor</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    {{-- React Fast Refresh preamble for Vite (required in dev when using Blade instead of index.html) --}}
    @viteReactRefresh
    @vite(['resources/css/app.css','resources/js/app.js'])

    <!-- Optional: Turn.js via CDN for future flipbook preview -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/turn.js@4/turn.min.js"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div id="book-editor-root" class="h-screen"></div>
</body>
</html>
