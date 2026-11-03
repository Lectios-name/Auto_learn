<x-card class="border-red-200">
    <x-slot name="header">Опасная зона</x-slot>

    <p class="text-sm text-gray-500">
        Удаление аккаунта необратимо: вместе с ним исчезнут прогресс, покупки и созданные курсы.
    </p>

    <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4 space-y-3"
          onsubmit="return confirm('Удалить аккаунт навсегда? Действие необратимо.')">
        @csrf
        @method('DELETE')

        <div>
            <x-input-label for="delete_password" value="Пароль для подтверждения" />
            <x-text-input id="delete_password" name="password" type="password" class="mt-1 block w-full" />
            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
        </div>

        <x-btn type="submit" variant="danger" class="w-full">Удалить аккаунт</x-btn>
    </form>
</x-card>