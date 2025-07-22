<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Add Announcement</h1>
                <p class="text-gray-600 mt-1">Create a new announcement for customers</p>
            </div>
            <a href="{{ route('admin.announcement.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back
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
                <h3 class="text-lg font-semibold text-gray-900">Announcement Information</h3>
                <p class="text-sm text-gray-600 mt-1">Fill out the form below to create a new announcement</p>
            </div>
            <form action="{{ route('admin.announcement.store') }}" method="POST" class="p-6 space-y-6" x-data="announcementForm()">
                @csrf
                <div class="space-y-2">
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Announcement Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="title"
                           name="title"
                           value="{{ old('title') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                           placeholder="Enter the announcement title..."
                           maxlength="255">
                    @error('title')
                        <p class="text-red-500 text-sm flex items-center gap-1">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-sm text-gray-500">Maximum 255 characters</p>
                </div>
                <div class="space-y-2">
                    <label for="content" class="block text-sm font-medium text-gray-700">
                        Announcement Content <span class="text-red-500">*</span>
                    </label>
                    <textarea id="content"
                              name="content"
                              rows="8"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('content') border-red-500 @enderror"
                              placeholder="Enter the announcement content..."
                              @input="updatePreview()">{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-sm flex items-center gap-1">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-sm text-gray-500">Write the announcement content in plain text</p>
                </div>
                <div class="space-y-2">
                    <label for="published_at" class="block text-sm font-medium text-gray-700">
                        Publication Date & Time
                    </label>
                    <input type="datetime-local"
                           id="published_at"
                           name="published_at"
                           value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('published_at') border-red-500 @enderror">
                    @error('published_at')
                        <p class="text-red-500 text-sm flex items-center gap-1">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                    <p class="text-sm text-gray-500">If left empty, the current time will be used</p>
                </div>
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2">
                            <input type="radio"
                                   name="is_active"
                                   value="1"
                                   {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                   class="text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Active</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="radio"
                                   name="is_active"
                                   value="0"
                                   {{ old('is_active') == '0' ? 'checked' : '' }}
                                   class="text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Inactive</span>
                        </label>
                    </div>
                    @error('is_active')
                        <p class="text-red-500 text-sm flex items-center gap-1">
                            <i class="fas fa-exclamation-circle text-xs"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-semibold text-gray-900">Preview</h4>
                        <button type="button" @click="showPreview = !showPreview" class="text-blue-600 hover:text-blue-800 text-sm">
                            <span x-text="showPreview ? 'Hide Preview' : 'Show Preview'"></span>
                        </button>
                    </div>
                    <div x-show="showPreview" x-transition class="bg-gray-50 rounded-lg p-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <h5 class="font-semibold text-gray-900 mb-2" x-text="previewTitle || 'The announcement title will appear here'"></h5>
                            <div class="text-gray-700 text-sm whitespace-pre-wrap" x-text="previewContent || 'The announcement content will appear here'"></div>
                            <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-calendar"></i>
                                <span x-text="previewDate"></span>
                                <span class="px-2 py-1 rounded-full text-xs" :class="previewStatus ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                    <span x-text="previewStatus ? 'Active' : 'Inactive'"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.announcement.index') }}"
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Save Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function announcementForm() {
            return {
                showPreview: false,
                previewTitle: '',
                previewContent: '',
                previewDate: '',
                previewStatus: true,
                init() {
                    this.updatePreview();
                    document.getElementById('title').addEventListener('input', () => this.updatePreview());
                    document.getElementById('content').addEventListener('input', () => this.updatePreview());
                    document.getElementById('published_at').addEventListener('input', () => this.updatePreview());
                    document.querySelectorAll('input[name="is_active"]').forEach(radio => {
                        radio.addEventListener('change', () => this.updatePreview());
                    });
                },
                updatePreview() {
                    this.previewTitle = document.getElementById('title').value;
                    this.previewContent = document.getElementById('content').value;
                    const publishedAt = document.getElementById('published_at').value;
                    if (publishedAt) {
                        const date = new Date(publishedAt);
                        this.previewDate = date.toLocaleDateString('en-US', { 
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } else {
                        this.previewDate = 'No date selected'; 
                    }
                    const activeRadio = document.querySelector('input[name="is_active"]:checked');
                    this.previewStatus = activeRadio ? activeRadio.value === '1' : true;
                }
            }
        }
    </script>
    <style>
        /* Custom styles for textarea */
        textarea:focus {
            outline: none;
        }
    </style>
</x-layouts.app>