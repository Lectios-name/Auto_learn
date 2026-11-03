<x-app-layout>
    <x-page-title title="Новый курс" subtitle="Шаг 1 — базовая информация. Секции и уроки добавите после создания." />

    <x-card class="max-w-3xl">
        <form method="POST" action="{{ route('author.courses.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('author.courses._form')

            <div class="flex items-center gap-3">
                <x-btn type="submit">Создать курс</x-btn>
                <x-btn href="{{ route('author.courses.index') }}" variant="ghost">Отмена</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>