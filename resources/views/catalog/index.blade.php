<x-app-layout>
    @php
        $accessLabels = ['free' => 'Бесплатные', 'paid' => 'Платные', 'subscription' => 'По подписке'];
        $levelLabels = ['beginner' => 'Начинающий', 'intermediate' => 'Средний', 'advanced' => 'Продвинутый'];
        $formatLabels = ['video' => 'Видео-курс', 'text' => 'Текстовый', 'practice' => 'Практикум', 'mixed' => 'Смешанный'];
        $durationLabels = ['short' => 'До 5 часов', 'medium' => '5–10 часов', 'long' => 'Более 10 часов'];
        $ratingLabels = ['4' => 'от 4.0', '4.5' => 'от 4.5'];

        $selectClass = 'rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500';
    @endphp

    <x-page-title title="Каталог курсов" subtitle="Умный поиск и многокритериальные фильтры" />

    {{-- Панель фильтров-тулбар --}}
    <x-card padding="p-4" class="mb-6">
        <form method="GET" action="{{ route('catalog') }}" class="flex flex-wrap items-center gap-3">
            {{-- Поиск --}}
            <x-text-input name="q" :value="request('q')" placeholder="Поиск по названию или тегу..."
                          class="min-w-[220px] flex-1" />

            {{-- Критерии --}}
            <select name="access" onchange="this.form.submit()" class="{{ $selectClass }}">
                <option value="">Тип доступа: любой</option>
                @foreach ($accessLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('access') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="level" onchange="this.form.submit()" class="{{ $selectClass }}">
                <option value="">Уровень: любой</option>
                @foreach ($levelLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('level') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="format" onchange="this.form.submit()" class="{{ $selectClass }}">
                <option value="">Формат: любой</option>
                @foreach ($formatLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('format') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="rating" onchange="this.form.submit()" class="{{ $selectClass }}">
                <option value="">Рейтинг: любой</option>
                @foreach ($ratingLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('rating') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="duration" onchange="this.form.submit()" class="{{ $selectClass }}">
                <option value="">Длительность: любая</option>
                @foreach ($durationLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('duration') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="sort" onchange="this.form.submit()" class="{{ $selectClass }}">
                <option value="newest" @selected(request('sort', 'newest') === 'newest')>Сначала новые</option>
                <option value="rating" @selected(request('sort') === 'rating')>По рейтингу</option>
                <option value="popular" @selected(request('sort') === 'popular')>По популярности</option>
                <option value="price_asc" @selected(request('sort') === 'price_asc')>Сначала дешёвые</option>
                <option value="price_desc" @selected(request('sort') === 'price_desc')>Сначала дорогие</option>
            </select>

            <x-btn type="submit" size="sm">Найти</x-btn>
        </form>

        {{-- Чипсы активных фильтров --}}
        @if (request('q') || request('access') || request('level') || request('format') || request('rating') || request('duration') || request('tag'))
            <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-gray-100 pt-3">
                <span class="text-xs text-gray-400">Активные фильтры:</span>

                @if (request('q'))
                    <a href="{{ route('catalog', request()->except(['q', 'page'])) }}"
                       class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100">
                        Поиск: {{ request('q') }} ×
                    </a>
                @endif

                @if (request('access'))
                    <a href="{{ route('catalog', request()->except(['access', 'page'])) }}"
                       class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100">
                        {{ $accessLabels[request('access')] ?? request('access') }} ×
                    </a>
                @endif

                @if (request('level'))
                    <a href="{{ route('catalog', request()->except(['level', 'page'])) }}"
                       class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100">
                        {{ $levelLabels[request('level')] ?? request('level') }} ×
                    </a>
                @endif

                @if (request('format'))
                    <a href="{{ route('catalog', request()->except(['format', 'page'])) }}"
                       class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100">
                        {{ $formatLabels[request('format')] ?? request('format') }} ×
                    </a>
                @endif

                @if (request('rating'))
                    <a href="{{ route('catalog', request()->except(['rating', 'page'])) }}"
                       class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100">
                        Рейтинг {{ $ratingLabels[request('rating')] ?? request('rating') }} ×
                    </a>
                @endif

                @if (request('duration'))
                    <a href="{{ route('catalog', request()->except(['duration', 'page'])) }}"
                       class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100">
                        {{ $durationLabels[request('duration')] ?? request('duration') }} ×
                    </a>
                @endif

                @if (request('tag'))
                    <a href="{{ route('catalog', request()->except(['tag', 'page'])) }}"
                       class="inline-flex items-center gap-1 rounded-full bg-primary-50 px-3 py-1 text-xs font-medium text-primary-700 hover:bg-primary-100">
                        #{{ request('tag') }} ×
                    </a>
                @endif

                <a href="{{ route('catalog') }}" class="text-xs font-medium text-red-500 hover:text-red-700">Сбросить всё</a>
            </div>
        @endif
    </x-card>

    {{-- Популярные теги --}}
    @if ($tags->isNotEmpty())
        <div class="mb-6 flex flex-wrap items-center gap-2">
            <span class="text-xs text-gray-400">Теги:</span>
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
    @endif

    {{-- Результаты --}}
    <div class="mb-4 flex items-center justify-between gap-4">
        <p class="text-sm text-gray-500">
            Найдено: <span class="font-semibold text-gray-800">{{ $courses->total() }}</span>
        </p>
    </div>

    @if ($courses->isEmpty())
        <x-card class="text-center">
            <p class="text-gray-500">Ничего не найдено. Попробуйте сбросить фильтры.</p>
            <x-btn href="{{ route('catalog') }}" variant="secondary" size="sm" class="mt-4">Сбросить фильтры</x-btn>
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($courses as $course)
                <x-course-card :course="$course" />
            @endforeach
        </div>

        <div class="mt-8">{{ $courses->links() }}</div>
    @endif
</x-app-layout>