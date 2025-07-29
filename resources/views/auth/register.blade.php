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
    
    <div class="max-w-md mx-auto mt-10 rounded-lg p-8 ">
        <!-- Register Heading -->
        <div class="mb-6" style="text-align: center;">
            <h1 style="font-size: 2rem; font-weight: 600; color: #1F2937; margin: 0;">
                Create Account
            </h1>
            <p style="font-size: 0.875rem; color: #6B7280; margin-top: 0.25rem;">
                Fill in the details to Create Your Account
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                              type="password"
                              name="password"
                              required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                              type="password"
                              name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <!-- Register Button -->
            <div class="mb-4" style="text-align: center;">
                <x-primary-button class="w-full justify-center text-center">
                    {{ __('Register') }}
                </x-primary-button>
            </div>

            <!-- Login Redirect -->
            <div class="text-center mt-4" style="text-align: center;">
                <p class="text-sm text-gray-600">
                    {{ __('Already have an account?') }}
                    <a href="{{ route('login') }}" class="text-indigo-600 link-hover-style">
                        {{ __('Log in here') }}
                    </a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
