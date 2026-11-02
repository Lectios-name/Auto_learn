<x-app-layout>
    <x-page-title title="Дашборд" :subtitle="'С возвращением, '.auth()->user()->name.'!'">
        <x-slot name="actions">
            <x-btn href="#">Перейти в каталог</x-btn>
        </x-slot>
    </x-page-title>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <x-card>
            <p class="text-sm text-gray-500">Курсов в обучении</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">0</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Средний балл тестов</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">—</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Пройдено уроков</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">0</p>
        </x-card>
        <x-card>
            <p class="text-sm text-gray-500">Статус</p>
            <div class="mt-2"><x-badge color="green">{{ auth()->user()->getRoleNames()->first() ?? 'student' }}</x-badge></div>
        </x-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <x-card class="lg:col-span-2">
            <x-slot name="header">Прогресс обучения</x-slot>
            <p class="text-sm text-gray-500">Здесь появятся радиальные и линейные диаграммы (Этап 5: Аналитика).</p>
        </x-card>
        <x-card>
            <x-slot name="header">Быстрые действия</x-slot>
            <div class="space-y-3">
                <x-btn href="#" class="w-full">Продолжить обучение</x-btn>
                <x-btn href="#" variant="secondary" class="w-full">Мои покупки</x-btn>
            </div>
        </x-card>
    </div>
</x-app-layout>