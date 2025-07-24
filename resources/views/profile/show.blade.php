<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-8">
        <h1 class="text-2xl font-semibold text-gray-800">Profil Saya</h1>
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        <div class="bg-white p-6 rounded-lg shadow">
            <div class="border-b border-gray-200 pb-4 mb-4 flex justify-between items-center">
                <h2 class="text-xl font-medium text-gray-700">Informasi Pribadi</h2>
                <a href="{{ route('profile.edit') }}" class="text-sm bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Edit Profil
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Lengkap</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $user->full_name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama Lengkap (Kana)</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $user->full_name_kana }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Alamat Email</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nomor Telepon</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $user->phone_number }}</dd>
                </div>
                 <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal Lahir</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $user->date_of_birth ? $user->date_of_birth->format('d F Y') : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Alamat Rumah</dt>
                    <dd class="mt-1 text-lg text-gray-900">{{ $user->home_address ?? '-' }}</dd>
                </div>
                
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
             <div class="border-b border-gray-200 pb-4 mb-4">
                <h2 class="text-xl font-medium text-gray-700">Ganti Password</h2>
            </div>
            <form action="{{ route('profile.update.password') }}" method="POST" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" required class="mt-1 block w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('current_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" name="new_password" id="new_password" required class="mt-1 block w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('new_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required class="mt-1 block w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div>
                    <button type="submit" class="text-sm bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>