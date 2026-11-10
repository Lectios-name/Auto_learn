<x-app-layout>
    @php
        $activeFilters = collect(['q', 'format', 'rating', 'duration', 'tag'])
                ->filter(fn ($key) => filled(request($key)))->count()
            + count((array) request('access', []))
            + count((array) request('level', []));
    @endphp

    <div x-data="{ filtersOpen: {{ $activeFilters > 0 ? 'true' : 'false' }} }">
        <x-page-title title="Каталог курсов" subtitle="Умный поиск и многокритериальные фильтры">
            <x-slot name="actions">
                <button @click="filtersOpen = !filtersOpen"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                    Фильтры
                    @if ($activeFilters > 0)
                        <span class="rounded-full bg-primary-500 px-2 py-0.5 text-xs font-bold text-white">{{ $activeFilters }}</span>
                    @endif
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                         class="h-4 w-4 transition-transform" :class="filtersOpen ? 'rotate-180' : ''">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </x-slot>
        </x-page-title>

        <div class="grid grid-cols-1 gap-8" :class="filtersOpen ? 'lg:grid-cols-4' : 'lg:grid-cols-1'">
            {{-- Панель фильтров (сворачиваемая) --}}
            <aside x-show="filtersOpen" x-cloak x-transition.opacity.duration.200ms>
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
            <div :class="filtersOpen ? 'lg:col-span-3' : 'lg:col-span-1'">
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
    </div>
</x-app-layout>