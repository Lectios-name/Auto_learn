<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — Страница не найдена | Autolearn</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-4 font-sans antialiased">
    <a href="{{ url('/') }}" class="mb-8 flex items-center gap-2.5">
        <img src="{{ asset('images/logo.png') }}" alt="" class="h-10 w-10 object-contain">
        <span class="text-xl font-extrabold tracking-tight text-gray-900">Autolearn</span>
    </a>

    <p class="text-8xl font-extrabold text-primary-500">404</p>
    <h1 class="mt-4 text-2xl font-bold text-gray-900">Страница не найдена 🔍</h1>
    <p class="mt-2 max-w-md text-center text-gray-500">
        Возможно, страница была удалена, перемещена или её никогда не существовало.
        Зато у нас точно есть каталог курсов!
    </p>

    <div class="mt-8 flex gap-3">
        <a href="{{ url('/') }}" class="rounded-lg bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-600">На главную</a>
        <a href="{{ route('catalog') }}" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">В каталог</a>
    </div>
</body>
</html>