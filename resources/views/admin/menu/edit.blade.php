<x-layouts.app>
    <div class="max-w-4xl mx-auto space-y-6" x-data="menuEdit()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <a href="{{ route('admin.menu.index') }}" class="hover:text-blue-600">Menu Management</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-gray-900">Edit Menu</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Menu</h1>
                <p class="text-gray-600 mt-1">Update menu information</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menu.show', $menu->id) }}"
                   class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-eye"></i>
                    View Menu
                </a>
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
        <form action="{{ route('admin.menu.update', $menu->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
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
                               value="{{ old('name', $menu->name) }}"
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
                               value="{{ old('required_time', $menu->required_time) }}"
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
                               value="{{ old('price', $menu->price) }}"
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
                                  placeholder="Enter menu description...">{{ old('description', $menu->description) }}</textarea>
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
                            $selectedColor = old('color', $menu->color ?? '#3B82F6');
                        @endphp
                        @foreach($colors as $colorValue => $colorName)
                            <label class="relative cursor-pointer group">
                                <input type="radio" 
                                       name="color" 
                                       value="{{ $colorValue }}"
                                       {{ $selectedColor === $colorValue ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-12 h-12 rounded-full border-4 border-transparent peer-checked:border-gray-800 peer-checked:shadow-lg transition-all duration-200 flex items-center justify-center group-"
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
                                   {{ old('is_active', $menu->is_active) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Menu Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Created:</span>
                        <div class="font-medium">{{ $menu->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Last Updated:</span>
                        <div class="font-medium">{{ $menu->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                    <div>
                        <span class="text-gray-600">Status:</span>
                        <div class="font-medium">
                            <span class="px-2 py-1 rounded-full text-xs {{ $menu->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $menu->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
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
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ route('admin.menu.index') }}" 
                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                        Cancel
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <button type="button"
                            @click="showDeleteConfirmation()"
                            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-trash"></i>
                        Delete Menu
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        Update Menu
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
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Konfirmasi Hapus Menu</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Apakah Anda yakin ingin menghapus menu <strong>"{{ $menu->name }}"</strong>? 
                            Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteModal = false" 
                                class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                            Batal
                        </button>
                        <button @click="confirmDelete()" 
                                :disabled="isDeleting"
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isDeleting">Hapus Menu</span>
                            <span x-show="isDeleting" class="flex items-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i>
                                Menghapus...
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
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Berhasil!</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" x-text="successMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button @click="redirectToIndex()" 
                                class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700 w-full">
                            OK
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
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Terjadi Kesalahan</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" x-text="errorMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <button @click="showErrorModal = false" 
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 w-full">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function menuEdit() {
            return {
                selectedColor: '{{ old("color", $menu->color ?? "#3B82F6") }}',
                previewData: {
                    name: '{{ old("name", $menu->name) }}',
                    price: '{{ old("price", $menu->price) }}',
                    time: '{{ old("required_time", $menu->required_time) }}',
                    description: '{{ old("description", $menu->description) }}'
                },
                showDeleteModal: false,
                showSuccessModal: false,
                showErrorModal: false,
                isDeleting: false,
                successMessage: '',
                errorMessage: '',
                
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
                },
                
                showDeleteConfirmation() {
                    this.showDeleteModal = true;
                },
                
                async confirmDelete() {
                    this.isDeleting = true;
                    const menuId = {{ $menu->id }};
                    
                    try {
                        const response = await fetch(`/admin/menu/${menuId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                               document.querySelector('input[name="_token"]').value,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            }
                        });
                        
                        const data = await response.json();
                        
                        this.showDeleteModal = false;
                        this.isDeleting = false;
                        
                        if (data.success) {
                            this.successMessage = 'Menu berhasil dihapus!';
                            this.showSuccessModal = true;
                        } else {
                            this.errorMessage = data.message || 'Gagal menghapus menu';
                            this.showErrorModal = true;
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showDeleteModal = false;
                        this.isDeleting = false;
                        this.errorMessage = 'Terjadi kesalahan saat menghapus menu';
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