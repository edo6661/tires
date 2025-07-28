<x-layouts.app>
    <div class="container" x-data="menuCreate()">
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 mb-6 transform transition-all duration-300 hover:shadow-lg">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-2 text-body-md text-main-text/70 mb-2">
                        <a href="{{ route('admin.menu.index') }}" class="hover:text-brand transition-colors duration-300">{{ __('admin/menu.title') }}</a>
                        <i class="fas fa-chevron-right text-xs"></i>
                        <span class="text-main-text">{{ __('admin/menu.add_menu') }}</span>
                    </div>
                    <h1 class="text-title-lg font-bold text-main-text mb-2">{{ __('admin/menu.add_menu') }}</h1>
                    <p class="text-body-md text-main-text/70">{{ __('admin/menu.add_subtitle') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.menu.index') }}"
                       class="px-4 py-2 bg-secondary-button text-main-text rounded-lg hover:bg-secondary-button/80 transition-all duration-300 transform hover:-translate-y-0.5 flex items-center gap-2 text-button-md">
                        <i class="fas fa-arrow-left"></i>
                        {{ __('admin/menu.back') }}
                    </a>
                </div>
            </div>
        </div>
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 transform transition-all duration-300 hover:shadow-sm" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span class="text-body-md">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-700 hover:text-green-900 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 transform transition-all duration-300 hover:shadow-sm" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                        <span class="text-body-md">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-700 hover:text-red-900 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
        <form action="{{ route('admin.menu.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-5 bg-brand rounded-sm flex items-center justify-center transform transition-all duration-300">
                        <span class="text-white text-xs font-bold">EN</span>
                    </div>
                    <h3 class="text-heading-lg font-semibold text-main-text">{{ __('admin/menu.english_info') }}</h3>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div class="transform transition-all duration-200 ">
                        <label for="translations_en_name" class="block text-body-md font-medium text-main-text mb-2">
                            {{ __('admin/menu.menu_name_en') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="translations[en][name]" 
                               id="translations_en_name"
                               value="{{ old('translations.en.name') }}"
                               class="w-full border border-disabled rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md @error('translations.en.name') border-red-500 @enderror"
                               placeholder="{{ __('admin/menu.placeholder_name_en') }}"
                               required>
                        @error('translations.en.name')
                            <p class="text-red-500 text-body-md mt-1 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="transform transition-all duration-200 ">
                        <label for="translations_en_description" class="block text-body-md font-medium text-main-text mb-2">
                            {{ __('admin/menu.description_en') }}
                        </label>
                        <textarea name="translations[en][description]" 
                                  id="translations_en_description"
                                  rows="4"
                                  class="w-full border border-disabled rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md @error('translations.en.description') border-red-500 @enderror"
                                  placeholder="{{ __('admin/menu.placeholder_desc_en') }}">{{ old('translations.en.description') }}</textarea>
                        @error('translations.en.description')
                            <p class="text-red-500 text-body-md mt-1 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-8 h-5 bg-red-600 rounded-sm flex items-center justify-center transform transition-all duration-300">
                        <span class="text-white text-xs font-bold">JA</span>
                    </div>
                    <h3 class="text-heading-lg font-semibold text-main-text">{{ __('admin/menu.japanese_info') }}</h3>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div class="transform transition-all duration-200 ">
                        <label for="translations_ja_name" class="block text-body-md font-medium text-main-text mb-2">
                            {{ __('admin/menu.menu_name_ja') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="translations[ja][name]" 
                               id="translations_ja_name"
                               value="{{ old('translations.ja.name') }}"
                               class="w-full border border-disabled rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md @error('translations.ja.name') border-red-500 @enderror"
                               placeholder="{{ __('admin/menu.placeholder_name_ja') }}"
                               required>
                        @error('translations.ja.name')
                            <p class="text-red-500 text-body-md mt-1 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="transform transition-all duration-200 ">
                        <label for="translations_ja_description" class="block text-body-md font-medium text-main-text mb-2">
                            {{ __('admin/menu.description_ja') }}
                        </label>
                        <textarea name="translations[ja][description]" 
                                  id="translations_ja_description"
                                  rows="4"
                                  class="w-full border border-disabled rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md @error('translations.ja.description') border-red-500 @enderror"
                                  placeholder="{{ __('admin/menu.placeholder_desc_ja') }}">{{ old('translations.ja.description') }}</textarea>
                        @error('translations.ja.description')
                            <p class="text-red-500 text-body-md mt-1 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg">
                <h3 class="text-heading-lg font-semibold text-main-text mb-6 flex items-center">
                    <i class="fas fa-cog text-brand mr-2 transform transition-all duration-300"></i>
                    {{ __('admin/menu.basic_settings') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="transform transition-all duration-200 ">
                        <label for="required_time" class="block text-body-md font-medium text-main-text mb-2">
                            {{ __('admin/menu.form_required_time') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="required_time" 
                               id="required_time"
                               value="{{ old('required_time') }}"
                               min="1"
                               class="w-full border border-disabled rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md @error('required_time') border-red-500 @enderror"
                               placeholder="30"
                               required>
                        @error('required_time')
                            <p class="text-red-500 text-body-md mt-1 animate-pulse">{{ $message }}</p>
                        @enderror
                        <p class="text-body-md text-main-text/70 mt-1">{{ __('admin/menu.help_required_time') }}</p>
                    </div>
                    <div class="transform transition-all duration-200 ">
                        <label for="price" class="block text-body-md font-medium text-main-text mb-2">
                            {{ __('admin/menu.form_price') }}
                        </label>
                        <input type="number" 
                               name="price" 
                               id="price"
                               value="{{ old('price') }}"
                               min="0"
                               step="0.01"
                               class="w-full border border-disabled rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md @error('price') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('price')
                            <p class="text-red-500 text-body-md mt-1 animate-pulse">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg">
                <h3 class="text-heading-lg font-semibold text-main-text mb-6 flex items-center">
                    <i class="fas fa-palette text-brand mr-2 transform transition-all duration-300"></i>
                    {{ __('admin/menu.visual_settings') }}
                </h3>
                <div>
                    <label class="block text-body-md font-medium text-main-text mb-3">
                        {{ __('admin/menu.info_color') }}
                    </label>
                    <div class="grid grid-cols-5 gap-3">
                        @php
                            $colors = [
                                '#3B82F6' => 'blue',
                                '#10B981' => 'green',
                                '#F59E0B' => 'yellow',
                                '#EF4444' => 'red',
                                '#8B5CF6' => 'purple',
                                '#EC4899' => 'pink',
                                '#06B6D4' => 'cyan',
                                '#84CC16' => 'lime',
                                '#F97316' => 'orange',
                                '#6B7280' => 'gray'
                            ];
                            $selectedColor = old('color', '#3B82F6');
                        @endphp
                        @foreach($colors as $colorValue => $colorKey)
                            <label class="relative cursor-pointer group transform transition-all duration-200 hover:scale-110">
                                <input type="radio" 
                                       name="color" 
                                       value="{{ $colorValue }}"
                                       {{ $selectedColor === $colorValue ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-12 h-12 rounded-full border-4 border-transparent peer-checked:border-main-text peer-checked:shadow-lg transition-all duration-300 flex items-center justify-center hover:shadow-md"
                                     style="background-color: {{ $colorValue }}"
                                     title="{{ __('admin/menu.colors.' . $colorKey) }}">
                                    <div class="opacity-0 peer-checked:opacity-100 transition-opacity duration-200">
                                        <svg class="w-5 h-5 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <span class="block text-xs text-center text-main-text/70 mt-1 font-medium">{{ __('admin/menu.colors.' . $colorKey) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('color')
                        <p class="text-red-500 text-body-md mt-2 animate-pulse">{{ $message }}</p>
                    @enderror
                    <p class="text-body-md text-main-text/70 mt-3">{{ __('admin/menu.help_color') }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg">
                <h3 class="text-heading-lg font-semibold text-main-text mb-6 flex items-center">
                    <i class="fas fa-toggle-on text-brand mr-2 transform transition-all duration-300"></i>
                    {{ __('admin/menu.status_settings') }}
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-sub rounded-lg transform transition-all duration-300 hover:shadow-sm">
                        <div>
                            <label class="text-body-md font-medium text-main-text">{{ __('admin/menu.form_active_status') }}</label>
                            <p class="text-body-md text-main-text/70 mt-1">{{ __('admin/menu.help_active_status') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', '1') ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-disabled peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand"></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg" x-show="previewData.en.name || previewData.ja.name">
                <h3 class="text-heading-lg font-semibold text-main-text mb-6 flex items-center">
                    <i class="fas fa-eye text-brand mr-2 transform transition-all duration-300"></i>
                    {{ __('admin/menu.menu_preview') }}
                </h3>
                <div class="flex border-b border-disabled/50 mb-4">
                    <button type="button" 
                            @click="previewLang = 'en'"
                            :class="previewLang === 'en' ? 'border-brand text-brand' : 'border-transparent text-main-text/70'"
                            class="px-4 py-2 border-b-2 font-medium text-button-md transition-all duration-300 hover:text-brand">
                        {{ __('admin/menu.english_preview') }}
                    </button>
                    <button type="button"
                            @click="previewLang = 'ja'"
                            :class="previewLang === 'ja' ? 'border-brand text-brand' : 'border-transparent text-main-text/70'"
                            class="px-4 py-2 border-b-2 font-medium text-button-md transition-all duration-300 hover:text-brand">
                        {{ __('admin/menu.japanese_preview') }}
                    </button>
                </div>
                <div class="max-w-sm">
                    <div class="border border-disabled/50 rounded-lg p-4 hover:shadow-md transition-all duration-300 hover:-translate-y-1 bg-sub/30">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold text-body-md shadow-sm transform transition-all duration-300"
                                 :style="'background-color: ' + selectedColor">
                                <span x-text="(previewLang === 'en' ? previewData.en.name : previewData.ja.name) ? 
                                             (previewLang === 'en' ? previewData.en.name : previewData.ja.name).substring(0, 2).toUpperCase() : 'MN'"></span>
                            </div>
                            <div class="flex-1">
                                <div class="text-body-md font-medium text-main-text" 
                                     x-text="previewLang === 'en' ? (previewData.en.name || '{{ __('admin/menu.info_name') }}') : (previewData.ja.name || '{{ __('admin/menu.info_name') }}')"></div>
                                <div class="text-body-md text-main-text/70" 
                                     x-text="previewData.time ? previewData.time + ' {{ __('admin/menu.time_unit_minutes_full') }}' : '- {{ __('admin/menu.time_unit_minutes_full') }}'"></div>
                            </div>
                        </div>
                        <div class="text-body-md font-medium text-main-text" 
                             x-text="previewData.price ? '$' + parseFloat(previewData.price).toLocaleString() : '$0'"></div>
                        <div class="text-body-md text-main-text/70 mt-1 line-clamp-2" 
                             x-text="previewLang === 'en' ? (previewData.en.description || '{{ __('admin/menu.no_description') }}') : (previewData.ja.description || '{{ __('admin/menu.no_description') }}')"></div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg">
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('admin.menu.index') }}" 
                       class="px-6 py-2 bg-secondary-button text-main-text rounded-lg hover:bg-secondary-button/80 transition-all duration-300 transform hover:-translate-y-0.5 text-button-md">
                        {{ __('admin/menu.cancel') }}
                    </a>
                    <button type="submit" 
                            class="px-8 py-2 bg-main-button text-white rounded-lg hover:bg-btn-main-hover transition-all duration-300 transform hover:-translate-y-0.5 shadow-md hover:shadow-lg flex items-center gap-2 text-button-lg">
                        <i class="fas fa-save"></i>
                        {{ __('admin/menu.save_menu') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        function menuCreate() {
            return {
                selectedColor: '{{ old("color", "#3B82F6") }}',
                previewLang: 'en',
                previewData: {
                    en: {
                        name: '{{ old("translations.en.name", "") }}',
                        description: '{{ old("translations.en.description", "") }}'
                    },
                    ja: {
                        name: '{{ old("translations.ja.name", "") }}',
                        description: '{{ old("translations.ja.description", "") }}'
                    },
                    price: '{{ old("price", "") }}',
                    time: '{{ old("required_time", "") }}'
                },
                init() {
                    this.setupFormListeners();
                },
                setupFormListeners() {
                    const enNameInput = document.getElementById('translations_en_name');
                    enNameInput?.addEventListener('input', (e) => {
                        this.previewData.en.name = e.target.value;
                    });
                    const enDescInput = document.getElementById('translations_en_description');
                    enDescInput?.addEventListener('input', (e) => {
                        this.previewData.en.description = e.target.value;
                    });
                    const jaNameInput = document.getElementById('translations_ja_name');
                    jaNameInput?.addEventListener('input', (e) => {
                        this.previewData.ja.name = e.target.value;
                    });
                    const jaDescInput = document.getElementById('translations_ja_description');
                    jaDescInput?.addEventListener('input', (e) => {
                        this.previewData.ja.description = e.target.value;
                    });
                    const priceInput = document.getElementById('price');
                    priceInput?.addEventListener('input', (e) => {
                        this.previewData.price = e.target.value;
                    });
                    const timeInput = document.getElementById('required_time');
                    timeInput?.addEventListener('input', (e) => {
                        this.previewData.time = e.target.value;
                    });
                    const colorInputs = document.querySelectorAll('input[name="color"]');
                    colorInputs.forEach(input => {
                        input.addEventListener('change', (e) => {
                            if (e.target.checked) {
                                this.selectedColor = e.target.value;
                            }
                        });
                    });
                }
            }
        }
    </script>
    <style>
        [x-cloak] {
            display: none !important;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;  
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-layouts.app>