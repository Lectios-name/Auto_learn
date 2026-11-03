<x-guest-layout>
    <x-card class="!p-8">
        <h2 class="text-center text-xl font-bold text-gray-900">Создать аккаунт</h2>
        <p class="mt-1 text-center text-sm text-gray-500">Выберите роль — её можно сменить позже</p>

        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-5"
              x-data="{ role: '{{ old('role', 'student') }}' }">
            @csrf

            {{-- Плитки ролей --}}
            <div class="grid grid-cols-2 gap-3">
                <label class="cursor-pointer rounded-xl border-2 p-4 text-center transition"
                       :class="role === 'student' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" name="role" value="student" x-model="role" class="sr-only" checked>
                    <span class="text-2xl">🎓</span>
                    <span class="mt-1 block text-sm font-semibold text-gray-800">Студент</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Учиться и проходить тесты</span>
                </label>

                <label class="cursor-pointer rounded-xl border-2 p-4 text-center transition"
                       :class="role === 'author' ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:border-gray-300'">
                    <input type="radio" name="role" value="author" x-model="role" class="sr-only">
                    <span class="text-2xl">✍️</span>
                    <span class="mt-1 block text-sm font-semibold text-gray-800">Автор</span>
                    <span class="mt-0.5 block text-xs text-gray-500">Создавать курсы и зарабатывать</span>
                </label>
            </div>

            <div>
                <x-input-label for="name" value="Имя / логин" />
                <x-text-input id="name" name="name" class="mt-1 block w-full"
                              :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                              :value="old('email')" required autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <x-input-label for="password" value="Пароль" />
                    <x-text-input id="password" name="password" type="password"
                                  class="mt-1 block w-full" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="password_confirmation" value="Повторите пароль" />
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                                  class="mt-1 block w-full" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>

            <x-btn type="submit" size="lg" class="w-full">Зарегистрироваться</x-btn>
        </form>

        <p class="mt-6 text-center text-sm text-gray-500">
            Уже есть аккаунт?
            <a href="{{ route('login') }}" class="font-semibold text-primary-600 hover:text-primary-700">Войти</a>
        </p>
    </x-card>
</x-guest-layout>