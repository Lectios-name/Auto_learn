<x-app-layout>
    <x-page-title title="Мои курсы" subtitle="Управление курсами, структурой и публикацией">
        <x-slot name="actions">
            <x-btn href="{{ route('author.courses.create') }}">+ Создать курс</x-btn>
        </x-slot>
    </x-page-title>

    @if ($courses->isEmpty())
        <x-card class="text-center">
            <p class="text-gray-500">У вас пока нет курсов. Создайте первый — это займёт минуту.</p>
            <x-btn href="{{ route('author.courses.create') }}" class="mt-4">Создать первый курс</x-btn>
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($courses as $course)
                @php
                    $status = [
                        'draft' => ['label' => 'Черновик', 'color' => 'gray'],
                        'on_moderation' => ['label' => 'На модерации', 'color' => 'yellow'],
                        'published' => ['label' => 'Опубликован', 'color' => 'green'],
                        'rejected' => ['label' => 'Отклонён', 'color' => 'red'],
                    ][$course->status];
                @endphp

                <x-card padding="p-0">
                    @if ($course->cover_path)
                        <img src="{{ asset('storage/'.$course->cover_path) }}" alt="{{ $course->title }}"
                            class="h-28 w-full rounded-t-xl object-cover">
                    @else
                        <div class="h-28 rounded-t-xl bg-gradient-to-br from-primary-400 to-primary-600"></div>
                    @endif
                
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-2">
                            <h3 class="font-semibold text-gray-900">{{ $course->title }}</h3>
                            <x-badge :color="$status['color']">{{ $status['label'] }}</x-badge>
                        </div>

                        <p class="mt-2 line-clamp-2 text-sm text-gray-500">{{ $course->description }}</p>

                        <div class="mt-4 flex items-center gap-4 text-xs text-gray-400">
                            <span>Секций: {{ $course->sections_count }}</span>
                            <span>Уроков: {{ $course->lessons_count }}</span>
                            <span>{{ $course->price ? $course->price.' ₽' : 'Бесплатно' }}</span>
                        </div>

                        <div class="mt-4 flex gap-2">
                            <x-btn href="{{ route('author.courses.edit', $course) }}" size="sm" variant="secondary" class="flex-1">Изменить</x-btn>
                            <x-btn href="{{ route('author.courses.builder', $course) }}" size="sm" variant="ghost" class="flex-1">Конструктор</x-btn>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</x-app-layout>