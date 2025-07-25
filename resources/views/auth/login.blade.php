<x-layouts.app>
    <div class="flex flex-col items-center justify-center py-8">
        <div class="w-full max-w-xl bg-white p-6 rounded-lg shadow-sm border border-disabled hover:shadow-lg transition-shadow duration-300">
            <div class="text-center mb-8">
                <h1 class="text-title-lg font-bold text-brand">RESERVATION ID</h1>
            </div>
            @if(session('status'))
                <div class="bg-brand/10 text-brand p-4 rounded mb-4 border border-brand/20">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                @if(request('redirect'))
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif
                <div>
                    <label for="email" class="block text-body-md font-medium text-main-text mb-1">Email Address*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-envelope text-secondary-button"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand/50 focus:border-brand transition-all duration-200"
                            placeholder="example@reservation.be"
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
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-body-md font-medium text-main-text mb-1">Password*</label>
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
                            autocomplete="current-password"
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
                <div class="flex items-center justify-between">
                     <div class="flex items-center">
                        <input
                            id="remember"
                            name="remember"
                            type="checkbox"
                            class="h-4 w-4 text-brand focus:ring-brand/50 border-disabled rounded"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <label for="remember" class="ml-2 block text-body-md text-main-text">
                           Remember me
                        </label>
                    </div>
                    <a href="{{ route('forgot-password') }}" class="text-sm text-link hover:text-link-hover transition-colors duration-200">
                        Forgot your password?
                    </a>
                </div>
                <button type="submit" class="w-full bg-main-button hover:bg-btn-main-hover text-footer-text font-semibold py-2.5 px-4 rounded-md transition-all duration-300 transform hover:scale-[1.01]">
                    Login
                </button>
            </form>
            <div class="mt-8 pt-8 border-t border-disabled text-center">
                <p class="text-main-text/80">
                    Don't have a RESERVATION account?
                </p>
                <a href="{{ route('register') . (request('redirect') ? '?redirect=' . urlencode(request('redirect')) : '') }}" 
                   class="mt-2 inline-block text-link hover:text-link-hover transition-colors duration-200 font-medium">
                    Sign up now
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>