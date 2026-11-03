<x-app-layout>
    <x-page-title title="Каталог курсов" subtitle="Умный поиск и многокритериальные фильтры" />

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
        {{-- Фильтры --}}
        <aside>
            <x-card padding="p-5">
                <form method="GET" action="{{ route('catalog') }}" class="space-y-6">
                    <div>
                        <x-input-label for="q" value="Поиск" />
                        <x-text-input id="q" name="q" :value="request('q')" class="mt-1 block w-full"
                                      placeholder="Название или тег..." />
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-700">Тип доступа</p>
                        @foreach (['free' => 'Бесплатные', 'paid' => 'Платные', 'subscription' => 'По подписке'] as $value => $label)
                            <label class="flex items-center gap-2 py-1 text-sm text-gray-600">
                                <input type="checkbox" name="access[]" value="{{ $value }}"
                                       @checked(in_array($value, (array) request('access', [])))
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-700">Уровень</p>
                        @foreach (['beginner' => 'Начинающий', 'intermediate' => 'Средний', 'advanced' => 'Продвинутый'] as $value => $label)
                            <label class="flex items-center gap-2 py-1 text-sm text-gray-600">
                                <input type="checkbox" name="level[]" value="{{ $value }}"
                                       @checked(in_array($value, (array) request('level', [])))
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>

                    <div>
                        <x-input-label for="format" value="Формат" />
                        <select id="format" name="format"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Любой</option>
                            <option value="video" @selected(request('format') === 'video')>Видео-курс</option>
                            <option value="text" @selected(request('format') === 'text')>Текстовый</option>
                            <option value="practice" @selected(request('format') === 'practice')>Практикум</option>
                            <option value="mixed" @selected(request('format') === 'mixed')>Смешанный</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="rating" value="Рейтинг" />
                        <select id="rating" name="rating"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">Любой</option>
                            <option value="4" @selected(request('rating') === '4')>от 4.0</option>
                            <option value="4.5" @selected(request('rating') === '4.5')>от 4.5</option>
                        </select>
                    </div>

                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-700">Длительность</p>
                        @foreach (['short' => 'До 5 часов', 'medium' => '5–10 часов', 'long' => 'Более 10 часов'] as $value => $label)
                            <label class="flex items-center gap-2 py-1 text-sm text-gray-600">
                                <input type="radio" name="duration" value="{{ $value }}"
                                       @checked(request('duration') === $value)
                                       class="rounded-full border-gray-300 text-primary-600 focus:ring-primary-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>

                    <div class="flex gap-2">
                        <x-btn type="submit" size="sm" class="flex-1">Применить</x-btn>
                        <x-btn href="{{ route('catalog') }}" variant="secondary" size="sm">Сброс</x-btn>
                    </div>
                </form>
            </x-card>

            @if ($tags->isNotEmpty())
                <x-card padding="p-5" class="mt-6">
                    <p class="mb-3 text-sm font-medium text-gray-700">Популярные теги</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tags as $tag)
                            <a href="{{ route('catalog', ['tag' => $tag->slug]) }}"
                               class="rounded-full px-3 py-1 text-xs font-medium
                                      {{ request('tag') === $tag->slug
                                          ? 'bg-primary-500 text-white'
                                          : 'bg-gray-100 text-gray-600 hover:bg-primary-50 hover:text-primary-700' }}">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </x-card>
            @endif
        </aside>

        {{-- Результаты --}}
        <div class="lg:col-span-3">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-gray-500">
                    Найдено: <span class="font-semibold text-gray-800">{{ $courses->total() }}</span>
                </p>

                <form method="GET" action="{{ route('catalog') }}">
                    @foreach (request()->except(['sort', 'page']) as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $item)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select name="sort" onchange="this.form.submit()"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="newest" @selected(request('sort', 'newest') === 'newest')>Сначала новые</option>
                        <option value="rating" @selected(request('sort') === 'rating')>По рейтингу</option>
                        <option value="popular" @selected(request('sort') === 'popular')>По популярности</option>
                        <option value="price_asc" @selected(request('sort') === 'price_asc')>Сначала дешёвые</option>
                        <option value="price_desc" @selected(request('sort') === 'price_desc')>Сначала дорогие</option>
                    </select>
                </form>
            </div>

            @if ($courses->isEmpty())
                <x-card class="text-center">
                    <p class="text-gray-500">Ничего не найдено. Попробуйте сбросить фильтры.</p>
                </x-card>
            @else
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($courses as $course)
                        <x-course-card :course="$course" />
                    @endforeach
                </div>

                <div class="mt-8">{{ $courses->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>