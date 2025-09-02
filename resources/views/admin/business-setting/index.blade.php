<x-layouts.app>
    <div class="container">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('admin/business-setting/index.title') }}</h1>
                    <p class="text-gray-600 mt-1">{{ __('admin/business-setting/index.description') }}</p>
                </div>
                @if($businessSettings)
                    <a href="{{ route('admin.business-setting.edit', $businessSettings->id) }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        <i class="fas fa-edit mr-2"></i>{{ __('admin/business-setting/index.edit_button') }}
                    </a>
                @else
                    <a href="{{ route('admin.business-setting.edit', 1) }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        <i class="fas fa-plus mr-2"></i>{{ __('admin/business-setting/index.create_button') }}
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
                        {{ __('admin/business-setting/index.basic_info.title') }}
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.basic_info.shop_name') }}</label>
                            <p class="text-gray-900">{{ $businessSettings->shop_name ?: __('admin/business-setting/index.not_set') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.basic_info.phone_number') }}</label>
                            <p class="text-gray-900">{{ $businessSettings->phone_number ?: __('admin/business-setting/index.not_set') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.basic_info.address') }}</label>
                            <p class="text-gray-900">{{ $businessSettings->address ?: __('admin/business-setting/index.not_set') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.basic_info.website_url') }}</label>
                            <p class="text-gray-900">
                                @if($businessSettings->website_url)
                                    <a href="{{ $businessSettings->website_url }}" target="_blank" class="text-blue-600 hover:underline">
                                        {{ $businessSettings->website_url }}
                                    </a>
                                @else
                                    {{ __('admin/business-setting/index.not_set') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        {{ __('admin/business-setting/index.business_hours.title') }}
                    </h2>
                    <div class="space-y-3">
                        @if($businessSettings->business_hours)
                            @foreach(__('admin/business-setting/index.business_hours.days') as $day => $dayName)
                                @php
                                    $hours = $businessSettings->business_hours[$day] ?? null;
                                @endphp
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0">
                                    <span class="font-medium text-gray-700">{{ $dayName }}</span>
                                    <span class="text-gray-900">
                                        @if($hours && isset($hours['closed']) && $hours['closed'])
                                            <span class="text-red-600">{{ __('admin/business-setting/index.business_hours.closed') }}</span>
                                        @elseif($hours && isset($hours['open']) && isset($hours['close']))
                                            {{ $hours['open'] }} - {{ $hours['close'] }}
                                        @else
                                            <span class="text-red-600">{{ __('admin/business-setting/index.business_hours.closed') }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-gray-500">{{ __('admin/business-setting/index.business_hours.not_set') }}</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-globe text-purple-600 mr-2"></i>
                        {{ __('admin/business-setting/index.site_settings.title') }}
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.site_settings.site_name') }}</label>
                            <p class="text-gray-900">{{ $businessSettings->site_name ?: __('admin/business-setting/index.not_set') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.site_settings.site_status') }}</label>
                            <p class="text-gray-900">
                                @if($businessSettings->site_public)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        {{ __('admin/business-setting/index.site_settings.public') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        {{ __('admin/business-setting/index.site_settings.private') }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.site_settings.reply_email') }}</label>
                            <p class="text-gray-900">{{ $businessSettings->reply_email ?: __('admin/business-setting/index.not_set') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.site_settings.google_analytics_id') }}</label>
                            <p class="text-gray-900">{{ $businessSettings->google_analytics_id ?: __('admin/business-setting/index.not_set') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-image text-orange-600 mr-2"></i>
                        {{ __('admin/business-setting/index.description_image.title') }}
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.description_image.shop_description.label') }}</label>
                            <p class="text-gray-900 text-sm">
                                {{ $businessSettings->shop_description ?: __('admin/business-setting/index.description_image.shop_description.not_set') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.description_image.top_image.label') }}</label>
                            @if($businessSettings->top_image_path)
                                <div class="mt-2">
                                    <img src="{{ asset($businessSettings->path_top_image_url) }}"
                                         alt="Top Image"
                                         class="h-32 w-full object-cover rounded-lg">
                                </div>
                            @else
                                <p class="text-gray-500">{{ __('admin/business-setting/index.description_image.top_image.not_set') }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/business-setting/index.description_image.access_information.label') }}</label>
                            <p class="text-gray-900 text-sm">
                                {{ $businessSettings->access_information ?: __('admin/business-setting/index.description_image.access_information.not_set') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-file-contract text-indigo-600 mr-2"></i>
                        {{ __('admin/business-setting/index.policies_terms.title') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/business-setting/index.policies_terms.terms_of_use.label') }}</label>
                            <div class="bg-gray-50 rounded-lg p-3 max-h-32 overflow-y-auto">
                                <p class="text-sm text-gray-700">
                                    {{ $businessSettings->terms_of_use ?: __('admin/business-setting/index.policies_terms.terms_of_use.not_set') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/business-setting/index.policies_terms.privacy_policy.label') }}</label>
                            <div class="bg-gray-50 rounded-lg p-3 max-h-32 overflow-y-auto">
                                <p class="text-sm text-gray-700">
                                    {{ $businessSettings->privacy_policy ?: __('admin/business-setting/index.policies_terms.privacy_policy.not_set') }}
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('admin/business-setting/index.not_found.title') }}</h3>
                <p class="text-gray-500 mb-4">{{ __('admin/business-setting/index.not_found.description') }}</p>
                <a href="{{ route('admin.business-setting.edit', 1) }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-plus mr-2"></i>
                    {{ __('admin/business-setting/index.not_found.create_button') }}
                </a>
            </div>
        @endif
    </div>
</x-layouts.app>
