@props([
    'reservations',
    'menus',
    'statuses'
])
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-filter text-blue-600 mr-2"></i>
                    Filter Reservations
                </h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">
                        {{ $reservations->total() }} total reservations
                    </span>
                </div>
            </div>
            <form method="GET" action="{{ route('admin.reservation.calendar') }}" class="space-y-4">
                <input type="hidden" name="tab" value="list">
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-search text-gray-400 mr-1"></i>
                            Quick Search
                        </label>
                        <input type="text" 
                                name="search" 
                                id="search" 
                                placeholder="Customer name, phone, email..."
                                value="{{ request('search') }}"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all py-2 pl-2">
                    </div>
                    <div>
                        <label for="menu_id" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-utensils text-gray-400 mr-1"></i>
                            Menu
                        </label>
                        <select name="menu_id" id="menu_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all py-2 pl-2">
                            <option value="">All Menus</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}" 
                                        {{ request('menu_id') == $menu->id ? 'selected' : '' }}>
                                    {{ $menu->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-flag text-gray-400 mr-1"></i>
                            Status
                        </label>
                        <select name="status" id="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all py-2 pl-2">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $value => $label)
                                <option value="{{ $value }}" 
                                        {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                            Date Range
                        </label>
                        <button type="button" 
                                onclick="toggleDateRange()" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 transition-all">
                            <i class="fas fa-calendar-week mr-1"></i>
                            Select Range
                        </button>
                    </div>
                </div>
                <div id="dateRangeFilters" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-calendar-plus text-blue-600 mr-1"></i>
                                Start Date
                            </label>
                            <input type="date" 
                                    name="start_date" 
                                    id="start_date" 
                                    value="{{ request('start_date') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all py-2 pl-2">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fas fa-calendar-minus text-blue-600 mr-1"></i>
                                End Date
                            </label>
                            <input type="date" 
                                    name="end_date" 
                                    id="end_date" 
                                    value="{{ request('end_date') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-all py-2 pl-2">
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center pt-4 border-t border-gray-200 flex-wrap sm:gap-2 gap-4">
                    <div class="flex space-x-3">
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            <i class="fas fa-filter mr-2"></i>
                            Apply Filters
                        </button>
                        <a href="{{ route('admin.reservation.calendar', ['tab' => 'list']) }}" 
                            class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-all">
                            <i class="fas fa-times mr-2"></i>
                            Clear All
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            Showing {{ $reservations->firstItem() ?? 0 }} - {{ $reservations->lastItem() ?? 0 }} 
                            of {{ $reservations->total() }} results
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="exportReservations()" 
                                    class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-download mr-1"></i>
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-user text-gray-400"></i>
                            <span>Customer</span>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-clock text-gray-400"></i>
                            <span>Date & Time</span>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-utensils text-gray-400"></i>
                            <span>Menu</span>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-users text-gray-400"></i>
                            <span>People</span>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-flag text-gray-400"></i>
                            <span>Status</span>
                        </div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-cog text-gray-400"></i>
                            <span>Actions</span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($reservations as $reservation)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $reservation->getFullName() }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $reservation->getPhoneNumber() }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">
                                <i class="fas fa-calendar-day text-blue-600 mr-1"></i>
                                {{ $reservation->reservation_datetime->format('d M Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                {{ $reservation->reservation_datetime->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">
                                {{ $reservation->menu->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-stopwatch text-gray-400 mr-1"></i>
                                {{ $reservation->menu->required_time }} minutes
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-users text-gray-400 mr-2"></i>
                                <span class="text-sm text-gray-900 font-medium">
                                    {{ $reservation->number_of_people }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $reservation->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $reservation->status->value === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $reservation->status->value === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $reservation->status->value === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                <i class="fas fa-circle text-xs mr-1"></i>
                                {{ $reservation->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.reservation.show', $reservation->id) }}" 
                                    class="text-blue-600 hover:text-blue-900 transition-colors"
                                    title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.reservation.edit', $reservation->id) }}" 
                                    class="text-indigo-600 hover:text-indigo-900 transition-colors"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($reservation->status->value === 'pending')
                                    <button onclick="updateStatus('{{ route('admin.reservation.confirm', $reservation->id) }}')"
                                            class="text-green-600 hover:text-green-900 transition-colors"
                                            title="Confirm">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                @if(in_array($reservation->status->value, ['pending', 'confirmed']))
                                    <button onclick="updateStatus('{{ route('admin.reservation.cancel', $reservation->id) }}')"
                                            class="text-red-600 hover:text-red-900 transition-colors"
                                            title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="text-gray-600 hover:text-gray-900 transition-colors"
                                            title="More Actions">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                        <div class="py-1">
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-print mr-2"></i>
                                                Print Details
                                            </a>
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-envelope mr-2"></i>
                                                Send Email
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No reservations found</h3>
                                <p class="text-gray-500">Try adjusting your filters or search criteria</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reservations->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <span>Show</span>
                    <span class="font-medium">{{ $reservations->count() }}</span>
                    <span>entries per page</span>
                </div>
                <div class="flex items-center space-x-2">
                    {{ $reservations->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeDateValidation();
    });
    function initializeDateValidation() {
        const startDate = document.getElementById('start_date');
        const endDate = document.getElementById('end_date');
        if (startDate && endDate) {
            startDate.addEventListener('change', function() {
                if (endDate.value && this.value > endDate.value) {
                    endDate.value = this.value;
                }
                endDate.setAttribute('min', this.value);
            });
            endDate.addEventListener('change', function() {
                if (startDate.value && this.value < startDate.value) {
                    startDate.value = this.value;
                }
                startDate.setAttribute('max', this.value);
            });
            if (startDate.value) {
                endDate.setAttribute('min', startDate.value);
            }
            if (endDate.value) {
                startDate.setAttribute('max', endDate.value);
            }
        }
    }
    function toggleDateRange() {
        const dateRangeFilters = document.getElementById('dateRangeFilters');
        dateRangeFilters.classList.toggle('hidden');
    }
    function toggleAdvancedFilters() {
        console.log('Advanced filters toggled');
    }
    function exportReservations() {
        console.log('Export reservations');
    }
    function updateStatus(url) { 
        if (!confirm('Are you sure you want to change the status of this reservation?')) {
            return;
        }
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status');
        });
    }
</script>