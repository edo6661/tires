<x-layouts.app>
    <div class="flex flex-col items-center justify-center min-h-[50vh] bg-gray-50 py-8">
        <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">Reset Password</h1>
            </div>
            @if(session('status'))
                <div class="mb-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('reset-password.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                             <i class="fa-solid fa-lock text-gray-400"></i>
                        </span>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent pr-10"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                        >
                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                     @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div x-data="{ showPasswordConfirmation: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Password Confirmation*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                             <i class="fa-solid fa-lock text-gray-400"></i>
                        </span>
                        <input
                            :type="showPasswordConfirmation ? 'text' : 'password'"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent pr-10"
                            placeholder="••••••••"
                            required
                            autocomplete="current-password"
                        >
                        <button
                            type="button"
                            @click="showPasswordConfirmation = !showPasswordConfirmation"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                        >
                            <i class="fa-solid" :class="showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                     @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-green-700 text-white font-medium py-2.5 px-4 rounded-md transition duration-200">
                    Login
                </button>

            </form>

            <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                <p class="text-gray-600">
                    Don't have a RESERVATION account?
                </p>
                <a href="{{ route('register') }}" class="mt-2 inline-block text-primary hover:text-green-800 transition font-medium">
                    Sign up now
                </a>
            </div>
            
        </div>
    </div>
</x-layouts.app>