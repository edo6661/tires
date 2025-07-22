<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6" x-data="menuIndex()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Menu Management</h1>
                <p class="text-gray-600 mt-1">Manage service menus for customers</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menu.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add Menu
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Menus</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $menus->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-utensils text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Active</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $menus->where('is_active', true)->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Inactive</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $menus->where('is_active', false)->count() }}
                        </p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Average Price</p>
                        <p class="text-2xl font-bold text-purple-600">
                            $ {{ number_format($menus->avg('price') ?? 0, 0, '.', ',') }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-money-bill-wave text-purple-600"></i>
                    </div>
                </div>
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
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Filters & Search</h3>
                <button @click="showFilters = !showFilters" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-filter"></i>
                    <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'"></span>
                </button>
            </div>
            <div x-show="showFilters" x-transition class="space-y-4">
                <form method="GET" action="{{ route('admin.menu.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Price Range</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Price Range</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="1000000" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search for menu name..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="lg:col-span-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.menu.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div x-show="selectedItems.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
            <span class="text-blue-700">
                <span x-text="selectedItems.length"></span> item(s) selected
            </span>
            <div class="flex gap-2">
                <button @click="toggleStatusSelected(true)" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-check mr-1"></i>
                    Activate
                </button>
                <button @click="toggleStatusSelected(false)" class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors duration-200">
                    <i class="fas fa-pause mr-1"></i>
                    Deactivate
                </button>
                <button @click="deleteSelected()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
                <button @click="openReorderModal()" class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition-colors duration-200">
                    <i class="fas fa-sort mr-1"></i>
                    Reorder
                </button>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Menu List</h3>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" @change="toggleSelectAll($event.target.checked)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label class="text-sm text-gray-700">Select All</label>
                    </div>
                </div>
            </div>
            @if($menus->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" @change="toggleSelectAll($event.target.checked)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Required</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($menus as $menu)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $menu->id }}" @change="toggleSelect({{ $menu->id }}, $event.target.checked)" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($menu->photo_path)
                                                <img src="{{ asset('storage/' . $menu->photo_path) }}" 
                                                     alt="{{ $menu->name }}" 
                                                     class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold"
                                                     style="background-color: {{ $menu->color }}">
                                                    {{ substr($menu->name, 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $menu->name }}</div>
                                                @if($menu->description)
                                                    <div class="text-sm text-gray-500 max-w-xs truncate" title="{{ $menu->description }}">
                                                        {{ Str::limit($menu->description, 40) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            $ {{ number_format($menu->price, 0, '.', ',') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $menu->required_time }} mins
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            #{{ $menu->display_order }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($menu->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle text-green-500 mr-1 text-xs"></i>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle text-red-500 mr-1 text-xs"></i>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.menu.show', $menu->id) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.menu.edit', $menu->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="toggleStatus({{ $menu->id }})" class="text-{{ $menu->is_active ? 'red' : 'green' }}-600 hover:text-{{ $menu->is_active ? 'red' : 'green' }}-900" title="{{ $menu->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $menu->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button @click="deleteSingle({{ $menu->id }})" class="text-red-600 hover:text-red-900" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $menus->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-utensils text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No menus yet</h3>
                    <p class="text-gray-500 mb-4">No menus have been created, or none match the applied filters.</p>
                    <a href="{{ route('admin.menu.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-plus"></i>
                        Add Menu
                    </a>
                </div>
            @endif
        </div>
        <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Confirm Deletion</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" x-text="deleteMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                            Cancel
                        </button>
                        <button @click="confirmDelete()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div x-show="showReorderModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border max-w-2xl shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Set Menu Order</h3>
                    <p class="text-sm text-gray-600 mb-4">Drag and drop items to change the order. Click and hold the grip icon to move an item.</p>
                    <div class="space-y-2 max-h-96 overflow-y-auto" id="sortable-menu">
                        @foreach($menus as $menu)
                            <div class="flex items-center gap-3 p-3 border rounded-lg cursor-move bg-white hover:bg-gray-50 transition-colors" data-id="{{ $menu->id }}">
                                <div class="drag-handle cursor-grab active:cursor-grabbing">
                                    <i class="fas fa-grip-vertical text-gray-400 hover:text-gray-600"></i>
                                </div>
                                <div class="flex items-center gap-3 flex-1">
                                    @if($menu->photo_path)
                                        <img src="{{ asset('storage/' . $menu->photo_path) }}" 
                                             alt="{{ $menu->name }}" 
                                             class="w-8 h-8 rounded object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded flex items-center justify-center text-white text-xs font-bold"
                                             style="background-color: {{ $menu->color }}">
                                            {{ substr($menu->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $menu->name }}</div>
                                        <div class="text-xs text-gray-500">Current order: #{{ $menu->display_order }}</div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <i class="fas fa-arrows-alt-v"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex gap-2 justify-end">
                        <button @click="showReorderModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                            Cancel
                        </button>
                        <button @click="saveReorder()" class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700">
                            Save Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function menuIndex() {
            return {
                showFilters: false,
                showDeleteModal: false,
                showReorderModal: false,
                selectedItems: [],
                deleteTarget: null,
                deleteMessage: '',
                sortableInstance: null,
                init() {
                    this.$nextTick(() => {
                        this.initSortable();
                    });
                },
                initSortable() {
                    const sortableContainer = document.getElementById('sortable-menu');
                    if (sortableContainer && !this.sortableInstance) {
                        this.sortableInstance = window.Sortable.create(sortableContainer, {
                            handle: '.drag-handle',
                            animation: 200,
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            dragClass: 'sortable-drag',
                            onStart: function (evt) {
                                evt.item.classList.add('opacity-75');
                            },
                            onEnd: function (evt) {
                                evt.item.classList.remove('opacity-75');
                            }
                        });
                    }
                },
                openReorderModal() {
                    this.showReorderModal = true;
                    this.$nextTick(() => {
                        this.initSortable();
                    });
                },
                toggleSelectAll(checked) {
                    const checkboxes = document.querySelectorAll('input[type="checkbox"][value]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = checked;
                        const id = parseInt(checkbox.value);
                        if (checked && !this.selectedItems.includes(id)) {
                            this.selectedItems.push(id);
                        } else if (!checked) {
                            const index = this.selectedItems.indexOf(id);
                            if (index > -1) {
                                this.selectedItems.splice(index, 1);
                            }
                        }
                    });
                },
                toggleSelect(id, checked) {
                    if (checked && !this.selectedItems.includes(id)) {
                        this.selectedItems.push(id);
                    } else if (!checked) {
                        const index = this.selectedItems.indexOf(id);
                        if (index > -1) {
                            this.selectedItems.splice(index, 1);
                        }
                    }
                },
                deleteSingle(id) {
                    this.deleteTarget = [id];
                    this.deleteMessage = 'Are you sure you want to delete this menu?';
                    this.showDeleteModal = true;
                },
                deleteSelected() {
                    if (this.selectedItems.length === 0) return;
                    this.deleteTarget = [...this.selectedItems];
                    this.deleteMessage = `Are you sure you want to delete ${this.selectedItems.length} menu(s)?`;
                    this.showDeleteModal = true;
                },
                async toggleStatusSelected(status) {
                    if (this.selectedItems.length === 0) {
                        alert('Please select at least one menu');
                        return;
                    }
                    try {
                        for (const id of this.selectedItems) {
                            await fetch(`/admin/menu/${id}/toggle-status`, {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json'
                                }
                            });
                        }
                        window.location.reload();
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while changing the status');
                    }
                },
                async confirmDelete() {
                    try {
                        if (this.deleteTarget.length === 1) {
                            const response = await fetch(`/admin/menu/${this.deleteTarget[0]}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            const result = await response.json();
                            if (result.success || response.ok) {
                                window.location.reload();
                            } else {
                                alert(result.message || 'An error occurred while deleting');
                            }
                        } else {
                            const response = await fetch('/admin/menu/bulk-delete', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    ids: this.deleteTarget
                                })
                            });
                            const result = await response.json();
                            if (result.success) {
                                window.location.reload();
                            } else {
                                alert(result.message || 'An error occurred while deleting');
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while deleting');
                    }
                    this.showDeleteModal = false;
                },
                async saveReorder() {
                    const sortableItems = document.querySelectorAll('#sortable-menu > div');
                    const orderData = Array.from(sortableItems).map((item, index) => ({
                        id: parseInt(item.dataset.id),
                        display_order: index + 1
                    }));
                    try {
                        const response = await fetch('/admin/menu/reorder', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ order: orderData })
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.showReorderModal = false;
                            window.location.reload();
                        } else {
                            alert(result.error || 'An error occurred while saving the order');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while saving the order');
                    }
                }
            }
        }
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
                    alert(result.error || 'An error occurred while changing the status');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while changing the status');
            }
        }
    </script>
</x-layouts.app>