<x-layouts.app>
    <div class="flex flex-col items-center justify-center  bg-gray-50 py-8">
        <div class="w-full max-w-md bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-800">RESERVATION ID</h1>
                <h2 class="text-xl font-semibold text-gray-700 mt-2">Create Your Account</h2>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                @if(request('redirect'))
                    <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                @endif

                <div>
                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-user text-gray-400"></i>
                        </span>
                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                    <label for="full_name_kana" class="block text-sm font-medium text-gray-700 mb-1">Full Name (Kana)*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-keyboard text-gray-400"></i>
                        </span>
                        <input
                            type="text"
                            id="full_name_kana"
                            name="full_name_kana"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-envelope text-gray-400"></i>
                        </span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-phone text-gray-400"></i>
                        </span>
                        <input
                            type="tel"
                            id="phone_number"
                            name="phone_number"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                    <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-building text-gray-400"></i>
                        </span>
                        <input
                            type="text"
                            id="company_name"
                            name="company_name"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-sitemap text-gray-400"></i>
                        </span>
                        <input
                            type="text"
                            id="department"
                            name="department"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                    <label for="company_address" class="block text-sm font-medium text-gray-700 mb-1">Company Address</label>
                    <textarea
                        id="company_address"
                        name="company_address"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Enter company address"
                    >{{ old('company_address') }}</textarea>
                    @error('company_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="home_address" class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                    <textarea
                        id="home_address"
                        name="home_address"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="Enter home address"
                    >{{ old('home_address') }}</textarea>
                    @error('home_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-calendar-alt text-gray-400"></i>
                        </span>
                        <input
                            type="date"
                            id="date_of_birth"
                            name="date_of_birth"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            value="{{ old('date_of_birth') }}"
                            autocomplete="bday"
                        >
                    </div>
                    @error('date_of_birth')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="fa-solid fa-venus-mars text-gray-400"></i>
                        </span>
                        <select
                            id="gender"
                            name="gender"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
                            placeholder="Minimum 8 characters"
                            required
                            autocomplete="new-password"
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

                <div x-data="{ showConfirmPassword: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password*</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                             <i class="fa-solid fa-lock text-gray-400"></i>
                        </span>
                        <input
                            :type="showConfirmPassword ? 'text' : 'password'"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent pr-10"
                            placeholder="Confirm your password"
                            required
                            autocomplete="new-password"
                        >
                        <button
                            type="button"
                            @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none"
                        >
                            <i class="fa-solid" :class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary hover:bg-green-700 text-white font-medium py-2.5 px-4 rounded-md transition duration-200">
                    Register
                </button>
            </form>

            <div class="mt-8 pt-8 border-t border-gray-200 text-center">
                <p class="text-gray-600">
                    Already have a RESERVATION account?
                </p>
                <a href="{{ route('login') . (request('redirect') ? '?redirect=' . urlencode(request('redirect')) : '') }}" 
                   class="mt-2 inline-block text-primary hover:text-green-800 transition font-medium">
                    Sign in here
                </a>
            </div>

            <div class="mt-8 text-center text-sm text-gray-600">
                <p class="mb-3">
                    By registering, you agree to our terms of service and privacy policy.
                </p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="text-primary hover:text-green-800">Terms of Service</a>
                    <a href="#" class="text-primary hover:text-green-800">Privacy Policy</a>
                    <a href="#" class="text-primary hover:text-green-800">Contact Us</a>
                </div>
                <p class="mt-4 text-gray-500">© RESERVATION</p>
            </div>
        </div>
    </div>
</x-layouts.app>