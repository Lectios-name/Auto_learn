<x-app-layout>
    <x-page-title :title="'Конструктор: '.$course->title"
                  subtitle="Структура курса: секции, уроки и скачиваемые материалы">
        <x-slot name="actions">
            <x-btn href="{{ route('author.courses.index') }}" variant="secondary" size="sm">← Мои курсы</x-btn>
        </x-slot>
    </x-page-title>

    @php
        $typeLabels = ['video' => 'Видео', 'text' => 'Текст', 'test' => 'Тест', 'task' => 'Задача'];
        $typeColors = ['video' => 'blue', 'text' => 'gray', 'test' => 'yellow', 'task' => 'green'];
    @endphp

    <div class="space-y-6">
        @forelse ($course->sections as $section)
            <x-card padding="p-0" x-data="{ editingSection: false }">
                {{-- Шапка секции --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 bg-gray-50/60 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col leading-none">
                            <form method="POST" action="{{ route('author.builder.sections.move', [$section, 'up']) }}">
                                @csrf
                                <button type="submit" title="Выше" class="px-1 text-gray-400 hover:text-primary-600">▲</button>
                            </form>
                            <form method="POST" action="{{ route('author.builder.sections.move', [$section, 'down']) }}">
                                @csrf
                                <button type="submit" title="Ниже" class="px-1 text-gray-400 hover:text-primary-600">▼</button>
                            </form>
                        </div>

                        <h3 class="text-base font-semibold text-gray-900" x-show="!editingSection">
                            {{ $section->title }}
                            <span class="ml-1 text-xs font-normal text-gray-400">
                                ({{ $section->lessons->count() }} {{ Str::plural('урок', $section->lessons->count()) }})
                            </span>
                        </h3>

                        <form x-show="editingSection" x-cloak method="POST"
                              action="{{ route('author.builder.sections.update', $section) }}"
                              class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <x-text-input name="title" :value="$section->title" required class="!py-1 text-sm" />
                            <x-btn type="submit" size="sm">Сохранить</x-btn>
                        </form>
                    </div>

                    <div class="flex items-center gap-2">
                        <x-btn size="sm" variant="ghost" @click="editingSection = !editingSection">Переименовать</x-btn>
                        <form method="POST" action="{{ route('author.builder.sections.destroy', $section) }}"
                              onsubmit="return confirm('Удалить секцию вместе со всеми уроками?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-700">Удалить</button>
                        </form>
                    </div>
                </div>

                {{-- Уроки секции --}}
                <div class="divide-y divide-gray-100">
                    @foreach ($section->lessons as $lesson)
                        <div class="px-5 py-3" x-data="{ open: false, lessonType: '{{ $lesson->type }}' }">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="flex flex-col leading-none">
                                        <form method="POST" action="{{ route('author.builder.lessons.move', [$lesson, 'up']) }}">
                                            @csrf
                                            <button type="submit" title="Выше" class="px-1 text-gray-300 hover:text-primary-600">▲</button>
                                        </form>
                                        <form method="POST" action="{{ route('author.builder.lessons.move', [$lesson, 'down']) }}">
                                            @csrf
                                            <button type="submit" title="Ниже" class="px-1 text-gray-300 hover:text-primary-600">▼</button>
                                        </form>
                                    </div>

                                    <button @click="open = !open"
                                            class="text-left text-sm font-medium text-gray-800 hover:text-primary-600">
                                        {{ $lesson->title }}
                                    </button>

                                    <x-badge :color="$typeColors[$lesson->type]">{{ $typeLabels[$lesson->type] }}</x-badge>

                                    @if ($lesson->is_free_preview)
                                        <x-badge color="green">Открытый урок</x-badge>
                                    @endif

                                    <span class="text-xs text-gray-400">{{ $lesson->duration_minutes }} мин</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <x-btn size="sm" variant="ghost" @click="open = !open">Редактировать</x-btn>
                                    <form method="POST" action="{{ route('author.builder.lessons.destroy', $lesson) }}"
                                          onsubmit="return confirm('Удалить урок?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-500 hover:text-red-700">Удалить</button>
                                    </form>
                                </div>
                            </div>

                            {{-- Редактор урока --}}
                            <div x-show="open" x-cloak class="mt-4 space-y-4 rounded-xl bg-gray-50 p-4">
                                <form method="POST" action="{{ route('author.builder.lessons.update', $lesson) }}" class="space-y-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div class="sm:col-span-2">
                                            <x-input-label :for="'lesson-title-'.$lesson->id" value="Название" />
                                            <x-text-input :id="'lesson-title-'.$lesson->id" name="title"
                                                          :value="$lesson->title" required class="mt-1 block w-full" />
                                        </div>
                                        <div>
                                            <x-input-label :for="'lesson-type-'.$lesson->id" value="Тип урока" />
                                            <select :id="'lesson-type-'.$lesson->id" name="type" x-model="lessonType"
                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                                                <option value="video">Видео</option>
                                                <option value="text">Текст</option>
                                                <option value="test">Тест</option>
                                                <option value="task">Задача</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div x-show="lessonType === 'video'">
                                        <x-input-label :for="'lesson-video-'.$lesson->id" value="Ссылка на видео (YouTube / Vimeo)" />
                                        <x-text-input :id="'lesson-video-'.$lesson->id" name="video_url"
                                                      :value="$lesson->video_url" class="mt-1 block w-full"
                                                      placeholder="https://youtube.com/watch?v=..." />
                                    </div>

                                    <div x-show="lessonType === 'text'">
                                        <x-input-label :for="'lesson-content-'.$lesson->id" value="Текст урока" />
                                        <textarea :id="'lesson-content-'.$lesson->id" name="content" rows="5"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">{{ $lesson->content }}</textarea>
                                    </div>

                                    <div x-show="lessonType === 'test' || lessonType === 'task'"
                                         class="rounded-lg border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500">
                                        Вопросы теста и условия задач настраиваются в редакторе тестов (Этап 4).
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <x-input-label :for="'lesson-duration-'.$lesson->id" value="Длительность, мин" />
                                            <x-text-input :id="'lesson-duration-'.$lesson->id" name="duration_minutes"
                                                          type="number" min="0" max="600"
                                                          :value="$lesson->duration_minutes" class="mt-1 block w-full" />
                                        </div>
                                        <div class="flex items-end pb-2">
                                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                                <input type="checkbox" name="is_free_preview" value="1"
                                                       @checked($lesson->is_free_preview)
                                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                                Открытый урок (бесплатный предпросмотр)
                                            </label>
                                        </div>
                                        <div class="flex items-end">
                                            <x-btn type="submit" size="sm">Сохранить урок</x-btn>
                                        </div>
                                    </div>
                                </form>

                                {{-- Материалы урока --}}
                                <div class="border-t border-gray-200 pt-4">
                                    <p class="mb-2 text-sm font-medium text-gray-700">Материалы для скачивания</p>

                                    @if ($lesson->materials->isNotEmpty())
                                        <ul class="mb-3 space-y-1">
                                            @foreach ($lesson->materials as $material)
                                                <li class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-sm ring-1 ring-gray-200">
                                                    <span class="truncate text-gray-700">
                                                        📎 {{ $material->title }}
                                                        <span class="text-xs text-gray-400">({{ round($material->size_bytes / 1024, 1) }} КБ)</span>
                                                    </span>
                                                    <form method="POST" action="{{ route('author.builder.materials.destroy', $material) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Удалить</button>
                                                    </form>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif

                                    <form method="POST" action="{{ route('author.builder.materials.store', $lesson) }}"
                                          enctype="multipart/form-data" class="flex flex-wrap items-center gap-2">
                                        @csrf
                                        <x-text-input name="title" placeholder="Название (необязательно)" class="!w-48" />
                                        <input type="file" name="file" required
                                               class="block w-full max-w-xs text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100" />
                                        <x-btn type="submit" size="sm" variant="secondary">Загрузить</x-btn>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Добавить урок --}}
                <div class="border-t border-gray-100 px-5 py-3" x-data="{ adding: false }">
                    <button @click="adding = !adding" class="text-sm font-medium text-primary-600 hover:text-primary-700">
                        + Добавить урок
                    </button>

                    <form x-show="adding" x-cloak method="POST"
                          action="{{ route('author.builder.lessons.store', $section) }}"
                          class="mt-3 flex flex-wrap items-center gap-3">
                        @csrf
                        <x-text-input name="title" placeholder="Название урока" required class="flex-1" />
                        <select name="type" class="rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="video">Видео</option>
                            <option value="text">Текст</option>
                            <option value="test">Тест</option>
                            <option value="task">Задача</option>
                        </select>
                        <x-btn type="submit" size="sm">Добавить</x-btn>
                    </form>
                </div>
            </x-card>
        @empty
            <x-card class="text-center">
                <p class="text-gray-500">В курсе пока нет секций. Добавьте первую ниже.</p>
            </x-card>
        @endforelse

        {{-- Добавить секцию --}}
        <x-card>
            <form method="POST" action="{{ route('author.builder.sections.store', $course) }}"
                  class="flex flex-wrap items-center gap-3">
                @csrf
                <x-text-input name="title" placeholder="Название новой секции (например, «Модуль 1: Введение»)" required class="flex-1" />
                <x-btn type="submit">Добавить секцию</x-btn>
            </form>
        </x-card>
    </div>
</x-app-layout>