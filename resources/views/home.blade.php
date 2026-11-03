<x-app-layout>
    <x-slot name="title">Главная</x-slot>

    <section class="py-16 text-center sm:py-24">
        <x-badge color="green">Платформа онлайн-обучения с AI-контролем знаний</x-badge>

        <h1 class="mx-auto mt-6 max-w-3xl text-4xl font-extrabold tracking-tight text-gray-900 sm:text-5xl">
            Учись у лучших.
            <span class="text-primary-500">Проверяй знания автоматически.</span>
        </h1>

        <p class="mx-auto mt-4 max-w-2xl text-lg text-gray-500">
            Autolearn — маркетплейс курсов с интерактивными уроками, тестами с таймером
            и автоматической проверкой эссе и кода нейросетью.
        </p>

        <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
            <x-btn href="{{ route('register') }}" size="lg">Начать обучение</x-btn>
            <x-btn href="{{ route('catalog') }}" variant="secondary" size="lg">Смотреть каталог</x-btn>
        </div>
    </section>

    {{-- Преимущества --}}
    <section class="grid gap-6 pb-8 sm:grid-cols-3">
        <x-card>
            <x-slot name="header">🎓 Курсы от практиков</x-slot>
            <p class="text-sm text-gray-500">
                Видеоуроки, скачиваемые материалы и пошаговые программы от авторов-экспертов.
            </p>
        </x-card>
        <x-card>
            <x-slot name="header">🤖 AI-проверка знаний</x-slot>
            <p class="text-sm text-gray-500">
                Тесты с таймером и случайной выборкой вопросов, оценка открытых ответов через YandexGPT / GigaChat.
            </p>
        </x-card>
        <x-card>
            <x-slot name="header">📈 Прозрачная аналитика</x-slot>
            <p class="text-sm text-gray-500">
                Дашборды прогресса для студентов, воронки прохождения и финансовые метрики для авторов.
            </p>
        </x-card>
    </section>
</x-app-layout>