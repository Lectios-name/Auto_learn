{{-- Базовые данные курса. Используется на страницах create и edit. --}}

<div>
    <x-input-label for="title" value="Название курса" />
    <x-text-input id="title" name="title" class="mt-1 block w-full" maxlength="255"
                  :value="old('title', $course->title)" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" value="Описание" />
    <textarea id="description" name="description" rows="4" maxlength="5000"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
              placeholder="О чём курс, для кого он и чему научится студент?">{{ old('description', $course->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2"
     x-data="{ accessType: '{{ old('access_type', $course->access_type ?? 'paid') }}' }">

    <div>
        <x-input-label for="level" value="Уровень сложности" />
        <select id="level" name="level"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="beginner" @selected(old('level', $course->level) === 'beginner')>Начинающий</option>
            <option value="intermediate" @selected(old('level', $course->level) === 'intermediate')>Средний</option>
            <option value="advanced" @selected(old('level', $course->level) === 'advanced')>Продвинутый</option>
        </select>
        <x-input-error :messages="$errors->get('level')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="format" value="Формат" />
        <select id="format" name="format"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="video" @selected(old('format', $course->format) === 'video')>Видео-курс</option>
            <option value="text" @selected(old('format', $course->format) === 'text')>Текстовый курс</option>
            <option value="practice" @selected(old('format', $course->format) === 'practice')>Практикум</option>
            <option value="mixed" @selected(old('format', $course->format ?? 'mixed') === 'mixed')>Смешанный</option>
        </select>
        <x-input-error :messages="$errors->get('format')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="access_type" value="Тип доступа" />
        <select id="access_type" name="access_type" x-model="accessType"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
            <option value="paid">Платный</option>
            <option value="free">Бесплатный</option>
            <option value="subscription">По подписке</option>
        </select>
        <x-input-error :messages="$errors->get('access_type')" class="mt-2" />
    </div>

    {{-- Цена видна и активна только для платного курса --}}
    <div x-show="accessType === 'paid'" x-cloak>
        <x-input-label for="price" value="Цена, ₽" />
        <x-text-input id="price" name="price" type="number" step="0.01" min="0" max="999999"
                      x-bind:disabled="accessType !== 'paid'"
                      class="mt-1 block w-full" :value="old('price', $course->price)" />
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="tags" value="Теги (для поиска в каталоге)" />
    <x-text-input id="tags" name="tags" class="mt-1 block w-full"
                  :value="old('tags', $course->tags->pluck('name')->implode(', '))"
                  placeholder="php, laravel, backend" />
    <p class="mt-1 text-xs text-gray-400">Разделяйте запятыми, до 10 тегов.</p>
</div>

<div>
    <x-input-label for="cover" value="Обложка курса" />

    @if ($course->cover_path)
        <img src="{{ asset('storage/'.$course->cover_path) }}" alt="Текущая обложка"
             class="mb-2 h-32 w-full rounded-lg object-cover">
    @endif

    <input type="file" name="cover" id="cover" accept="image/jpeg,image/png,image/webp"
           class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100" />
    <p class="mt-1 text-xs text-gray-400">JPG, PNG или WebP до 2 МБ. Оставьте пустым, чтобы сохранить текущую обложку.</p>
    <x-input-error :messages="$errors->get('cover')" class="mt-2" />
</div>