<x-card>
    <x-slot name="header">Смена пароля</x-slot>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <x-input-label for="current_password" value="Текущий пароль" />
            <x-text-input id="current_password" name="current_password" type="password"
                          class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="password" value="Новый пароль" />
                <x-text-input id="password" name="password" type="password"
                              class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" value="Повторите новый пароль" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                              class="mt-1 block w-full" autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-btn type="submit">Обновить пароль</x-btn>

            @if (session('status') === 'password-updated')
                <span class="text-sm font-medium text-primary-600">Пароль изменён ✓</span>
            @endif
        </div>
    </form>
</x-card>