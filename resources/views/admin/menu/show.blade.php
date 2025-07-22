<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('admin.menu.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Menu Details</h1>
                </div>
                <p class="text-gray-600">Detailed information for the menu item: {{ $menu->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menu.edit', $menu->id) }}"
                   class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-edit"></i>
                    Edit Menu
                </a>
                <a href="{{ route('admin.menu.index') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-list"></i>
                    Back to List
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 space-y-6">
                    <div class="text-center">
                        @if($menu->photo_path)
                            <img src="{{ asset('storage/' . $menu->photo_path) }}" 
                                 alt="{{ $menu->name }}" 
                                 class="w-full max-w-sm mx-auto rounded-lg object-cover shadow-md">
                        @else
                            <div class="w-32 h-32 mx-auto rounded-lg flex items-center justify-center text-white font-bold text-4xl shadow-md"
                                 style="background-color: {{ $menu->color }}">
                                {{ substr($menu->name, 0, 2) }}
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-center">
                        @if($menu->is_active)
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                Active Menu
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                Inactive Menu
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-col gap-2">
                        <button onclick="toggleStatus({{ $menu->id }})" 
                                class="w-full px-4 py-2 text-white rounded-lg transition-colors duration-200 flex items-center justify-center gap-2 {{ $menu->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                            <i class="fas fa-{{ $menu->is_active ? 'pause' : 'play' }}"></i>
                            {{ $menu->is_active ? 'Deactivate' : 'Activate' }} Menu
                        </button>
                        <button onclick="deleteMenu({{ $menu->id }})" 
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-trash"></i>
                            Delete Menu
                        </button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-6">Menu Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Menu Name</label>
                                <p class="text-lg font-semibold text-gray-900">{{ $menu->name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                                <p class="text-2xl font-bold text-green-600">
                                    $ {{ number_format($menu->price, 0, '.', ',') }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Required Time</label>
                                <p class="text-lg text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-clock text-blue-600"></i>
                                    {{ $menu->required_time }} minutes
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                                <p class="text-lg text-gray-900">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        #{{ $menu->display_order }}
                                    </span>
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Menu Color</label>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full border-2 border-gray-300" 
                                         style="background-color: {{ $menu->color }}"></div>
                                    <p class="text-gray-900 font-mono">{{ $menu->color }}</p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date Created</label>
                                <p class="text-gray-900">
                                    {{ $menu->created_at->format('F d, Y, H:i') }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Updated</label>
                                <p class="text-gray-900">
                                    {{ $menu->updated_at->format('F d, Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($menu->description)
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Menu Description</h3>
            <div class="prose max-w-none">
                <p class="text-gray-700 leading-relaxed">{{ $menu->description }}</p>
            </div>
        </div>
        @endif


        <div x-data="{ showDeleteModal: false }" @show-delete-modal.window="showDeleteModal = true" x-cloak>
            <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div @click.away="showDeleteModal = false" class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mt-2">Confirm Deletion</h3>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500">
                                Are you sure you want to delete the menu item "{{ $menu->name }}"? This action cannot be undone.
                            </p>
                        </div>
                        <div class="items-center px-4 py-3 flex gap-2 justify-center">
                            <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                                Cancel
                            </button>
                            <button onclick="confirmDelete()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function toggleStatus(id) {
            try {
                const response = await fetch(`/admin/menu/${id}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.error || 'An error occurred while changing the status.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while changing the status.');
            }
        }

        function deleteMenu(id) {
            
            window.dispatchEvent(new CustomEvent('show-delete-modal'));
        }

        async function confirmDelete() {
            try {
                const response = await fetch(`/admin/menu/{{ $menu->id }}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const result = await response.json();
                if (result.success) {
                    window.location.href = "{{ route('admin.menu.index') }}";
                } else {
                    alert(result.message || 'An error occurred while deleting the menu item.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while deleting the menu item.');
            }
        }
    </script>
</x-layouts.app>