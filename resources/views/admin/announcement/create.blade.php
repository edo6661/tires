<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Pengumuman</h1>
                <p class="text-gray-600 mt-1">Buat pengumuman baru dengan dukungan multilingual</p>
            </div>
            <a href="{{ route('admin.announcement.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
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
                <h3 class="text-lg font-semibold text-gray-900">Informasi Pengumuman</h3>
                <p class="text-sm text-gray-600 mt-1">Isi form di bawah untuk membuat pengumuman baru dengan dukungan bahasa Inggris dan Jepang</p>
            </div>

            <form action="{{ route('admin.announcement.store') }}" method="POST" class="p-6 space-y-6" x-data="announcementForm()">
                @csrf

                <!-- Language Tabs -->
                <div class="border-b border-gray-200">
                    <div class="flex space-x-8">
                        <button type="button" 
                                @click="activeTab = 'en'"
                                :class="activeTab === 'en' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                            <i class="fas fa-globe"></i>
                            English
                        </button>
                        <button type="button"
                                @click="activeTab = 'ja'"
                                :class="activeTab === 'ja' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                            <i class="fas fa-language"></i>
                            Japanese (日本語)
                        </button>
                    </div>
                </div>

                <!-- English Content -->
                <div x-show="activeTab === 'en'" x-transition class="space-y-6">
                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-globe text-blue-600"></i>
                            <h4 class="font-medium text-blue-900">Konten Bahasa Inggris</h4>
                        </div>
                        <p class="text-sm text-blue-700 mt-1">Isi judul dan konten dalam bahasa Inggris</p>
                    </div>

                    <div class="space-y-2">
                        <label for="title_en" class="block text-sm font-medium text-gray-700">
                            Judul (English) <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title_en"
                               name="translations[en][title]"
                               value="{{ old('translations.en.title') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.en.title') border-red-500 @enderror"
                               placeholder="Enter the announcement title in English..."
                               maxlength="255"
                               @input="updatePreview()">
                        @error('translations.en.title')
                            <p class="text-red-500 text-sm flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500">Maksimal 255 karakter</p>
                    </div>

                    <div class="space-y-2">
                        <label for="content_en" class="block text-sm font-medium text-gray-700">
                            Konten (English) <span class="text-red-500">*</span>
                        </label>
                        <textarea id="content_en"
                                  name="translations[en][content]"
                                  rows="8"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.en.content') border-red-500 @enderror"
                                  placeholder="Enter the announcement content in English..."
                                  @input="updatePreview()">{{ old('translations.en.content') }}</textarea>
                        @error('translations.en.content')
                            <p class="text-red-500 text-sm flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500">Tulis konten pengumuman dalam bahasa Inggris</p>
                    </div>
                </div>

                <!-- Japanese Content -->
                <div x-show="activeTab === 'ja'" x-transition class="space-y-6">
                    <div class="bg-green-50 rounded-lg p-4 mb-4">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-language text-green-600"></i>
                            <h4 class="font-medium text-green-900">コンテンツ (Japanese Content)</h4>
                        </div>
                        <p class="text-sm text-green-700 mt-1">Isi judul dan konten dalam bahasa Jepang</p>
                    </div>

                    <div class="space-y-2">
                        <label for="title_ja" class="block text-sm font-medium text-gray-700">
                            タイトル (Japanese Title) <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="title_ja"
                               name="translations[ja][title]"
                               value="{{ old('translations.ja.title') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.ja.title') border-red-500 @enderror"
                               placeholder="日本語でお知らせのタイトルを入力してください..."
                               maxlength="255"
                               @input="updatePreview()">
                        @error('translations.ja.title')
                            <p class="text-red-500 text-sm flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500">最大255文字</p>
                    </div>

                    <div class="space-y-2">
                        <label for="content_ja" class="block text-sm font-medium text-gray-700">
                            コンテンツ (Japanese Content) <span class="text-red-500">*</span>
                        </label>
                        <textarea id="content_ja"
                                  name="translations[ja][content]"
                                  rows="8"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('translations.ja.content') border-red-500 @enderror"
                                  placeholder="日本語でお知らせの内容を入力してください..."
                                  @input="updatePreview()">{{ old('translations.ja.content') }}</textarea>
                        @error('translations.ja.content')
                            <p class="text-red-500 text-sm flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-xs"></i>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-sm text-gray-500">日本語でお知らせの内容を記入してください</p>
                    </div>
                </div>

                <!-- Common Fields -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-semibold text-gray-900 mb-4">Pengaturan Umum</h4>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label for="published_at" class="block text-sm font-medium text-gray-700">
                                Tanggal & Waktu Publikasi
                            </label>
                            <input type="datetime-local"
                                   id="published_at"
                                   name="published_at"
                                   value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('published_at') border-red-500 @enderror"
                                   @input="updatePreview()">
                            @error('published_at')
                                <p class="text-red-500 text-sm flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                            <p class="text-sm text-gray-500">Jika kosong, waktu saat ini akan digunakan</p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2">
                                    <input type="radio"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                           class="text-blue-600 focus:ring-blue-500"
                                           @change="updatePreview()">
                                    <span class="text-sm text-gray-700">Aktif</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input type="radio"
                                           name="is_active"
                                           value="0"
                                           {{ old('is_active') == '0' ? 'checked' : '' }}
                                           class="text-blue-600 focus:ring-blue-500"
                                           @change="updatePreview()">
                                    <span class="text-sm text-gray-700">Tidak Aktif</span>
                                </label>
                            </div>
                            @error('is_active')
                                <p class="text-red-500 text-sm flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-semibold text-gray-900">Preview</h4>
                        <button type="button" @click="showPreview = !showPreview" class="text-blue-600 hover:text-blue-800 text-sm">
                            <span x-text="showPreview ? 'Sembunyikan Preview' : 'Tampilkan Preview'"></span>
                        </button>
                    </div>
                    <div x-show="showPreview" x-transition class="bg-gray-50 rounded-lg p-4">
                        <!-- Language Toggle for Preview -->
                        <div class="flex gap-2 mb-4">
                            <button type="button" 
                                    @click="previewLang = 'en'"
                                    :class="previewLang === 'en' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-3 py-1 rounded text-sm transition-colors">
                                English
                            </button>
                            <button type="button"
                                    @click="previewLang = 'ja'"
                                    :class="previewLang === 'ja' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'"
                                    class="px-3 py-1 rounded text-sm transition-colors">
                                日本語
                            </button>
                        </div>

                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h5 class="font-semibold text-gray-900 mb-2" x-text="getPreviewTitle() || 'Judul pengumuman akan muncul di sini'"></h5>
                            <div class="text-gray-700 text-sm whitespace-pre-wrap" x-text="getPreviewContent() || 'Konten pengumuman akan muncul di sini'"></div>
                            <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-calendar"></i>
                                <span x-text="previewDate"></span>
                                <span class="px-2 py-1 rounded-full text-xs" :class="previewStatus ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    <span x-text="previewStatus ? 'Aktif' : 'Tidak Aktif'"></span>
                                </span>
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs" x-text="previewLang === 'en' ? 'English' : '日本語'"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.announcement.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Simpan Pengumuman
                    </button>
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
                        this.previewDate = 'Tanggal tidak dipilih'; 
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