<x-layouts.app>
    <div class="flex flex-col items-center justify-center py-8">
        <div class="w-full max-w-xl bg-white p-6 rounded-lg shadow-sm border border-disabled hover:shadow-lg transition-shadow duration-300">
            <div class="text-center mb-8">
                <h1 class="text-title-lg font-bold text-brand">{{ __('forgot-password.title') }}</h1>
            </div>
            @if(session('status'))
                <div class="bg-brand/10 text-brand p-4 rounded mb-4 border border-brand/20 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('forgot-password') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-body-md font-medium text-main-text mb-1">{{ __('forgot-password.email_label') }}</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-envelope text-secondary-button"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-200"
                            placeholder="{{ __('forgot-password.email_placeholder') }}"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            autofocus
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full bg-main-button hover:bg-btn-main-hover text-footer-text font-semibold py-2.5 px-4 rounded-md transition-all duration-300 transform hover:scale-[1.01]">
                    {{ __('forgot-password.submit_button') }}
                </button>
            </form>
            <div class="mt-8 pt-8 border-t border-disabled text-center">
                <p class="text-main-text/80">
                    {{ __('forgot-password.remember_password_prompt') }}
                </p>
                <a href="{{ route('login') }}" class="mt-2 inline-block text-link hover:text-link-hover transition-colors duration-200 font-medium">
                    {{ __('forgot-password.back_to_login_link') }}
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>