<x-card>
    <x-slot name="header">Данные профиля</x-slot>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PATCH')

        {{-- Аватар --}}
        <div class="flex items-center gap-4">
            @if ($user->avatar_path)
                <img src="{{ asset('storage/'.$user->avatar_path) }}" alt=""
                     class="h-16 w-16 rounded-full object-cover ring-2 ring-primary-500">
            @else
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-500 text-xl font-bold text-white">
                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                </span>
            @endif

            <div class="flex-1">
                <x-input-label for="avatar" value="Аватар" />
                <input type="file" name="avatar" id="avatar" accept="image/jpeg,image/png,image/webp"
                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-primary-700 hover:file:bg-primary-100" />
                <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="name" value="Имя / логин" />
            <x-text-input id="name" name="name" class="mt-1 block w-full"
                          :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                          :value="old('email', $user->email)" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center gap-3">
            <x-btn type="submit">Сохранить</x-btn>

            @if (session('status') === 'profile-updated')
                <span class="text-sm font-medium text-primary-600">Сохранено ✓</span>
            @endif
        </div>
    </form>
</x-card>