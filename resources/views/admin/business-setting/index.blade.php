<x-layouts.app>
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Business Settings</h1>
                    <p class="text-gray-600 mt-1">Manage your business information and settings</p>
                </div>
                @if($businessSettings)
                    <a href="{{ route('admin.business-setting.edit', $businessSettings->id) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        <i class="fas fa-edit mr-2"></i>Edit Settings
                    </a>
                @else
                    <a href="{{ route('admin.business-setting.edit', 1) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        <i class="fas fa-plus mr-2"></i>Create Settings
                    </a>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if($businessSettings)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-store text-blue-600 mr-2"></i>
                        Basic Information
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shop Name</label>
                            <p class="text-gray-900">{{ $businessSettings->shop_name ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <p class="text-gray-900">{{ $businessSettings->phone_number ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <p class="text-gray-900">{{ $businessSettings->address ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                            <p class="text-gray-900">
                                @if($businessSettings->website_url)
                                    <a href="{{ $businessSettings->website_url }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $businessSettings->website_url }}
                                    </a>
                                @else
                                    Not set
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        Business Hours
                    </h2>
                    <div class="space-y-3">
                        @if($businessSettings->business_hours)
                            @php
                                $days = [
                                    'monday' => 'Monday',
                                    'tuesday' => 'Tuesday', 
                                    'wednesday' => 'Wednesday',
                                    'thursday' => 'Thursday',
                                    'friday' => 'Friday',
                                    'saturday' => 'Saturday',
                                    'sunday' => 'Sunday'
                                ];
                            @endphp
                            @foreach($days as $day => $dayName)
                                @php
                                    $hours = $businessSettings->business_hours[$day] ?? null;
                                @endphp
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                    <span class="font-medium text-gray-700">{{ $dayName }}</span>
                                    <span class="text-gray-900">
                                        @if($hours && isset($hours['closed']) && $hours['closed'])
                                            <span class="text-red-600">Closed</span>
                                        @elseif($hours && isset($hours['open']) && isset($hours['close']))
                                            {{ $hours['open'] }} - {{ $hours['close'] }}
                                        @else
                                            <span class="text-red-600">Closed</span>
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">Business hours not set</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-globe text-purple-600 mr-2"></i>
                        Site Settings
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <p class="text-gray-900">{{ $businessSettings->site_name ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Status</label>
                            <p class="text-gray-900">
                                @if($businessSettings->site_public)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Public
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Private
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reply Email</label>
                            <p class="text-gray-900">{{ $businessSettings->reply_email ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
                            <p class="text-gray-900">{{ $businessSettings->google_analytics_id ?? 'Not set' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-image text-orange-600 mr-2"></i>
                        Description & Image
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Shop Description</label>
                            <p class="text-gray-900 text-sm">
                                {{ $businessSettings->shop_description ?? 'No description set' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Top Image</label>
                            @if($businessSettings->top_image_path)
                                <div class="mt-2">
                                    <img src="{{ asset($businessSettings->top_image_path) }}" 
                                         alt="Top Image" 
                                         class="h-32 w-full object-cover rounded-lg">
                                </div>
                            @else
                                <p class="text-gray-500">No image uploaded</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Access Information</label>
                            <p class="text-gray-900 text-sm">
                                {{ $businessSettings->access_information ?? 'No access information set' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-contract text-indigo-600 mr-2"></i>
                        Policies & Terms
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Terms of Use</label>
                            <div class="bg-gray-50 rounded-lg p-3 max-h-32 overflow-y-auto">
                                <p class="text-sm text-gray-700">
                                    {{ $businessSettings->terms_of_use ?? 'Terms of use not set' }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Privacy Policy</label>
                            <div class="bg-gray-50 rounded-lg p-3 max-h-32 overflow-y-auto">
                                <p class="text-sm text-gray-700">
                                    {{ $businessSettings->privacy_policy ?? 'Privacy policy not set' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-store text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Business Settings Found</h3>
                <p class="text-gray-500 mb-4">Get started by creating your business settings.</p>
                <a href="{{ route('admin.business-setting.edit', 1) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-plus mr-2"></i>
                    Create Business Settings
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>