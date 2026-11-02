<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title . ' — ' : '' }}Autolearn</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-12">
        <a href="{{ url('/') }}" class="mb-8 flex items-center justify-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="" class="h-14 w-14 object-contain">
            <span class="text-3xl font-extrabold tracking-tight text-gray-900">Autolearn</span>
        </a>
        <div class="w-full max-w-md">{{ $slot }}</div>
        <p class="mt-8 text-xs text-gray-400">© {{ date('Y') }} Autolearn</p>
    </div>
</body>

</html>