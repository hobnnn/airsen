<x-guest-layout>
    <!-- Inline style block in your Blade file -->
    <style>
        .link-hover-style {
            font-weight: 500;
            text-decoration: none;
        }

        .link-hover-style:hover {
            text-decoration: underline;
        }
    </style>


    <div class="max-w-md mx-auto mt-10  rounded-lg p-8">
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Sign In Heading -->
        <div class="mb-6" style="text-align: center;">
            <h1 style="font-size: 2rem; font-weight: 600; color: #1F2937; margin: 0;">
                Sign In
            </h1>
            <p style="font-size: 0.875rem; color: #6B7280; margin-top: 0.25rem;">
                Access your Air Sentinel account
            </p>
        </div>


        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between mb-4">
                <label for="remember_me" class="flex items-center">
                    <input id="remember_me" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-indigo-600 link-hover-style" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <!-- Login Button -->
            <div class="mb-4 ">
                <x-primary-button class="w-full justify-center text-center">
                    {{ __('Log in') }}
                </x-primary-button>
            </div>


            <!-- Register Link -->
            <div class="text-center mt-4" style="text-align: center;">
                <p class="text-sm text-gray-600">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="text-indigo-600 link-hover-style">
                        {{ __('Register here') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
