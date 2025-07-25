<x-layouts.app>
    <div class="container space-y-8">
        <h1 class="text-title-lg font-semibold text-main-text">My Profile</h1>
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative animate-fade-in" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 ease-in-out">
            <div class="border-b border-disabled/50 pb-4 mb-4 flex justify-between items-center">
                <h2 class="text-heading-lg font-medium text-brand">Personal Information</h2>
                <a href="{{ route('profile.edit') }}" class="text-button-md bg-main-button hover:bg-btn-main-hover text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300  hover:-translate-y-0.5">
                    Edit Profile
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="hover:bg-sub/30 p-3 rounded-md transition-all duration-200">
                    <dt class="text-body-md font-medium text-main-text/70">Full Name</dt>
                    <dd class="mt-1 text-body-lg text-main-text">{{ $user->full_name }}</dd>
                </div>
                <div class="hover:bg-sub/30 p-3 rounded-md transition-all duration-200">
                    <dt class="text-body-md font-medium text-main-text/70">Full Name (Kana)</dt>
                    <dd class="mt-1 text-body-lg text-main-text">{{ $user->full_name_kana }}</dd>
                </div>
                <div class="hover:bg-sub/30 p-3 rounded-md transition-all duration-200">
                    <dt class="text-body-md font-medium text-main-text/70">Email Address</dt>
                    <dd class="mt-1 text-body-lg text-main-text">{{ $user->email }}</dd>
                </div>
                <div class="hover:bg-sub/30 p-3 rounded-md transition-all duration-200">
                    <dt class="text-body-md font-medium text-main-text/70">Phone Number</dt>
                    <dd class="mt-1 text-body-lg text-main-text">{{ $user->phone_number }}</dd>
                </div>
                <div class="hover:bg-sub/30 p-3 rounded-md transition-all duration-200">
                    <dt class="text-body-md font-medium text-main-text/70">Date of Birth</dt>
                    <dd class="mt-1 text-body-lg text-main-text">{{ $user->date_of_birth ? $user->date_of_birth->format('d F Y') : '-' }}</dd>
                </div>
                <div class="hover:bg-sub/30 p-3 rounded-md transition-all duration-200">
                    <dt class="text-body-md font-medium text-main-text/70">Home Address</dt>
                    <dd class="mt-1 text-body-lg text-main-text">{{ $user->home_address ?? '-' }}</dd>
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 ease-in-out">
            <div class="border-b border-disabled/50 pb-4 mb-4">
                <h2 class="text-heading-lg font-medium text-brand">Change Password</h2>
            </div>
            <form action="{{ route('profile.update.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div class="transform transition-all duration-200 ">
                    <label for="current_password" class="block text-body-md font-medium text-main-text">Current Password</label>
                    <input type="password" name="current_password" id="current_password" required class="w-full md:w-1/2 px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md">
                    @error('current_password')
                        <p class="mt-2 text-body-md text-red-600 animate-shake">{{ $message }}</p>
                    @enderror
                </div>
                <div class="transform transition-all duration-200 ">
                    <label for="new_password" class="block text-body-md font-medium text-main-text">New Password</label>
                    <input type="password" name="new_password" id="new_password" required class="w-full md:w-1/2 px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md">
                    @error('new_password')
                        <p class="mt-2 text-body-md text-red-600 animate-shake">{{ $message }}</p>
                    @enderror
                </div>
                <div class="transform transition-all duration-200 ">
                    <label for="new_password_confirmation" class="block text-body-md font-medium text-main-text">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required class="w-full md:w-1/2 px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md">
                </div>
                <div>
                    <button type="submit" class="text-button-md bg-brand hover:bg-link-hover text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300  hover:-translate-y-0.5">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</x-layouts.app>