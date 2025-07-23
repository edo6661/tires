<x-layouts.app>
    <div class="max-w-7xl mx-auto">
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Business Settings</h1>
                    <p class="text-gray-600 mt-1">Update your business information and operating hours</p>
                </div>
                <a href="{{ route('admin.business-setting.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Settings
                </a>
            </div>
        </div>
        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('admin.business-setting.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-store text-blue-600 mr-2"></i>
                        Basic Information
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label for="shop_name" class="block text-sm font-medium text-gray-700 mb-1">
                                Shop Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="shop_name" 
                                   name="shop_name" 
                                   value="{{ old('shop_name', $businessSettings->shop_name ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number', $businessSettings->phone_number ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                Address <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      required>{{ old('address', $businessSettings->address ?? '') }}</textarea>
                        </div>
                        <div>
                            <label for="website_url" class="block text-sm font-medium text-gray-700 mb-1">Website URL</label>
                            <input type="url" 
                                   id="website_url" 
                                   name="website_url" 
                                   value="{{ old('website_url', $businessSettings->website_url ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https:
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock text-green-600 mr-2"></i>
                        Business Hours <span class="text-red-500">*</span>
                    </h2>
                    <div class="space-y-4" x-data="businessHours()">
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
                            $currentHours = old('business_hours', $businessSettings->business_hours ?? []);
                        @endphp
                        @foreach($days as $day => $dayName)
                            @php
                                $dayData = $currentHours[$day] ?? ['closed' => true];
                                $isClosed = isset($dayData['closed']) && $dayData['closed'];
                                $openTime = $dayData['open'] ?? '09:00';
                                $closeTime = $dayData['close'] ?? '18:00';
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-medium text-gray-900">{{ $dayName }}</h3>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" 
                                               name="business_hours[{{ $day }}][closed]"
                                               value="1"
                                               {{ $isClosed ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50"
                                               x-on:change="toggleDay('{{ $day }}', $event.target.checked)">
                                        <span class="ml-2 text-sm text-gray-700">Closed</span>
                                    </label>
                                </div>
                                <div class="grid grid-cols-2 gap-4" 
                                     x-show="!days.{{ $day }}.closed" 
                                     x-transition>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Open Time</label>
                                        <input type="time" 
                                               name="business_hours[{{ $day }}][open]"
                                               value="{{ $openTime }}"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               x-bind:required="!days.{{ $day }}.closed">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Close Time</label>
                                        <input type="time" 
                                               name="business_hours[{{ $day }}][close]"
                                               value="{{ $closeTime }}"
                                               class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               x-bind:required="!days.{{ $day }}.closed">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-globe text-purple-600 mr-2"></i>
                        Site Settings
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <input type="text" 
                                   id="site_name" 
                                   name="site_name" 
                                   value="{{ old('site_name', $businessSettings->site_name ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="hidden" name="site_public" value="0">
                                <input type="checkbox" 
                                       name="site_public" 
                                       value="1" 
                                       {{ old('site_public', $businessSettings->site_public ?? false) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">Make site public</span>
                            </label>
                        </div>
                        <div>
                            <label for="reply_email" class="block text-sm font-medium text-gray-700 mb-1">Reply Email</label>
                            <input type="email" 
                                   id="reply_email" 
                                   name="reply_email" 
                                   value="{{ old('reply_email', $businessSettings->reply_email ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="google_analytics_id" class="block text-sm font-medium text-gray-700 mb-1">Google Analytics ID</label>
                            <input type="text" 
                                   id="google_analytics_id" 
                                   name="google_analytics_id" 
                                   value="{{ old('google_analytics_id', $businessSettings->google_analytics_id ?? '') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="GA-XXXXXXXXX-X">
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
                            <label for="shop_description" class="block text-sm font-medium text-gray-700 mb-1">Shop Description</label>
                            <textarea id="shop_description" 
                                      name="shop_description" 
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Describe your business...">{{ old('shop_description', $businessSettings->shop_description ?? '') }}</textarea>
                        </div>
                        <div>
                            <label for="access_information" class="block text-sm font-medium text-gray-700 mb-1">Access Information</label>
                            <textarea id="access_information" 
                                      name="access_information" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="How to get to your business...">{{ old('access_information', $businessSettings->access_information ?? '') }}</textarea>
                        </div>
                        <div>
                            <label for="top_image" class="block text-sm font-medium text-gray-700 mb-1">Top Image</label>
                            <input type="file" 
                                   id="top_image" 
                                   name="top_image" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @if($businessSettings && $businessSettings->top_image_path)
                                <div class="mt-2">
                                    <img src="{{ $businessSettings->path_top_image_url }}" 
                                         alt="Current image" 
                                         class="h-20 w-32 object-cover rounded">
                                    <p class="text-xs text-gray-500 mt-1">Current image</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-file-contract text-indigo-600 mr-2"></i>
                    Policies & Terms
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="terms_of_use" class="block text-sm font-medium text-gray-700 mb-1">Terms of Use</label>
                        <textarea id="terms_of_use" 
                                  name="terms_of_use" 
                                  rows="6"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter your terms of use...">{{ old('terms_of_use', $businessSettings->terms_of_use ?? '') }}</textarea>
                    </div>
                    <div>
                        <label for="privacy_policy" class="block text-sm font-medium text-gray-700 mb-1">Privacy Policy</label>
                        <textarea id="privacy_policy" 
                                  name="privacy_policy" 
                                  rows="6"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Enter your privacy policy...">{{ old('privacy_policy', $businessSettings->privacy_policy ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.business-setting.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
    <script>
        function businessHours() {
            return {
                days: {
                    @foreach($days as $day => $dayName)
                        {{ $day }}: {
                            closed: {{ isset($currentHours[$day]['closed']) && $currentHours[$day]['closed'] ? 'true' : 'false' }}
                        },
                    @endforeach
                },
                toggleDay(day, isClosed) {
                    this.days[day].closed = isClosed;
                }
            }
        }
    </script>
</x-layouts.app>