<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6" x-data="blockedPeriodIndex()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Blocked Period Management</h1>
                <p class="text-gray-600 mt-1">Manage blocked time periods for reservations</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.blocked-period.create') }}" 
                   class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    Add Period
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Periods</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $blockedPeriods->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-ban text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Currently Active</p>
                        <p class="text-2xl font-bold text-green-600" x-text="activeCount">0</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Upcoming</p>
                        <p class="text-2xl font-bold text-yellow-600" x-text="upcomingCount">0</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-hourglass-start text-yellow-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Expired</p>
                        <p class="text-2xl font-bold text-gray-500" x-text="expiredCount">0</p>
                    </div>
                    <div class="bg-gray-100 p-3 rounded-full">
                        <i class="fas fa-history text-gray-500"></i>
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
                <form method="GET" action="{{ route('admin.blocked-period.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Menu</label>
                        <select name="menu_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Menus</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" {{ request('menu_id') == $menu->id ? 'selected' : '' }}>
                                    {{ $menu->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="all_menus" value="1" {{ request('all_menus') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label class="ml-2 text-sm text-gray-700">Block All Menus Only</label>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by reason or menu name..."
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.blocked-period.index') }}" 
                           class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
        <div x-show="selectedItems.length > 0" 
             class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
            <span class="text-blue-700">
                <span x-text="selectedItems.length"></span> items selected
            </span>
            <div class="flex gap-2">
                <button @click="deleteSelected()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Blocked Periods List</h3>
                    
                </div>
            </div>
            @if($blockedPeriods->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox"
                                        @change="toggleSelectAll($event.target.checked)"
                                        :checked="selectedItems.length === {{ $blockedPeriods->count() }} && {{ $blockedPeriods->count() }} > 0"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Menu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($blockedPeriods as $period)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $period->id }}" 
                                               @change="toggleSelect({{ $period->id }}, $event.target.checked)"
                                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($period->all_menus)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-ban mr-1"></i>
                                                All Menus
                                            </span>
                                        @else
                                            <div class="flex items-center gap-2">
                                                @if($period->menu && $period->menu->color)
                                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $period->menu->color }}"></div>
                                                @endif
                                                <span class="font-medium">{{ $period->menu ? $period->menu->name : 'Menu not found' }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-play text-green-500 text-xs"></i>
                                                {{ $period->start_datetime->format('d/m/Y H:i') }}
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <i class="fas fa-stop text-red-500 text-xs"></i>
                                                {{ $period->end_datetime->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $period->getDurationText() }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                        <div class="truncate" title="{{ $period->reason }}">
                                            {{ $period->reason }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $now = now();
                                            $isActive = $now->between($period->start_datetime, $period->end_datetime);
                                            $isUpcoming = $period->start_datetime > $now;
                                            $isExpired = $period->end_datetime < $now;
                                        @endphp
                                        @if($isActive)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-circle text-green-500 mr-1 text-xs"></i>
                                                Active
                                            </span>
                                        @elseif($isUpcoming)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock text-yellow-500 mr-1 text-xs"></i>
                                                Upcoming
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-history text-gray-500 mr-1 text-xs"></i>
                                                Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.blocked-period.show', $period->id) }}" 
                                               class="text-blue-600 hover:text-blue-900" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.blocked-period.edit', $period->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button @click="deleteSingle({{ $period->id }})" 
                                                    class="text-red-600 hover:text-red-900" title="Delete">
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
                    {{ $blockedPeriods->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-ban text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No blocked periods</h3>
                    <p class="text-gray-500 mb-4">No blocked periods have been created or match the applied filters.</p>
                    <a href="{{ route('admin.blocked-period.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-plus"></i>
                        Add First Period
                    </a>
                </div>
            @endif
        </div>
        <div x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">Delete Confirmation</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" x-text="deleteMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteModal = false" 
                                class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                            Cancel
                        </button>
                        <button @click="confirmDelete()" 
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function blockedPeriodIndex() {
            return {
                showFilters: false,
                showDeleteModal: false,
                selectedItems: [],
                deleteTarget: null,
                deleteMessage: '',
                activeCount: {{ $blockedPeriods->where('start_datetime', '<=', now())->where('end_datetime', '>=', now())->count() }},
                upcomingCount: {{ $blockedPeriods->where('start_datetime', '>', now())->count() }},
                expiredCount: {{ $blockedPeriods->where('end_datetime', '<', now())->count() }},
                init() {
                    this.calculateStats();
                },
                calculateStats() {
                    // Stats are already calculated in the backend
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
                    this.deleteMessage = 'Are you sure you want to delete this blocked period?';
                    this.showDeleteModal = true;
                },
                deleteSelected() {
                    this.deleteTarget = [...this.selectedItems];
                    this.deleteMessage = `Are you sure you want to delete ${this.selectedItems.length} blocked periods?`;
                    this.showDeleteModal = true;
                },
                async confirmDelete() {
                    try {
                        const response = await fetch('{{ route("admin.blocked-period.bulk-delete") }}', {
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
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while deleting');
                    }
                    this.showDeleteModal = false;
                }
            }
        }
    </script>
</x-layouts.app>