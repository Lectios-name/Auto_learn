<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>67 | Autolearn</title>
    @vite(['resources/css/app.css'])
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-3deg); }
            50%      { transform: translateY(-24px) rotate(3deg); }
        }
        @keyframes glow {
            0%, 100% { text-shadow: 0 0 24px rgba(51, 184, 104, .55); }
            50%      { text-shadow: 0 0 72px rgba(51, 184, 104, .95); }
        }
        .six-seven {
            animation: float 3s ease-in-out infinite, glow 2s ease-in-out infinite;
        }
    </style>
</head>
<body class="flex min-h-screen flex-col items-center justify-center bg-gray-950 px-4 font-sans antialiased">
    <div class="six-seven select-none text-[10rem] font-extrabold leading-none text-primary-500 sm:text-[16rem]">
        67
    </div>

    <p class="mt-6 text-center text-lg font-medium text-primary-300">
        Вы нашли главную пасхалку Autolearn 🎉
    </p>
    <p class="mt-2 text-center text-sm text-gray-500">
        67 — просто число. Но теперь оно ещё и ваше.
    </p>

    <p class="mt-6 rounded-full bg-gray-900 px-4 py-1.5 text-xs text-gray-400 ring-1 ring-gray-800">
        Вы зашли на эту страницу: {{ $count }} {{ Str::plural('раз', $count) }}
    </p>

    <a href="{{ url('/') }}"
       class="mt-8 rounded-lg bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-600">
        Вернуться к учёбе
    </a>
</body>
</html>