<x-layouts.app>
    <div class="flex flex-col items-center justify-center py-8">
        <div class="w-full max-w-xl bg-white p-6 rounded-lg shadow-sm border border-disabled hover:shadow-lg transition-shadow duration-300">

            <div class="text-center mb-8">
                <h1 class="text-title-lg font-bold text-brand">Set a New Password</h1>
            </div>

            @if(session('status'))
                <div class="bg-brand/10 text-brand p-4 rounded mb-4 border border-brand/20">
                    {{ session('status') }}
                </div>
            @endif
            @error('email')
                <div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ $message }}</span>
                </div>
            @enderror

            <form method="POST" action="{{ route('reset-password.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-body-md font-medium text-main-text mb-1">New Password*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                             <i class="fa-solid fa-lock text-secondary-button"></i>
                        </span>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-200 pr-10"
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-button hover:text-main-text focus:outline-none transition-colors duration-200"
                        >
                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                     @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showPasswordConfirmation: false }">
                    <label for="password_confirmation" class="block text-body-md font-medium text-main-text mb-1">Confirm New Password*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                             <i class="fa-solid fa-lock text-secondary-button"></i>
                        </span>
                        <input
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-200 pr-10"
                            placeholder="••••••••"
                            required
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-button hover:text-main-text focus:outline-none transition-colors duration-200"
                        >
                            <i class="fa-solid" :class="showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-main-button hover:bg-btn-main-hover text-footer-text font-semibold py-2.5 px-4 rounded-md transition-all duration-300 transform hover:scale-[1.01]">
                    Reset Password
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-disabled text-center">
                <a href="{{ route('login') }}" class="text-link hover:text-link-hover transition-colors duration-200 font-medium">
                    Back to Login
                </a>
            </div>

        </div>
    </div>
</x-layouts.app>    