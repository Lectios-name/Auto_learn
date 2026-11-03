<x-guest-layout>
    <x-card class="!p-8">
        <h2 class="text-center text-xl font-bold text-gray-900">Вход в Autolearn</h2>
        <p class="mt-1 text-center text-sm text-gray-500">Продолжите обучение с того же места</p>

        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
            @csrf

            <div>
                <x-input-label for="email" value="Email или логин" />
                <x-text-input id="email" name="email" class="mt-1 block w-full"
                              :value="old('email')" required autofocus autocomplete="username"
                              placeholder="you@example.com или username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" value="Пароль" />
                <x-text-input id="password" name="password" type="password"
                              class="mt-1 block w-full" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember" class="flex items-center gap-2 text-sm text-gray-600">
                    <input id="remember" type="checkbox" name="remember"
                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    Запомнить меня
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:text-primary-700">
                        Забыли пароль?
                    </a>
                @endif
            </div>

            <x-btn type="submit" size="lg" class="w-full">Войти</x-btn>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Впервые здесь?
            <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:text-primary-700">Создать аккаунт</a>
        </p>
    </x-card>
</x-guest-layout>