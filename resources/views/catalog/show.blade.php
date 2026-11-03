<x-app-layout>
    <x-slot name="title">{{ $course->title }}</x-slot>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <x-card>
                <div class="flex flex-wrap gap-2">
                    @foreach ($course->tags as $tag)
                        <a href="{{ route('catalog', ['tag' => $tag->slug]) }}">
                            <x-badge color="green">#{{ $tag->name }}</x-badge>
                        </a>
                    @endforeach
                </div>

                <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
                <p class="mt-2 text-gray-600">{{ $course->description }}</p>

                <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                        <span class="text-yellow-500">★</span>
                        <span class="font-semibold text-gray-800">{{ number_format($course->rating_avg, 1) }}</span>
                        ({{ $course->rating_count }} отзывов)
                    </span>
                    <span>⏱ {{ round($course->duration_minutes / 60, 1) }} ч</span>
                    <span>🎯 {{ ['beginner' => 'Начинающий', 'intermediate' => 'Средний', 'advanced' => 'Продвинутый'][$course->level] }}</span>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">Программа курса</x-slot>
                <div class="space-y-5">
                    @foreach ($course->sections as $section)
                        <div>
                            <p class="font-semibold text-gray-800">{{ $section->title }}</p>
                            <ul class="mt-2 space-y-1">
                                @foreach ($section->lessons as $lesson)
                                    <li class="flex items-center justify-between rounded-lg px-3 py-2 text-sm odd:bg-gray-50">
                                        <span class="flex items-center gap-2 text-gray-600">
                                            {{ ['video' => '🎬', 'text' => '📄', 'test' => '📝', 'task' => '💻'][$lesson->type] }}
                                            {{ $lesson->title }}
                                            @if ($lesson->is_free_preview)
                                                <x-badge color="green">Открытый</x-badge>
                                            @endif
                                        </span>
                                        <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }} мин</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>

        <div class="space-y-6">
            <x-card>
                <p class="text-3xl font-extrabold text-gray-900">
                    {{ $course->access_type === 'free' ? 'Бесплатно' : ($course->price ? number_format($course->price, 0, ',', ' ') .' ₽' : 'Подписка') }}
                </p>
                <div class="mt-4 space-y-3">
                    <x-btn href="#" class="w-full">Купить курс</x-btn>
                    <p class="text-center text-xs text-gray-400">Покупки и подписка подключаются на Этапе 5 (ЮMoney)</p>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">Автор курса</x-slot>
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-500 font-bold text-white">
                        {{ mb_substr($course->author->name, 0, 1) }}
                    </span>
                    <div>
                        <p class="font-medium text-gray-900">{{ $course->author->name }}</p>
                        <p class="text-xs text-gray-400">Преподаватель Autolearn</p>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>