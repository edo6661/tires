<x-layouts.app>
    <div class="container space-y-6" x-data="tireStorageIndex()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tire Storage Management</h1>
                <p class="text-gray-600 mt-1">Manage customer tire storage data</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.tire-storage.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add Storage
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Storages</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $tireStorages->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-archive text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Active</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $tireStorages->where('status', 'active')->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-play-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Ended</p>
                        <p class="text-2xl font-bold text-gray-600">
                            {{ $tireStorages->where('status', 'ended')->count() }}
                        </p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-full">
                        <i class="fas fa-stop-circle text-gray-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Average Fee</p>
                        <p class="text-2xl font-bold text-purple-600">
                            $ {{ number_format($tireStorages->avg('storage_fee') ?? 0, 2, '.', ',') }}
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
                <form method="GET" action="{{ route('admin.tire-storage.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>Ended</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tire Brand</label>
                        <input type="text" name="tire_brand" value="{{ request('tire_brand') }}" placeholder="Search tire brand..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tire Size</label>
                        <input type="text" name="tire_size" value="{{ request('tire_size') }}" placeholder="Search tire size..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                        <input type="text" name="customer_name" value="{{ request('customer_name') }}" placeholder="Search customer name..." class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="lg:col-span-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.tire-storage.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
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
                <button @click="endSelected()" class="px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 transition-colors duration-200">
                    <i class="fas fa-stop mr-1"></i>
                    End Storage
                </button>
                <button @click="deleteSelected()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Tire Storage List</h3>
                </div>
            </div>
            @if($tireStorages->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox"
                                    @change="toggleSelectAll($event.target.checked)"
                                    :checked="allItemIds.length > 0 && selectedItems.length === allItemIds.length"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tire Info</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($tireStorages as $storage)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $storage->id }}" x-model.number="selectedItems" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-user text-blue-600"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $storage->user->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $storage->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $storage->tire_brand }}</div>
                                            <div class="text-sm text-gray-500">Size: {{ $storage->tire_size }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div>
                                            <div class="font-medium">Start: {{ $storage->storage_start_date->format('d/m/Y') }}</div>
                                            <div>End: {{ $storage->planned_end_date->format('d/m/Y') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            $ {{ number_format($storage->storage_fee, 2, '.', ',') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($storage->status->value === 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-play-circle text-green-500 mr-1 text-xs"></i>
                                                {{ $storage->status->label() }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-stop-circle text-gray-500 mr-1 text-xs"></i>
                                                {{ $storage->status->label() }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.tire-storage.show', $storage->id) }}" class="text-blue-600 hover:text-blue-900" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.tire-storage.edit', $storage->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($storage->status->value === 'active')
                                                <button onclick="endStorage({{ $storage->id }})" class="text-orange-600 hover:text-orange-900" title="End Storage">
                                                    <i class="fas fa-stop"></i>
                                                </button>
                                            @endif
                                            @if($storage->status->value === 'ended')
                                                <button @click="deleteSingle({{ $storage->id }})" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $tireStorages->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-archive text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No storage data yet</h3>
                    <p class="text-gray-500 mb-4">No tire storages have been created yet, or none match the applied filters.</p>
                    <a href="{{ route('admin.tire-storage.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-plus"></i>
                        Add Storage
                    </a>
                </div>
            @endif
        </div>
        <div x-show="showDeleteModal" x-transition class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-cloak>
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
    </div>
    <script>
        function tireStorageIndex() {
            return {
                showFilters: false,
                showDeleteModal: false,
                selectedItems: [],
                allItemIds: [], 
                deleteTarget: null,
                deleteMessage: '',
                init() {
                    this.allItemIds = Array.from(document.querySelectorAll('tbody input[type="checkbox"][value]'))
                                        .map(cb => parseInt(cb.value));
                },
                toggleSelectAll(checked) {
                    this.selectedItems = checked ? [...this.allItemIds] : [];
                },
                deleteSingle(id) {
                    this.deleteTarget = [id];
                    this.deleteMessage = 'Are you sure you want to delete this tire storage?';
                    this.showDeleteModal = true;
                },
                deleteSelected() {
                    if (this.selectedItems.length === 0) return;
                    this.deleteTarget = [...this.selectedItems];
                    this.deleteMessage = `Are you sure you want to delete ${this.selectedItems.length} storage item(s)?`;
                    this.showDeleteModal = true;
                },
                async endSelected() {
                    if (this.selectedItems.length === 0) return;
                    if (confirm(`Are you sure you want to end ${this.selectedItems.length} storage item(s)?`)) {
                        try {
                            const response = await fetch('/admin/tire-storage/bulk-end', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ ids: this.selectedItems })
                            });
                            const result = await response.json();
                            if (result.success) {
                                window.location.reload();
                            } else {
                                alert(result.message || 'An error occurred.');
                            }
                        } catch (error) {
                            console.error('Error:', error);
                            alert('An error occurred.');
                        }
                    }
                },
                async confirmDelete() {
                    try {
                        let response;
                        if (this.deleteTarget.length > 1) { 
                            response = await fetch('/admin/tire-storage/bulk-delete', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({ ids: this.deleteTarget })
                            });
                        } else if (this.deleteTarget.length === 1) { 
                            response = await fetch(`/admin/tire-storage/${this.deleteTarget[0]}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                        } else {
                            return; 
                        }
                        if (response.ok) {
                            window.location.reload();
                        } else {
                            const result = await response.json();
                            alert(result.message || 'An error occurred during deletion.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred during deletion.');
                    } finally {
                        this.showDeleteModal = false;
                    }
                }
            }
        }
        async function endStorage(id) {
            if (confirm('Are you sure you want to end this tire storage?')) {
                try {
                    const response = await fetch(`/admin/tire-storage/${id}/end`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        }
                    });
                    const result = await response.json();
                    if (result.success) {
                        window.location.reload();
                    } else {
                        alert(result.message || 'An error occurred.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('An error occurred.');
                }
            }
        }
    </script>
</x-layouts.app>