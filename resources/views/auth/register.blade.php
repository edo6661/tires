<x-layouts.app>
    <div class="flex flex-col items-center justify-center py-8 px-4">
        <div class="w-full max-w-xl bg-white p-6 md:p-8 rounded-lg shadow-md border border-disabled/50 transition-shadow duration-300 hover:shadow-xl">
            <div class="text-center mb-8">
                <h1 class="text-title-lg font-bold text-brand">RESERVATION ID</h1>
                <h2 class="text-title-md font-semibold text-main-text mt-2">Create Your Account</h2>
            </div>
            
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                @if(request('redirect'))
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif

                <div>
                    <label for="full_name" class="block text-body-md font-medium text-main-text mb-1">Full Name<span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-user text-main-text/40"></i>
                        </span>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                            placeholder="e.g., John Doe"
                            value="{{ old('full_name') }}"
                            required
                            autocomplete="name"
                            autofocus
                        >
                    </div>
                    @error('full_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="full_name_kana" class="block text-body-md font-medium text-main-text mb-1">Full Name (Kana)<span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-keyboard text-main-text/40"></i>
                        </span>
                        <input
                            type="text"
                            id="full_name_kana"
                            name="full_name_kana"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                            placeholder="e.g., ジョン ドウ"
                            value="{{ old('full_name_kana') }}"
                            required
                            autocomplete="name"
                        >
                    </div>
                    @error('full_name_kana')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-body-md font-medium text-main-text mb-1">Email Address<span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-envelope text-main-text/40"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                            placeholder="example@reservation.be"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone_number" class="block text-body-md font-medium text-main-text mb-1">Phone Number<span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-phone text-main-text/40"></i>
                        </span>
                        <input
                            type="tel"
                            id="phone_number"
                            name="phone_number"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                            placeholder="e.g., 08012345678"
                            value="{{ old('phone_number') }}"
                            required
                            autocomplete="tel"
                        >
                    </div>
                    @error('phone_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="company_name" class="block text-body-md font-medium text-main-text mb-1">Company Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-building text-main-text/40"></i>
                        </span>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                            placeholder="e.g., Acme Corporation"
                            value="{{ old('company_name') }}"
                            autocomplete="organization"
                        >
                    </div>
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="block text-body-md font-medium text-main-text mb-1">Department</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-sitemap text-main-text/40"></i>
                        </span>
                        <input
                            type="text"
                            id="department"
                            name="department"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                            placeholder="e.g., Sales"
                            value="{{ old('department') }}"
                            autocomplete="organization-title"
                        >
                    </div>
                    @error('department')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_address" class="block text-body-md font-medium text-main-text mb-1">Company Address</label>
                    <textarea
                        id="company_address"
                        name="company_address"
                        rows="3"
                        class="w-full px-3 py-2 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                        placeholder="Enter company address"
                    >{{ old('company_address') }}</textarea>
                    @error('company_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="home_address" class="block text-body-md font-medium text-main-text mb-1">Home Address</label>
                    <textarea
                        id="home_address"
                        name="home_address"
                        rows="3"
                        class="w-full px-3 py-2 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                        placeholder="Enter home address"
                    >{{ old('home_address') }}</textarea>
                    @error('home_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-body-md font-medium text-main-text mb-1">Date of Birth</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-calendar-alt text-main-text/40"></i>
                        </span>
                        <input
                            type="date"
                            id="date_of_birth"
                            name="date_of_birth"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                            value="{{ old('date_of_birth') }}"
                            autocomplete="bday"
                        >
                    </div>
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-body-md font-medium text-main-text mb-1">Gender</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-venus-mars text-main-text/40"></i>
                        </span>
                        <select
                            id="gender"
                            name="gender"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand transition-colors duration-300"
                            autocomplete="sex"
                        >
                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-body-md font-medium text-main-text mb-1">Password<span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                             <i class="fa-solid fa-lock text-main-text/40"></i>
                        </span>
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            id="password"
                            name="password"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand pr-10 transition-colors duration-300"
                            placeholder="Minimum 8 characters"
                            required
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-main-text/50 hover:text-brand focus:outline-none"
                            aria-label="Toggle password visibility"
                        >
                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                     @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ showConfirmPassword: false }">
                    <label for="password_confirmation" class="block text-body-md font-medium text-main-text mb-1">Confirm Password<span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                             <i class="fa-solid fa-lock text-main-text/40"></i>
                        </span>
                        <input
                            :type="showConfirmPassword ? 'text' : 'password'"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full px-3 py-2 pl-10 border border-disabled rounded-md focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand pr-10 transition-colors duration-300"
                            placeholder="Confirm your password"
                            required
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-main-text/50 hover:text-brand focus:outline-none"
                            aria-label="Toggle password confirmation visibility"
                        >
                            <i class="fa-solid" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-main-button hover:bg-btn-main-hover text-footer-text font-semibold py-2.5 px-4 rounded-md transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-main-button">
                    Register
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-disabled/70 text-center">
                <p class="text-main-text/90 text-body-md">
                    Already have a RESERVATION account?
                </p>
                <a href="{{ route('login') . (request('redirect') ? '?redirect=' . urlencode(request('redirect')) : '') }}" 
                   class="mt-2 inline-block text-link hover:text-link-hover transition-colors duration-300 font-medium text-body-md">
                    Sign in here
                </a>
            </div>

            <div class="mt-8 text-center text-body-md text-main-text/80">
                <p class="mb-3">
                    By registering, you agree to our terms of service and privacy policy.
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="text-link hover:text-link-hover text-sm">Terms of Service</a>
                    <a href="#" class="text-link hover:text-link-hover text-sm">Privacy Policy</a>
                    <a href="#" class="text-link hover:text-link-hover text-sm">Contact Us</a>
                </div>
                <p class="mt-4 text-main-text/60 text-xs">© RESERVATION</p>
            </div>
        </div>
    </div>
</x-layouts.app>