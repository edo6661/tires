<x-layouts.app>
    <div class="container space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin/announcement/edit.title') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('admin/announcement/edit.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.announcement.index') }}"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('admin/announcement/edit.back_button') }}
                </a>
            </div>
        </div>

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

        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('admin/announcement/edit.form.card_title') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('admin/announcement/edit.form.card_subtitle') }}</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <i class="fas fa-calendar"></i>
                        <span>{{ __('admin/announcement/edit.form.created_at') }}: {{ $announcement->created_at->format('d M Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.announcement.update', ['locale' => app()->getLocale(), 'announcement' => $announcement->id]) }}" method="POST" class="p-6 space-y-6" x-data="announcementForm()">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-200">
                    <div class="flex space-x-8">
                        <button type="button"
                                @click="activeTab = 'en'"
                                :class="activeTab === 'en' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                            <i class="fas fa-globe"></i>
                            {{ __('admin/announcement/edit.form.tabs.english') }}
                            @if($announcement->translations->where('locale', 'en')->first())
                                <i class="fas fa-check-circle text-green-500 text-xs" title="{{ __('admin/announcement/edit.form.tabs.translation_filled_tooltip') }}"></i>
                            @endif
                        </button>
                        <button type="button"
                                @click="activeTab = 'ja'"
                                :class="activeTab === 'ja' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                            <i class="fas fa-language"></i>
                            {{ __('admin/announcement/edit.form.tabs.japanese') }}
                            @if($announcement->translations->where('locale', 'ja')->first())
                                <i class="fas fa-check-circle text-green-500 text-xs" title="{{ __('admin/announcement/edit.form.tabs.translation_filled_tooltip') }}"></i>
                            @endif
                        </button>
                    </div>
                </div>

                <div x-show="activeTab === 'en'" x-transition class="space-y-6">
                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-globe text-blue-600"></i>
                            <h4 class="font-medium text-blue-900">{{ __('admin/announcement/edit.form.english_section.title') }}</h4>
                        </div>
                        <p class="text-sm text-blue-700 mt-1">{{ __('admin/announcement/edit.form.english_section.subtitle') }}</p>
                    </div>

                    <div class="space-y-2">
                        <label for="title_en" class="block text-sm font-medium text-gray-700">
                            {{ __('admin/announcement/edit.form.english_section.label_title') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title_en"
                               name="translations[en][title]"
                               value="{{ old('translations.en.title', $announcement->translations->where('locale', 'en')->first()?->title ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.en.title') border-red-500 @enderror"
                               placeholder="{{ __('admin/announcement/edit.form.english_section.placeholder_title') }}"
                               maxlength="255"
                               @input="updatePreview()">
                        @error('translations.en.title')
                            <p class="text-red-500 text-sm flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500">{{ __('admin/announcement/edit.form.english_section.max_chars') }}</p>
                    </div>

                    <div class="space-y-2">
                        <label for="content_en" class="block text-sm font-medium text-gray-700">
                            {{ __('admin/announcement/edit.form.english_section.label_content') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="content_en"
                                  name="translations[en][content]"
                                  rows="8"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.en.content') border-red-500 @enderror"
                                  placeholder="{{ __('admin/announcement/edit.form.english_section.placeholder_content') }}"
                                  @input="updatePreview()">{{ old('translations.en.content', $announcement->translations->where('locale', 'en')->first()?->content ?? '') }}</textarea>
                        @error('translations.en.content')
                            <p class="text-red-500 text-sm flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500">{{ __('admin/announcement/edit.form.english_section.help_content') }}</p>
                    </div>
                </div>

                <div x-show="activeTab === 'ja'" x-transition class="space-y-6">
                    <div class="bg-green-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-language text-green-600"></i>
                            <h4 class="font-medium text-green-900">{{ __('admin/announcement/edit.form.japanese_section.title') }}</h4>
                        </div>
                        <p class="text-sm text-green-700 mt-1">{{ __('admin/announcement/edit.form.japanese_section.subtitle') }}</p>
                    </div>

                    <div class="space-y-2">
                        <label for="title_ja" class="block text-sm font-medium text-gray-700">
                            {{ __('admin/announcement/edit.form.japanese_section.label_title') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title_ja"
                               name="translations[ja][title]"
                               value="{{ old('translations.ja.title', $announcement->translations->where('locale', 'ja')->first()?->title ?? '') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.ja.title') border-red-500 @enderror"
                               placeholder="{{ __('admin/announcement/edit.form.japanese_section.placeholder_title') }}"
                               maxlength="255"
                               @input="updatePreview()">
                        @error('translations.ja.title')
                            <p class="text-red-500 text-sm flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500">{{ __('admin/announcement/edit.form.japanese_section.max_chars') }}</p>
                    </div>

                    <div class="space-y-2">
                        <label for="content_ja" class="block text-sm font-medium text-gray-700">
                            {{ __('admin/announcement/edit.form.japanese_section.label_content') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="content_ja"
                                  name="translations[ja][content]"
                                  rows="8"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.ja.content') border-red-500 @enderror"
                                  placeholder="{{ __('admin/announcement/edit.form.japanese_section.placeholder_content') }}"
                                  @input="updatePreview()">{{ old('translations.ja.content', $announcement->translations->where('locale', 'ja')->first()?->content ?? '') }}</textarea>
                        @error('translations.ja.content')
                            <p class="text-red-500 text-sm flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500">{{ __('admin/announcement/edit.form.japanese_section.help_content') }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">{{ __('admin/announcement/edit.form.common_settings.title') }}</h4>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="published_at" class="block text-sm font-medium text-gray-700">
                                {{ __('admin/announcement/edit.form.common_settings.publish_date_label') }}
                            </label>
                            <input type="datetime-local"
                                   id="published_at"
                                   name="published_at"
                                   value="{{ old('published_at', $announcement->published_at ? $announcement->published_at->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('published_at') border-red-500 @enderror"
                                   @input="updatePreview()">
                            @error('published_at')
                                <p class="text-red-500 text-sm flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-sm text-gray-500">{{ __('admin/announcement/edit.form.common_settings.publish_date_help') }}</p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">{{ __('admin/announcement/edit.form.common_settings.status_label') }}</label>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', $announcement->is_active) == '1' ? 'checked' : '' }}
                                           class="text-blue-600 focus:ring-blue-500"
                                           @change="updatePreview()">
                                    <span class="text-sm text-gray-700">{{ __('admin/announcement/edit.form.common_settings.status_active') }}</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio"
                                           name="is_active"
                                           value="0"
                                           {{ old('is_active', $announcement->is_active) == '0' ? 'checked' : '' }}
                                           class="text-blue-600 focus:ring-blue-500"
                                           @change="updatePreview()">
                                    <span class="text-sm text-gray-700">{{ __('admin/announcement/edit.form.common_settings.status_inactive') }}</span>
                                </label>
                            </div>
                            @error('is_active')
                                <p class="text-red-500 text-sm flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <div class="flex items-center gap-2 text-xs">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                <span class="text-gray-600">{{ __('admin/announcement/edit.form.common_settings.current_status') }}
                                    <span class="px-2 py-1 rounded-full text-xs {{ $announcement->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $announcement->is_active ? __('admin/announcement/edit.form.common_settings.status_active') : __('admin/announcement/edit.form.common_settings.status_inactive') }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">{{ __('admin/announcement/edit.form.translation_info.title') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-globe text-blue-500"></i>
                                <span class="text-sm font-medium text-gray-700">{{ __('admin/announcement/edit.form.translation_info.language_en') }}</span>
                            </div>
                            @if($announcement->translations->where('locale', 'en')->first())
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs flex items-center gap-1">
                                    <i class="fas fa-check"></i>
                                    {{ __('admin/announcement/edit.form.translation_info.available') }}
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs flex items-center gap-1">
                                    <i class="fas fa-times"></i>
                                    {{ __('admin/announcement/edit.form.translation_info.not_available') }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-language text-green-500"></i>
                                <span class="text-sm font-medium text-gray-700">{{ __('admin/announcement/edit.form.translation_info.language_ja') }}</span>
                            </div>
                            @if($announcement->translations->where('locale', 'ja')->first())
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs flex items-center gap-1">
                                    <i class="fas fa-check"></i>
                                    {{ __('admin/announcement/edit.form.translation_info.available') }}
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs flex items-center gap-1">
                                    <i class="fas fa-times"></i>
                                    {{ __('admin/announcement/edit.form.translation_info.not_available') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('admin/announcement/edit.form.announcement_info.title') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-plus text-gray-400"></i>
                            <span>{{ __('admin/announcement/edit.form.announcement_info.created_at') }}: {{ $announcement->created_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-edit text-gray-400"></i>
                            <span>{{ __('admin/announcement/edit.form.announcement_info.updated_at') }}: {{ $announcement->updated_at->format('d M Y H:i') }}</span>
                        </div>
                        @if($announcement->published_at)
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-check text-gray-400"></i>
                            <span>{{ __('admin/announcement/edit.form.announcement_info.published_at') }}: {{ $announcement->published_at->format('d M Y H:i') }}</span>
                        </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <i class="fas fa-hashtag text-gray-400"></i>
                            <span>{{ __('admin/announcement/edit.form.announcement_info.id') }}: {{ $announcement->id }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-semibold text-gray-900">{{ __('admin/announcement/edit.form.preview.title') }}</h4>
                        <button type="button" @click="showPreview = !showPreview" class="text-blue-600 hover:text-blue-800 text-sm">
                            <span x-text="showPreview ? '{{ __('admin/announcement/edit.form.preview.hide_button') }}' : '{{ __('admin/announcement/edit.form.preview.show_button') }}'"></span>
                        </button>
                    </div>
                    <div x-show="showPreview" x-transition class="bg-gray-50 rounded-lg p-4">
                        <div class="flex gap-2 mb-4">
                            <button type="button"
                                    @click="previewLang = 'en'"
                                    :class="previewLang === 'en' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-3 py-1 rounded text-sm transition-colors">
                                {{ __('admin/announcement/edit.form.preview.preview_lang_en') }}
                            </button>
                            <button type="button"
                                    @click="previewLang = 'ja'"
                                    :class="previewLang === 'ja' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-3 py-1 rounded text-sm transition-colors">
                                {{ __('admin/announcement/edit.form.preview.preview_lang_ja') }}
                            </button>
                        </div>

                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h5 class="font-semibold text-gray-900 mb-2" x-text="getPreviewTitle() || '{{ __('admin/announcement/edit.form.preview.placeholder_title') }}'"></h5>
                            <div class="text-gray-700 text-sm whitespace-pre-wrap" x-text="getPreviewContent() || '{{ __('admin/announcement/edit.form.preview.placeholder_content') }}'"></div>
                            <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-calendar"></i>
                                <span x-text="previewDate"></span>
                                <span class="px-2 py-1 rounded-full text-xs" :class="previewStatus ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    <span x-text="previewStatus ? '{{ __('admin/announcement/edit.form.preview.status_active') }}' : '{{ __('admin/announcement/edit.form.preview.status_inactive') }}'"></span>
                                </span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs" x-text="previewLang === 'en' ? '{{ __('admin/announcement/edit.form.preview.preview_lang_en') }}' : '{{ __('admin/announcement/edit.form.preview.preview_lang_ja') }}'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.announcement.index') }}"
                           class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                           {{ __('admin/announcement/edit.form.buttons.cancel') }}
                        </a>
                        <a href="{{ route('admin.announcement.show', ['locale' => app()->getLocale(), 'announcement' => $announcement->id]) }}"
                           class="px-6 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-eye"></i>
                            {{ __('admin/announcement/edit.form.buttons.view_details') }}
                        </a>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-save"></i>
                            {{ __('admin/announcement/edit.form.buttons.update') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function announcementForm() {
            return {
                activeTab: 'en',
                showPreview: false,
                previewLang: 'en',
                previewDate: '',
                previewStatus: true,
                
                init() {
                    this.updatePreview();
                },
                
                updatePreview() {
                    const publishedAt = document.getElementById('published_at').value;
                    if (publishedAt) {
                        const date = new Date(publishedAt);
                        this.previewDate = date.toLocaleDateString('id-ID', { 
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } else {
                        this.previewDate = '{{ __('admin/announcement/edit.form.preview.date_not_selected') }}'; 
                    }
                    
                    const activeRadio = document.querySelector('input[name="is_active"]:checked');
                    this.previewStatus = activeRadio ? activeRadio.value === '1' : true;
                },

                getPreviewTitle() {
                    if (this.previewLang === 'en') {
                        return document.getElementById('title_en')?.value || '';
                    } else {
                        return document.getElementById('title_ja')?.value || '';
                    }
                },

                getPreviewContent() {
                    if (this.previewLang === 'en') {
                        return document.getElementById('content_en')?.value || '';
                    } else {
                        return document.getElementById('content_ja')?.value || '';
                    }
                }
            }
        }
    </script>

    <style>
        textarea:focus {
            outline: none;
        }
        
        [x-transition] {
            transition: all 0.3s ease;
        }
        
        .border-red-500:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }
    </style>
</x-layouts.app>