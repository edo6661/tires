<x-layouts.app>
    <div class="container">
        <h1 class="text-title-lg font-semibold text-main-text mb-6">Edit Profile</h1>
        <form action="{{ route('profile.update') }}" method="POST" class="bg-white p-8 rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 ease-in-out space-y-6">
            @csrf
            @method('PATCH')
            <div class="transform transition-all duration-200">
                <label for="full_name" class="block text-body-md font-medium text-main-text">Full Name</label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $user->full_name) }}" required class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md">
                @error('full_name')
                    <p class="mt-2 text-body-md text-red-600 animate-shake">{{ $message }}</p>
                @enderror
            </div>
            <div class="transform transition-all duration-200">
                <label for="full_name_kana" class="block text-body-md font-medium text-main-text">Full Name (Kana)</label>
                <input type="text" name="full_name_kana" id="full_name_kana" value="{{ old('full_name_kana', $user->full_name_kana) }}" required class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md">
                @error('full_name_kana')
                    <p class="mt-2 text-body-md text-red-600 animate-shake">{{ $message }}</p>
                @enderror
            </div>
            <div class="transform transition-all duration-200">
                <label for="email" class="block text-body-md font-medium text-main-text">Email Address</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md">
                @error('email')
                    <p class="mt-2 text-body-md text-red-600 animate-shake">{{ $message }}</p>
                @enderror
            </div>
            <div class="transform transition-all duration-200">
                <label for="phone_number" class="block text-body-md font-medium text-main-text">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md">
                @error('phone_number')
                    <p class="mt-2 text-body-md text-red-600 animate-shake">{{ $message }}</p>
                @enderror
            </div>
            <div class="transform transition-all duration-200">
                <label for="date_of_birth" class="block text-body-md font-medium text-main-text">Date of Birth</label>
                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md">
                @error('date_of_birth')
                    <p class="mt-2 text-body-md text-red-600 animate-shake">{{ $message }}</p>
                @enderror
            </div>
            <div class="transform transition-all duration-200">
                <label for="home_address" class="block text-body-md font-medium text-main-text">Home Address</label>
                <textarea name="home_address" id="home_address" rows="3" class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md">{{ old('home_address', $user->home_address) }}</textarea>
                @error('home_address')
                    <p class="mt-2 text-body-md text-red-600 animate-shake">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-disabled/50">
                <a href="{{ route('profile.show') }}" class="bg-secondary-button hover:bg-disabled text-main-text font-bold py-2 px-4 rounded-lg transition-all duration-300 hover:scale-105 hover:-translate-y-0.5 text-button-md">
                    Cancel
                </a>
                <button type="submit" class="bg-main-button hover:bg-btn-main-hover text-white font-bold py-2 px-4 rounded-lg transition-all duration-300 hover:scale-105 hover:-translate-y-0.5 text-button-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</x-layouts.app>