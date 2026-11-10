<x-app-layout>
    <x-slot name="title">{{ $lesson->title }} — {{ $lesson->section->course->title }}</x-slot>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        {{-- Sidebar: структура курса --}}
        <aside class="lg:col-span-1">
            <x-card padding="p-0">
                <div class="border-b border-gray-100 px-4 py-3">
                    <a href="{{ route('courses.show', $lesson->section->course) }}"
                        class="text-sm font-semibold text-gray-800 hover:text-primary-600">
                        ← {{ $lesson->section->course->title }}
                    </a>
                </div>

                <div class="max-h-[70vh] overflow-y-auto px-2 py-3">
                    @foreach ($lesson->section->course->sections as $section)
                        <div class="mb-3">
                            <p class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-400">
                                {{ $section->title }}
                            </p>
                            <ul class="mt-1 space-y-0.5">
                                @foreach ($section->lessons as $item)
                                    @php
                                        $isActive = $item->id === $lesson->id;
                                        $isDone = auth()->user()->lessonCompletions()->where('lesson_id', $item->id)->exists();
                                    @endphp
                                    <li>
                                        <a href="{{ route('lessons.show', $item) }}"
                                            class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm transition
                                                                  {{ $isActive ? 'bg-primary-50 text-primary-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                            <span class="flex h-5 w-5 items-center justify-center rounded-full text-xs">
                                                @if ($isDone)
                                                    <span class="text-primary-500">✓</span>
                                                @else
                                                    <span class="text-gray-300">○</span>
                                                @endif
                                            </span>
                                            <span class="flex-1 truncate">{{ $item->title }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            </x-card>
        </aside>

        {{-- Main content --}}
        <div class="space-y-6 lg:col-span-3">
            {{-- Контент урока --}}
            <x-card>
                <div class="flex items-center justify-between gap-4">
                    <h1 class="text-xl font-bold text-gray-900">{{ $lesson->title }}</h1>
                    <x-badge color="{{ $isCompleted ? 'green' : 'gray' }}">
                        {{ $isCompleted ? 'Завершён' : 'Не завершён' }}
                    </x-badge>
                </div>

                <div class="mt-4">
                    @if ($lesson->type === 'video')
                        {{-- Inline aspect-ratio гарантирует высоту 16:9 независимо от сборки CSS --}}
                        <div class="w-full overflow-hidden rounded-xl bg-black shadow-lg" style="aspect-ratio: 16 / 9;">
                            @if ($lesson->video_path)
                                <video controls class="h-full w-full" src="{{ asset('storage/' . $lesson->video_path) }}"></video>
                            @elseif ($lesson->isDirectVideoFile())
                                <video controls class="h-full w-full" src="{{ $lesson->video_url }}"></video>
                            @elseif ($embed = $lesson->embedUrl())
                                <iframe src="{{ $embed }}" class="h-full w-full" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            @else
                                <div class="flex h-full flex-col items-center justify-center gap-3 px-6 text-center">
                                    <p class="text-sm text-gray-300">
                                        Этот видеосервис запрещает встраивание плеера на сторонние сайты.
                                    </p>
                                    <a href="{{ $lesson->video_url }}" target="_blank" rel="noopener"
                                        class="rounded-lg bg-primary-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-600">
                                        ▶ Смотреть видео в новой вкладке
                                    </a>
                                </div>
                            @endif
                        </div>
                    @elseif ($lesson->type === 'text' && $lesson->content)
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($lesson->content)) !!}
                        </div>
                    @else
                        <div
                            class="rounded-xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500">
                            Контент урока будет добавлен в конструкторе (Этап 4: тесты и задачи).
                        </div>
                    @endif
                </div>

                {{-- Материалы для скачивания --}}
                @if ($lesson->materials->isNotEmpty())
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <p class="text-sm font-medium text-gray-700">Материалы для скачивания</p>
                        <ul class="mt-2 space-y-1">
                            @foreach ($lesson->materials as $material)
                                <li>
                                    <a href="{{ asset('storage/' . $material->path) }}" target="_blank"
                                        class="inline-flex items-center gap-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-primary-600 hover:bg-primary-50">
                                        📎 {{ $material->title }}
                                        <span class="text-xs text-gray-400">({{ round($material->size_bytes / 1024, 1) }}
                                            КБ)</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Кнопка завершения --}}
                <div class="mt-6 flex items-center gap-3 border-t border-gray-100 pt-4">
                    @if ($isCompleted)
                        <form method="POST" action="{{ route('lessons.uncomplete', $lesson) }}">
                            @csrf
                            <x-btn type="submit" variant="secondary">Снять отметку о завершении</x-btn>
                        </form>
                    @else
                        <form method="POST" action="{{ route('lessons.complete', $lesson) }}">
                            @csrf
                            <x-btn type="submit">Отметить урок завершённым</x-btn>
                        </form>
                    @endif

                    @if ($enrollment)
                        <span class="text-sm text-gray-500">
                            Прогресс курса: <span
                                class="font-semibold text-primary-600">{{ $enrollment->progress_percent }}%</span>
                        </span>
                    @endif
                </div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4">
                    @if ($prevLesson)
                        <x-btn href="{{ route('lessons.show', $prevLesson) }}" variant="secondary" size="sm">
                            ← {{ Str::limit($prevLesson->title, 30) }}
                        </x-btn>
                    @else
                        <span></span>
                    @endif

                    @if ($nextLesson)
                        <x-btn href="{{ route('lessons.show', $nextLesson) }}" size="sm">
                            Следующий урок: {{ Str::limit($nextLesson->title, 30) }} →
                        </x-btn>
                    @else
                        <x-btn href="{{ route('courses.show', $lesson->section->course) }}" size="sm">
                            🎉 Завершить курс
                        </x-btn>
                    @endif
                </div>
            </x-card>

            {{-- Заметки студента --}}
            @if ($enrollment)
                <x-card>
                    <x-slot name="header">Личные заметки</x-slot>

                    <form method="POST" action="{{ route('lessons.notes.save', $lesson) }}" class="space-y-3">
                        @csrf

                        <textarea name="content" rows="4"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Запишите ключевые моменты, вопросы или идеи...">{{ $note->content ?? '' }}</textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-1" />

                        <x-btn type="submit" size="sm">Сохранить заметку</x-btn>
                    </form>
                </x-card>
            @endif

            {{-- Комментарии (древовидные) --}}
            <x-card>
                <x-slot name="header">Вопросы и обсуждение ({{ $lesson->comments->count() }})</x-slot>

                @auth
                    <form method="POST" action="{{ route('lessons.comments.store', $lesson) }}" class="mb-6 space-y-3">
                        @csrf
                        <textarea name="content" rows="3"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                            placeholder="Задайте вопрос преподавателю или поделитесь мнением..."></textarea>
                        <x-input-error :messages="$errors->get('content')" class="mt-1" />
                        <x-btn type="submit" size="sm">Отправить комментарий</x-btn>
                    </form>
                @endauth

                @forelse ($comments as $comment)
                    <div class="border-t border-gray-100 py-4 first:border-t-0 first:pt-0">
                        @include('lessons.partials.comment', ['comment' => $comment, 'depth' => 0])
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Комментариев пока нет — будьте первым!</p>
                @endforelse
            </x-card>
        </div>
    </div>
</x-app-layout>