<x-guest-layout>
    <h2 class="text-center text-lg font-bold">{{ __('messages.login_heading') }}</h2>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('messages.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('messages.password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-gray-600 shadow-sm focus:ring-gray-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('messages.remember_me') }}</span>
            </label>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="text-left">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-800 block" href="{{ route('password.request') }}">
                        {{ __('messages.forgot_password') }}
                    </a>
                @endif
                <a href="{{ route('register') }}" class="underline text-sm text-gray-600 hover:text-gray-800 block mt-1">
                    {{ __('messages.create_account') }}
                </a>
            </div>

            <x-primary-button class="w-auto px-6">
                {{ __('messages.login') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
