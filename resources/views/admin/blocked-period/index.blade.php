<x-layouts.app>
    <div class="container space-y-6" x-data="blockedPeriodIndex()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-title-lg font-bold text-main-text">Blocked Period Management</h1>
                <p class="text-main-text/70 mt-1 text-body-md">Manage blocked time periods for reservations</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.blocked-period.create') }}" 
                   class="px-4 py-2 bg-main-button text-white rounded-lg hover:bg-btn-main-hover transition-all duration-300 transform  hover:shadow-lg flex items-center gap-2 text-button-md font-semibold">
                    <i class="fas fa-plus"></i>
                    Add Period
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">Total Periods</p>
                        <p class="text-title-md font-bold text-main-text">{{ $blockedPeriods->total() }}</p>
                    </div>
                    <div class="bg-brand/10 p-3 rounded-full">
                        <i class="fas fa-ban text-brand"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">Currently Active</p>
                        <p class="text-title-md font-bold text-green-600" x-text="activeCount">0</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">Upcoming</p>
                        <p class="text-title-md font-bold text-yellow-600" x-text="upcomingCount">0</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-hourglass-start text-yellow-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">Expired</p>
                        <p class="text-title-md font-bold text-main-text/50" x-text="expiredCount">0</p>
                    </div>
                    <div class="bg-disabled/30 p-3 rounded-full">
                        <i class="fas fa-history text-main-text/50"></i>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    <button @click="show = false" class="text-green-700 hover:text-green-900 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    <button @click="show = false" class="text-red-700 hover:text-red-900 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-heading-lg font-semibold text-main-text">Filters & Search</h3>
                <button @click="showFilters = !showFilters" class="text-link hover:text-link-hover transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span x-text="showFilters ? 'Hide Filters' : 'Show Filters'" class="text-body-md"></span>
                </button>
            </div>
            <div x-show="showFilters"
                 x-cloak 
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 max-h-0"
                 x-transition:enter-end="opacity-100 max-h-96"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="opacity-100 max-h-96"
                 x-transition:leave-end="opacity-0 max-h-0"
                 class="space-y-4 overflow-hidden">
                <form method="GET" action="{{ route('admin.blocked-period.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">Menu</label>
                        <select name="menu_id" class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                            <option value="">All Menus</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" {{ request('menu_id') == $menu->id ? 'selected' : '' }}>
                                    {{ $menu->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">Status</label>
                        <select name="status" class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" 
                               class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" 
                               class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="all_menus" value="1" {{ request('all_menus') ? 'checked' : '' }}
                               class="rounded border-disabled text-brand focus:ring-brand">
                        <label class="ml-2 text-body-md text-main-text">Block All Menus Only</label>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-body-md font-medium text-main-text mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by reason or menu name..."
                               class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand/90 transition-all duration-200 transform  flex items-center gap-2 text-button-md font-semibold flex-1">
                            <i class="fas fa-search"></i>
                            Filter
                        </button>
                        <a href="{{ route('admin.blocked-period.index') }}"
                            class="px-4 py-2 bg-secondary-button text-main-text rounded-lg hover:bg-secondary-button/80 transition-all duration-200 transform  text-button-md flex-1 text-center flex items-center justify-center">
                            Reset
                        </a>
                    </div>

                </form>
            </div>
        </div>

        <div x-show="selectedItems.length > 0"
             x-cloak 
             x-transition:enter="transition-all ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="bg-sub border border-brand/20 rounded-lg p-4 flex items-center justify-between">
            <span class="text-brand text-body-md font-medium">
                <span x-text="selectedItems.length"></span> items selected
            </span>
            <div class="flex gap-2">
                <button @click="deleteSelected()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-all duration-200 transform  text-body-md">
                    <i class="fas fa-trash mr-1"></i>
                    Delete
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-disabled/20">
            <div class="p-6 border-b border-disabled/20">
                <div class="flex items-center justify-between">
                    <h3 class="text-heading-lg font-semibold text-main-text">Blocked Periods List</h3>
                </div>
            </div>
            @if($blockedPeriods->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-sub/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">
                                    <input type="checkbox"
                                        @change="toggleSelectAll($event.target.checked)"
                                        :checked="selectedItems.length === {{ $blockedPeriods->count() }} && {{ $blockedPeriods->count() }} > 0"
                                        class="rounded border-disabled text-brand focus:ring-brand">
                                </th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Menu</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-disabled/20">
                            @foreach($blockedPeriods as $period)
                                <tr class="hover:bg-sub/30 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $period->id }}" 
                                               @change="toggleSelect({{ $period->id }}, $event.target.checked)"
                                               class="rounded border-disabled text-brand focus:ring-brand">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($period->all_menus)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-md font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-ban mr-1"></i>
                                                All Menus
                                            </span>
                                        @else
                                            <div class="flex items-center gap-2">
                                                @if($period->menu && $period->menu->color)
                                                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $period->menu->color }}"></div>
                                                @endif
                                                <span class="font-medium text-body-md text-main-text">{{ $period->menu ? $period->menu->name : 'Menu not found' }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-body-md text-main-text">
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
                                    <td class="px-6 py-4 whitespace-nowrap text-body-md text-main-text/70">
                                        {{ $period->getDurationText() }}
                                    </td>
                                    <td class="px-6 py-4 text-body-md text-main-text max-w-xs">
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
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-md font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-circle text-green-500 mr-1 text-xs"></i>
                                                Active
                                            </span>
                                        @elseif($isUpcoming)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-md font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock text-yellow-500 mr-1 text-xs"></i>
                                                Upcoming
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-md font-medium bg-disabled/30 text-main-text/70">
                                                <i class="fas fa-history text-main-text/50 mr-1 text-xs"></i>
                                                Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-body-md font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.blocked-period.show', $period->id) }}" 
                                               class="text-brand hover:text-brand/80 transition-all duration-200 transform " title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.blocked-period.edit', $period->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-700 transition-all duration-200 transform " title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button @click="deleteSingle({{ $period->id }})" 
                                                    class="text-red-600 hover:text-red-700 transition-all duration-200 transform " title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-disabled/20">
                    {{ $blockedPeriods->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-ban text-disabled text-6xl mb-4"></i>
                    <h3 class="text-heading-lg font-medium text-main-text mb-2">No blocked periods</h3>
                    <p class="text-main-text/70 mb-4 text-body-md">No blocked periods have been created or match the applied filters.</p>
                    <a href="{{ route('admin.blocked-period.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand/90 transition-all duration-300 transform  hover:shadow-lg text-button-md font-semibold">
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
             x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-heading-lg font-medium text-main-text mt-2">Delete Confirmation</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-body-md text-main-text/70" x-text="deleteMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteModal = false" 
                                class="px-4 py-2 bg-secondary-button text-main-text text-button-md font-medium rounded-md shadow-sm hover:bg-secondary-button/80 transition-all duration-200">
                            Cancel
                        </button>
                        <button @click="confirmDelete()" 
                                class="px-4 py-2 bg-red-600 text-white text-button-md font-medium rounded-md shadow-sm hover:bg-red-700 transition-all duration-200">
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