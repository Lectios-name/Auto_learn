<x-app-layout>
    <x-page-title :title="'Редактирование: '.$course->title" subtitle="Базовая информация курса" />

    <x-card class="max-w-3xl">
        <form method="POST" action="{{ route('author.courses.update', $course) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('author.courses._form')

            <div class="flex items-center gap-3">
                <x-btn type="submit">Сохранить</x-btn>
                <x-btn href="{{ route('author.courses.index') }}" variant="ghost">Отмена</x-btn>
            </div>
        </form>
    </x-card>
</x-app-layout>