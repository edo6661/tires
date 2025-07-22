<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6" x-data="menuCreate()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <a href="{{ route('admin.menu.index') }}" class="hover:text-blue-600">Menu Management</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-gray-900">Add New Menu</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Menu</h1>
                <p class="text-gray-600 mt-1">Create a new service menu for customers</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menu.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Back
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
        <form action="{{ route('admin.menu.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Menu Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name"
                               value="{{ old('name') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                               placeholder="Enter menu name"
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="required_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Required Time (minutes) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="required_time" 
                               id="required_time"
                               value="{{ old('required_time') }}"
                               min="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('required_time') border-red-500 @enderror"
                               placeholder="30"
                               required>
                        @error('required_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Estimated processing time in minutes</p>
                    </div>
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price ($)
                        </label>
                        <input type="number" 
                               name="price" 
                               id="price"
                               value="{{ old('price') }}"
                               min="0"
                               step="0.01"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-500 @enderror"
                               placeholder="0.00">
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea name="description" 
                                  id="description"
                                  rows="4"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                  placeholder="Enter menu description...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Visual Settings</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Menu Color
                    </label>
                    <div class="grid grid-cols-5 gap-3">
                        @php
                            $colors = [
                                '#3B82F6' => 'Blue',
                                '#10B981' => 'Green', 
                                '#F59E0B' => 'Yellow',
                                '#EF4444' => 'Red',
                                '#8B5CF6' => 'Purple',
                                '#EC4899' => 'Pink',
                                '#06B6D4' => 'Cyan',
                                '#84CC16' => 'Lime',
                                '#F97316' => 'Orange',
                                '#6B7280' => 'Gray'
                            ];
                            $selectedColor = old('color', '#3B82F6');
                        @endphp
                        @foreach($colors as $colorValue => $colorName)
                            <label class="relative cursor-pointer group">
                                <input type="radio" 
                                       name="color" 
                                       value="{{ $colorValue }}"
                                       {{ $selectedColor === $colorValue ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-12 h-12 rounded-full border-4 border-transparent peer-checked:border-gray-800 peer-checked:shadow-lg transition-all duration-200 flex items-center justify-center group-hover:scale-105"
                                     style="background-color: {{ $colorValue }}"
                                     title="{{ $colorName }}">
                                    <div class="opacity-0 peer-checked:opacity-100 transition-opacity duration-200">
                                        <svg class="w-5 h-5 text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <span class="block text-xs text-center text-gray-600 mt-1 font-medium">{{ $colorName }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('color')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-3">This color will be used for the menu display</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Settings</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <label class="text-sm font-medium text-gray-700">Active Status</label>
                            <p class="text-sm text-gray-500">Menu will be available for customers</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', '1') ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6" x-show="previewData.name || previewData.price || previewData.time">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Menu Preview</h3>
                <div class="max-w-sm">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold text-sm shadow-sm"
                                 :style="'background-color: ' + selectedColor"
                                 x-text="previewData.name ? previewData.name.substring(0, 2).toUpperCase() : 'MN'">
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900" 
                                     x-text="previewData.name || 'Menu Name'"></div>
                                <div class="text-xs text-gray-500" 
                                     x-text="previewData.time ? previewData.time + ' minutes' : '- minutes'"></div>
                            </div>
                        </div>
                        <div class="text-sm font-medium text-gray-900" 
                             x-text="previewData.price ? '$' + parseFloat(previewData.price).toLocaleString() : '$0'"></div>
                        <div class="text-xs text-gray-500 mt-1 line-clamp-2" 
                             x-text="previewData.description || 'No description available'"></div>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.menu.index') }}" 
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Save Menu
                </button>
            </div>
        </form>
    </div>
    <script>
        function menuCreate() {
            return {
                selectedColor: '{{ old("color", "#3B82F6") }}',
                previewData: {
                    name: '{{ old("name", "") }}',
                    price: '{{ old("price", "") }}',
                    time: '{{ old("required_time", "") }}',
                    description: '{{ old("description", default: "") }}'
                },
                init() {
                    this.$watch('previewData', () => {
                        this.updatePreview();
                    });
                    this.setupFormListeners();
                },
                setupFormListeners() {
                    const nameInput = document.getElementById('name');
                    nameInput?.addEventListener('input', (e) => {
                        this.previewData.name = e.target.value;
                    });
                    const priceInput = document.getElementById('price');
                    priceInput?.addEventListener('input', (e) => {
                        this.previewData.price = e.target.value;
                    });
                    const timeInput = document.getElementById('required_time');
                    timeInput?.addEventListener('input', (e) => {
                        this.previewData.time = e.target.value;
                    });
                    const descInput = document.getElementById('description');
                    descInput?.addEventListener('input', (e) => {
                        this.previewData.description = e.target.value;
                    });
                    const colorInputs = document.querySelectorAll('input[name="color"]');
                    colorInputs.forEach(input => {
                        input.addEventListener('change', (e) => {
                            if (e.target.checked) {
                                this.selectedColor = e.target.value;
                            }
                        });
                    });
                },
                updatePreview() {
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