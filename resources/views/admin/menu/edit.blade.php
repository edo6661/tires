<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6" x-data="menuEdit()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <a href="{{ route('admin.menu.index') }}" class="hover:text-blue-600">{{ __('admin/menu.title') }}</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-gray-900">{{ __('admin/menu.edit') }}</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin/menu.edit_title') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('admin/menu.edit_subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menu.show', $menu->id) }}"
                   class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-eye"></i>
                    {{ __('admin/menu.view_menu') }}
                </a>
                <a href="{{ route('admin.menu.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('admin/menu.back') }}
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    <button @click="show = false" class="text-green-700 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    <button @click="show = false" class="text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <form action="{{ route('admin.menu.update', $menu->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-5 bg-blue-600 rounded-sm flex items-center justify-center">
                        <span class="text-white text-xs font-bold">EN</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin/menu.english_info') }}</h3>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="translations_en_name" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin/menu.menu_name_en') }} <span class="text-red-500">*</span>
                        </label>
                        @php
                            $enTranslation = $menu->translations->where('locale', 'en')->first();
                        @endphp
                        <input type="text"
                               name="translations[en][name]"
                               id="translations_en_name"
                               value="{{ old('translations.en.name', $enTranslation->name ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.en.name') border-red-500 @enderror"
                               placeholder="{{ __('admin/menu.placeholder_name_en') }}"
                               required>
                        @error('translations.en.name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="translations_en_description" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin/menu.description_en') }}
                        </label>
                        <textarea name="translations[en][description]"
                                  id="translations_en_description"
                                  rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.en.description') border-red-500 @enderror"
                                  placeholder="{{ __('admin/menu.placeholder_desc_en') }}">{{ old('translations.en.description', $enTranslation->description ?? '') }}</textarea>
                        @error('translations.en.description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-5 bg-red-600 rounded-sm flex items-center justify-center">
                        <span class="text-white text-xs font-bold">JA</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin/menu.japanese_info') }}</h3>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="translations_ja_name" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin/menu.menu_name_ja') }} <span class="text-red-500">*</span>
                        </label>
                        @php
                            $jaTranslation = $menu->translations->where('locale', 'ja')->first();
                        @endphp
                        <input type="text"
                               name="translations[ja][name]"
                               id="translations_ja_name"
                               value="{{ old('translations.ja.name', $jaTranslation->name ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.ja.name') border-red-500 @enderror"
                               placeholder="{{ __('admin/menu.placeholder_name_ja') }}"
                               required>
                        @error('translations.ja.name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="translations_ja_description" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin/menu.description_ja') }}
                        </label>
                        <textarea name="translations[ja][description]"
                                  id="translations_ja_description"
                                  rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.ja.description') border-red-500 @enderror"
                                  placeholder="{{ __('admin/menu.placeholder_desc_ja') }}">{{ old('translations.ja.description', $jaTranslation->description ?? '') }}</textarea>
                        @error('translations.ja.description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin/menu.basic_settings') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="required_time" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin/menu.form_required_time') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="required_time"
                               id="required_time"
                               value="{{ old('required_time', $menu->required_time) }}"
                               min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('required_time') border-red-500 @enderror"
                               placeholder="30"
                               required>
                        @error('required_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">{{ __('admin/menu.help_required_time') }}</p>
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ __('admin/menu.form_price') }}
                        </label>
                        <input type="number"
                               name="price"
                               id="price"
                               value="{{ old('price', $menu->price) }}"
                               min="0"
                               step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin/menu.visual_settings') }}</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
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
                            $selectedColor = old('color', $menu->color ?? '#3B82F6');
                        @endphp
                        @foreach($colors as $colorValue => $colorKey)
                            <label class="relative cursor-pointer group">
                                <input type="radio"
                                       name="color"
                                       value="{{ $colorValue }}"
                                       {{ $selectedColor === $colorValue ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-12 h-12 rounded-full border-4 border-transparent peer-checked:border-gray-800 peer-checked:shadow-lg transition-all duration-200 flex items-center justify-center"
                                     style="background-color: {{ $colorValue }}"
                                     title="{{ __('admin/menu.colors.' . $colorKey) }}">
                                    <div class="opacity-0 peer-checked:opacity-100 transition-opacity duration-200">
                                        <svg class="w-5 h-5 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <span class="block text-xs text-center text-gray-600 mt-1 font-medium">{{ __('admin/menu.colors.' . $colorKey) }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('color')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-3">{{ __('admin/menu.help_color') }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin/menu.status_settings') }}</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">{{ __('admin/menu.form_active_status') }}</label>
                            <p class="text-sm text-gray-500">{{ __('admin/menu.help_active_status') }}</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', $menu->is_active) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin/menu.current_info') }}</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">{{ __('admin/menu.info_created_at') }}:</span>
                        <div class="font-medium">{{ $menu->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ __('admin/menu.info_updated_at') }}:</span>
                        <div class="font-medium">{{ $menu->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ __('admin/menu.status') }}:</span>
                        <div class="font-medium">
                            <span class="px-2 py-1 rounded-full text-xs {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $menu->is_active ? __('admin/menu.active') : __('admin/menu.inactive') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6" x-show="previewData.en.name || previewData.ja.name">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('admin/menu.menu_preview') }}</h3>
                <div class="flex border-b border-gray-200 mb-4">
                    <button type="button"
                            @click="previewLang = 'en'"
                            :class="previewLang === 'en' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                            class="px-4 py-2 border-b-2 font-medium text-sm">
                        {{ __('admin/menu.english_preview') }}
                    </button>
                    <button type="button"
                            @click="previewLang = 'ja'"
                            :class="previewLang === 'ja' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500'"
                            class="px-4 py-2 border-b-2 font-medium text-sm">
                        {{ __('admin/menu.japanese_preview') }}
                    </button>
                </div>
                <div class="max-w-sm">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm"
                                 :style="'background-color: ' + selectedColor">
                                <span x-text="(previewLang === 'en' ? previewData.en.name : previewData.ja.name) ?
                                             (previewLang === 'en' ? previewData.en.name : previewData.ja.name).substring(0, 2).toUpperCase() : 'MN'"></span>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900"
                                     x-text="previewLang === 'en' ? (previewData.en.name || 'Menu Name') : (previewData.ja.name || 'メニュー名')"></div>
                                <div class="text-xs text-gray-500"
                                     x-text="previewData.time ? previewData.time + ' {{ __('admin/menu.time_unit_minutes_full') }}' : '- {{ __('admin/menu.time_unit_minutes_full') }}'"></div>
                            </div>
                        </div>
                        <div class="text-sm font-medium text-gray-900"
                             x-text="previewData.price ? '$' + parseFloat(previewData.price).toLocaleString() : '$0'"></div>
                        <div class="text-xs text-gray-500 mt-1 line-clamp-2"
                             x-text="previewLang === 'en' ? (previewData.en.description || '{{ __('admin/menu.no_description') }}') : (previewData.ja.description || '{{ __('admin/menu.no_description') }}')"></div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                     <a href="{{ route('admin.menu.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        {{ __('admin/menu.cancel') }}
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <button type="button"
                            @click="showDeleteConfirmation()"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        {{ __('admin/menu.delete_menu_button') }}
                    </button>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        {{ __('admin/menu.update_menu') }}
                    </button>
                </div>
            </div>
        </form>

        <div x-show="showDeleteModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2" x-text="lang.confirm_delete_title"></h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" 
                           x-html="lang.confirm_delete_text.replace(':name', `<strong>${previewData.en.name || previewData.ja.name || ''}</strong>`)"></p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteModal = false"
                                class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                            {{ __('admin/menu.cancel') }}
                        </button>
                        <button @click="confirmDelete()"
                                :disabled="isDeleting"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isDeleting">{{ __('admin/menu.delete_menu_button') }}</span>
                            <span x-show="isDeleting" class="flex items-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span x-text="lang.deleting_text"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showSuccessModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2" x-text="lang.success_title"></h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" x-text="successMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button @click="redirectToIndex()"
                                class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 w-full">
                            {{ __('admin/menu.js_messages.ok') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div x-show="showErrorModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2" x-text="lang.error_title"></h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" x-text="errorMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button @click="showErrorModal = false"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 w-full">
                            {{ __('admin/menu.js_messages.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script>
        function menuEdit() {
            return {
                lang: @json(__('admin/menu.js_messages')),
                selectedColor: '{{ old("color", $menu->color ?? "#3B82F6") }}',
                previewLang: 'en',
                previewData: {
                    en: {
                        name: '{{ old("translations.en.name", $enTranslation->name ?? "") }}',
                        description: '{{ old("translations.en.description", $enTranslation->description ?? "") }}'
                    },
                    ja: {
                        name: '{{ old("translations.ja.name", $jaTranslation->name ?? "") }}',
                        description: '{{ old("translations.ja.description", $jaTranslation->description ?? "") }}'
                    },
                    price: '{{ old("price", $menu->price) }}',
                    time: '{{ old("required_time", $menu->required_time) }}'
                },
                showDeleteModal: false,
                showSuccessModal: false,
                showErrorModal: false,
                isDeleting: false,
                successMessage: '',
                errorMessage: '',
                init() {
                    this.setupFormListeners();
                },
                setupFormListeners() {
                    document.getElementById('translations_en_name')?.addEventListener('input', (e) => this.previewData.en.name = e.target.value);
                    document.getElementById('translations_en_description')?.addEventListener('input', (e) => this.previewData.en.description = e.target.value);
                    document.getElementById('translations_ja_name')?.addEventListener('input', (e) => this.previewData.ja.name = e.target.value);
                    document.getElementById('translations_ja_description')?.addEventListener('input', (e) => this.previewData.ja.description = e.target.value);
                    document.getElementById('price')?.addEventListener('input', (e) => this.previewData.price = e.target.value);
                    document.getElementById('required_time')?.addEventListener('input', (e) => this.previewData.time = e.target.value);
                    document.querySelectorAll('input[name="color"]').forEach(input => {
                        input.addEventListener('change', (e) => {
                            if (e.target.checked) this.selectedColor = e.target.value;
                        });
                    });
                },
                showDeleteConfirmation() {
                    this.showDeleteModal = true;
                },
                async confirmDelete() {
                    this.isDeleting = true;
                    const menuId = {{ $menu->id }};
                    try {
                        const url = '{{ route("admin.menu.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', menuId);
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await response.json();
                        this.showDeleteModal = false;
                        this.isDeleting = false;
                        if (data.success) {
                            this.successMessage = this.lang.delete_success;
                            this.showSuccessModal = true;
                        } else {
                            this.errorMessage = data.message || this.lang.delete_failed;
                            this.showErrorModal = true;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showDeleteModal = false;
                        this.isDeleting = false;
                        this.errorMessage = this.lang.error_occurred_delete;
                        this.showErrorModal = true;
                    }
                },
                redirectToIndex() {
                    window.location.href = '{{ route("admin.menu.index") }}';
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