@props(['course'])

<x-card padding="p-0" class="flex flex-col">
    @if ($course->cover_path)
        <img src="{{ asset('storage/'.$course->cover_path) }}" alt="{{ $course->title }}"
             class="h-40 w-full rounded-t-xl object-cover">
    @else
        <div class="flex h-40 items-center justify-center rounded-t-xl bg-gradient-to-br from-primary-400 to-primary-600">
            <span class="text-4xl font-extrabold text-white/80">{{ mb_substr($course->title, 0, 1) }}</span>
        </div>
    @endif

    <div class="flex flex-1 flex-col p-5">
        <div class="flex flex-wrap items-center gap-2">
            <x-badge color="green">{{ ['beginner' => 'Начинающий', 'intermediate' => 'Средний', 'advanced' => 'Продвинутый'][$course->level] }}</x-badge>
            <x-badge color="gray">{{ ['free' => 'Бесплатный', 'paid' => 'Платный', 'subscription' => 'Подписка'][$course->access_type] }}</x-badge>
        </div>

        <a href="{{ route('courses.show', $course) }}"
           class="mt-3 font-semibold text-gray-900 hover:text-primary-600">{{ $course->title }}</a>

        <p class="mt-1 text-sm text-gray-500">{{ Str::limit($course->description, 90) }}</p>

        <div class="mt-3 flex items-center gap-2 text-sm">
            <span class="text-yellow-500">★</span>
            <span class="font-medium text-gray-800">{{ number_format($course->rating_avg, 1) }}</span>
            <span class="text-xs text-gray-400">({{ $course->rating_count }})</span>
            <span class="text-xs text-gray-400">· {{ round($course->duration_minutes / 60, 1) }} ч</span>
        </div>

        <div class="mt-auto flex items-center justify-between pt-4">
            <span class="text-sm text-gray-500">{{ $course->author->name }}</span>
            <span class="font-bold text-gray-900">
                {{ $course->access_type === 'free' ? 'Бесплатно' : ($course->price ? number_format($course->price, 0, ',', ' ') .' ₽' : 'Подписка') }}
            </span>
        </div>
    </div>
</x-card>