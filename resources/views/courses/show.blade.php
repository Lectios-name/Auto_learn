<x-app-layout>
    <x-slot name="title">{{ $course->title }}</x-slot>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        {{-- Левая колонка: описание + программа + отзывы --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Описание курса --}}
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

            {{-- Программа курса --}}
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

            {{-- Отзывы --}}
            <x-card>
                <x-slot name="header">Отзывы ({{ $course->rating_count }})</x-slot>

                @auth
                    @if ($enrollment && $enrollment->progress_percent >= 20)
                        @php $myReview = $course->reviews->firstWhere('user_id', auth()->id()); @endphp

                        <form method="POST" action="{{ route('courses.reviews.store', $course) }}"
                              class="mb-6 rounded-xl bg-gray-50 p-4"
                              x-data="{ rating: {{ $myReview->rating ?? 5 }} }">
                            @csrf

                            <p class="text-sm font-medium text-gray-700">
                                {{ $myReview ? 'Редактировать ваш отзыв' : 'Оставить отзыв' }}
                            </p>

                            <div class="mt-2 flex gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}"
                                            class="text-2xl leading-none transition"
                                            :class="rating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'">★</button>
                                @endfor
                                <input type="hidden" name="rating" :value="rating">
                            </div>

                            <textarea name="text" rows="3"
                                      class="mt-3 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
                                      placeholder="Расскажите, что понравилось, а чего не хватило...">{{ $myReview->text ?? '' }}</textarea>
                            <x-input-error :messages="$errors->get('text')" class="mt-2" />

                            <x-btn type="submit" size="sm" class="mt-3">
                                {{ $myReview ? 'Обновить отзыв' : 'Опубликовать отзыв' }}
                            </x-btn>
                        </form>
                    @elseif ($enrollment)
                        <div class="mb-6 rounded-xl border border-dashed border-gray-300 px-4 py-3 text-sm text-gray-500">
                            🔒 Отзыв станет доступен после прохождения 20% курса.
                            Ваш прогресс: <span class="font-semibold">{{ $enrollment->progress_percent }}%</span>.
                        </div>
                    @endif
                @endauth

                @forelse ($course->reviews as $review)
                    <div class="border-t border-gray-100 py-4 first:border-t-0 first:pt-0">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-primary-100 text-sm font-bold text-primary-700">
                                    @if ($review->user->avatar_path)
                                        <img src="{{ asset('storage/'.$review->user->avatar_path) }}" alt="" class="h-9 w-9 object-cover">
                                    @else
                                        {{ strtoupper(mb_substr($review->user->name, 0, 1)) }}
                                    @endif
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $review->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $review->created_at->format('d.m.Y') }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <span class="text-sm">
                                    <span class="text-yellow-400">{{ str_repeat('★', $review->rating) }}</span><span class="text-gray-300">{{ str_repeat('★', 5 - $review->rating) }}</span>
                                </span>
                                @if (auth()->id() === $review->user_id)
                                    <form method="POST" action="{{ route('reviews.destroy', $review) }}"
                                          onsubmit="return confirm('Удалить ваш отзыв?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Удалить</button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        @if ($review->text)
                            <p class="mt-2 text-sm text-gray-600">{{ $review->text }}</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Отзывов пока нет — станьте первым!</p>
                @endforelse
            </x-card>
        </div>

        {{-- Правая колонка: покупка + автор --}}
        <div class="space-y-6">
            {{-- Карточка покупки (динамическая кнопка) --}}
            <x-card>
                <p class="text-3xl font-extrabold text-gray-900">
                    {{ $course->access_type === 'free' ? 'Бесплатно' : ($course->price ? number_format($course->price, 0, ',', ' ') .' ₽' : 'Подписка') }}
                </p>

                <div class="mt-4 space-y-3">
                    @guest
                        <x-btn href="{{ route('login') }}" class="w-full">Войти, чтобы записаться</x-btn>
                    @else
                        @if ($enrollment)
                            <x-btn href="#" class="w-full">Продолжить обучение ({{ $enrollment->progress_percent }}%)</x-btn>
                        @elseif ($course->access_type === 'free')
                            <form method="POST" action="{{ route('courses.enroll', $course) }}">
                                @csrf
                                <x-btn type="submit" class="w-full">Записаться бесплатно</x-btn>
                            </form>
                        @elseif ($course->access_type === 'paid')
                            <x-btn href="#" class="w-full">Купить курс</x-btn>
                            <p class="text-center text-xs text-gray-400">Оплата через ЮMoney — Этап 5</p>
                        @else
                            <x-btn href="#" class="w-full">Оформить подписку</x-btn>
                            <p class="text-center text-xs text-gray-400">Подписка подключается на Этапе 5</p>
                        @endif
                    @endguest
                </div>

                @if ($enrollment)
                    <div class="mt-4">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Прогресс прохождения</span>
                            <span class="font-semibold text-primary-600">{{ $enrollment->progress_percent }}%</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-gray-100">
                            <div class="h-2 rounded-full bg-primary-500 transition-all"
                                 style="width: {{ $enrollment->progress_percent }}%"></div>
                        </div>
                    </div>
                @endif
            </x-card>

            {{-- Автор курса --}}
            <x-card>
                <x-slot name="header">Автор курса</x-slot>
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full bg-primary-500 font-bold text-white">
                        @if ($course->author->avatar_path)
                            <img src="{{ asset('storage/'.$course->author->avatar_path) }}" alt="" class="h-10 w-10 object-cover">
                        @else
                            {{ mb_substr($course->author->name, 0, 1) }}
                        @endif
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