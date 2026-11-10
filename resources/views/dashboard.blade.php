<x-app-layout>
    <x-page-title title="Дашборд" :subtitle="'С возвращением, '.auth()->user()->name.'!'" />

    {{-- ================= СТУДЕНТ ================= --}}
    @if ($role === 'student')
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-card>
                <p class="text-sm text-gray-500">Курсов в обучении</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['courses_in_progress'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Курсов завершено</p>
                <p class="mt-1 text-3xl font-bold text-primary-600">{{ $stats['courses_completed'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Уроков пройдено</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['lessons_completed'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Средний балл тестов</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['avg_test_score'] ?? '—' }}</p>
            </x-card>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <x-card class="lg:col-span-2">
                <x-slot name="header">Мои курсы</x-slot>

                @forelse ($stats['enrollments'] as $enrollment)
                    <div class="border-t border-gray-100 py-3 first:border-t-0 first:pt-0">
                        <div class="flex items-center justify-between gap-3">
                            <a href="{{ route('courses.show', $enrollment->course) }}"
                               class="truncate text-sm font-medium text-gray-800 hover:text-primary-600">
                                {{ $enrollment->course->title }}
                            </a>
                            <span class="text-sm font-semibold text-primary-600">{{ $enrollment->progress_percent }}%</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-gray-100">
                            <div class="h-2 rounded-full bg-primary-500 transition-all"
                                 style="width: {{ $enrollment->progress_percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">
                        Вы ещё не зачислены на курсы.
                        <a href="{{ route('catalog') }}" class="font-medium text-primary-600 hover:underline">Выбрать в каталоге</a>
                    </p>
                @endforelse
            </x-card>

            <x-card>
                <x-slot name="header">Быстрые действия</x-slot>
                <div class="space-y-3">
                    <x-btn href="{{ route('student.my-courses') }}" class="w-full">Мои курсы</x-btn>
                    <x-btn href="{{ route('catalog') }}" variant="secondary" class="w-full">Каталог курсов</x-btn>
                </div>
            </x-card>
        </div>

    {{-- ================= АВТОР ================= --}}
    @elseif ($role === 'author')
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-card>
                <p class="text-sm text-gray-500">Моих курсов</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['courses_count'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Опубликовано</p>
                <p class="mt-1 text-3xl font-bold text-primary-600">{{ $stats['published_count'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Студентов на курсах</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['students_count'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Уроков создано</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['lessons_count'] }}</p>
            </x-card>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <x-card class="lg:col-span-2">
                <x-slot name="header">Последние курсы</x-slot>

                @forelse ($stats['courses'] as $course)
                    <div class="flex items-center justify-between gap-3 border-t border-gray-100 py-3 first:border-t-0 first:pt-0">
                        <a href="{{ route('author.courses.builder', $course) }}"
                           class="truncate text-sm font-medium text-gray-800 hover:text-primary-600">
                            {{ $course->title }}
                        </a>
                        <x-badge :color="['draft' => 'gray', 'on_moderation' => 'yellow', 'published' => 'green', 'rejected' => 'red'][$course->status]">
                            {{ ['draft' => 'Черновик', 'on_moderation' => 'На модерации', 'published' => 'Опубликован', 'rejected' => 'Отклонён'][$course->status] }}
                        </x-badge>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Курсов пока нет.</p>
                @endforelse
            </x-card>

            <x-card>
                <x-slot name="header">Метрики</x-slot>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Средний рейтинг курсов</span>
                        <span class="font-semibold text-gray-900">★ {{ number_format($stats['avg_rating'], 1) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Доход</span>
                        <span class="font-semibold text-gray-400">— (Этап 5)</span>
                    </div>
                </div>
                <x-btn href="{{ route('author.courses.create') }}" class="mt-4 w-full">+ Создать курс</x-btn>
            </x-card>
        </div>

    {{-- ================= АДМИН ================= --}}
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
            <x-card>
                <p class="text-sm text-gray-500">Пользователей</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['users_count'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Всего курсов</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['courses_count'] }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">На модерации</p>
                <p class="mt-1 text-3xl font-bold {{ $stats['on_moderation'] > 0 ? 'text-yellow-500' : 'text-gray-900' }}">
                    {{ $stats['on_moderation'] }}
                </p>
            </x-card>
            <x-card>
                <p class="text-sm text-gray-500">Опубликовано</p>
                <p class="mt-1 text-3xl font-bold text-primary-600">{{ $stats['published'] }}</p>
            </x-card>
        </div>
    @endif
</x-app-layout>