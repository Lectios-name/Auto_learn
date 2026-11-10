<x-app-layout>
    <x-page-title title="Мои курсы" subtitle="Все курсы, на которые вы записаны" />

    @if ($enrollments->isEmpty())
        <x-card class="text-center">
            <p class="text-gray-500">Вы пока не записаны ни на один курс.</p>
            <x-btn href="{{ route('catalog') }}" class="mt-4">Перейти в каталог</x-btn>
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($enrollments as $enrollment)
                @php
                    $course = $enrollment->course;
                    $nextLesson = $enrollment->nextLesson();
                @endphp

                <x-card padding="p-0" class="flex flex-col">
                    @if ($course->cover_path)
                        <img src="{{ asset('storage/'.$course->cover_path) }}" alt="{{ $course->title }}"
                             class="h-36 w-full rounded-t-xl object-cover">
                    @else
                        <div class="flex h-36 items-center justify-center rounded-t-xl bg-gradient-to-br from-primary-400 to-primary-600">
                            <span class="text-3xl font-extrabold text-white/80">{{ mb_substr($course->title, 0, 1) }}</span>
                        </div>
                    @endif

                    <div class="flex flex-1 flex-col p-5">
                        <a href="{{ route('courses.show', $course) }}"
                           class="font-semibold text-gray-900 hover:text-primary-600">{{ $course->title }}</a>

                        <p class="mt-1 text-sm text-gray-500">{{ $course->author->name }}</p>

                        <div class="mt-4">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Прогресс</span>
                                <span class="font-semibold text-primary-600">{{ $enrollment->progress_percent }}%</span>
                            </div>
                            <div class="mt-1 h-2 rounded-full bg-gray-100">
                                <div class="h-2 rounded-full bg-primary-500 transition-all"
                                     style="width: {{ $enrollment->progress_percent }}%"></div>
                            </div>
                        </div>

                        @if ($nextLesson)
                            <x-btn href="{{ route('lessons.show', $nextLesson) }}" size="sm" class="mt-4 w-full">
                                Продолжить: {{ $nextLesson->title }}
                            </x-btn>
                        @else
                            <div class="mt-4 rounded-lg bg-primary-50 px-3 py-2 text-center text-sm font-medium text-primary-700">
                                🎉 Курс пройден!
                            </div>
                        @endif
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</x-app-layout>