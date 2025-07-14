<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 space-y-8">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <h1 class="text-2xl font-bold text-gray-900">Reservation Calendar</h1>
                <div class="flex items-center space-x-4 flex-wrap gap-2">
                    <!-- View Toggle -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'date' => request('date', now()->format('Y-m-d'))]) }}" 
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ ($view ?? 'month') === 'month' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            Month
                        </a>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => request('date', now()->format('Y-m-d'))]) }}" 
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ ($view ?? 'month') === 'week' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            Week
                        </a>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => request('date', now()->format('Y-m-d'))]) }}" 
                        class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ ($view ?? 'month') === 'day' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            Day
                        </a>
                    </div>
                    <!-- Date Picker -->
                   <div x-data>
                        <button 
                            @click="$refs.dateSelect.showPicker()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                        >
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Select Date
                        </button>
                        <input 
                            type="date" 
                            x-ref="dateSelect"
                            @change="window.location.href = '{{ route('admin.reservation.calendar') }}?view={{ $view ?? 'month' }}&date=' + $event.target.value"
                            value="{{ request('date', now()->format('Y-m-d')) }}"
                            class="sr-only"
                        />
                    </div>
                </div>
            </div>
        </div>   
        <!-- Calendar Navigation -->
        <div class="bg-white rounded-lg shadow-sm sm:p-6 p-2 space-y-4">
            <div class="flex flex-col gap-4">
                <div class="flex items-center justify-between w-full flex-wrap gap-4">
                    @if(($view ?? 'month') === 'month')
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'month' => $previousMonth]) }}" 
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                            <i class="fas fa-chevron-left mr-2"></i>
                            Previous Month
                        </a>
                        <h2 class="text-xl font-semibold text-gray-900 flex-1 text-center whitespace-nowrap">
                            {{ $currentMonth->format('F Y') }}
                        </h2>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'month' => $nextMonth]) }}" 
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                            Next Month
                            <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    @elseif(($view ?? 'month') === 'week')
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => $previousWeek]) }}" 
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                            <i class="fas fa-chevron-left mr-2"></i>
                            Previous Week
                        </a>
                        <h2 class="text-xl font-semibold text-gray-900 flex-1 text-center whitespace-nowrap">
                            {{ $startOfWeek->format('d M Y') }} - {{ $endOfWeek->format('d M Y') }}
                        </h2>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => $nextWeek]) }}" 
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                            Next Week
                            <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    @else
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => $previousDay]) }}" 
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                            <i class="fas fa-chevron-left mr-2"></i>
                            Previous Day
                        </a>
                        <h2 class="text-xl font-semibold text-gray-900 flex-1 text-center whitespace-nowrap">
                            {{ $currentDate->format('d F Y') }}
                        </h2>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => $nextDay]) }}" 
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                            Next Day
                            <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    @endif
                </div>                
                {{-- <div class="w-full">
                    <!-- Legend -->
                    <div class="flex items-center space-x-4 text-sm justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></div>
                            <span class="text-gray-600">Pending</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full mr-2"></div>
                            <span class="text-gray-600">Confirmed</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-400 rounded-full mr-2"></div>
                            <span class="text-gray-600">Completed</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-red-400 rounded-full mr-2"></div>
                            <span class="text-gray-600">Cancelled</span>
                        </div>
                    </div>
                  
                </div>             --}}
            </div>
            <!-- Calendar Views -->
            @if(($view ?? 'month') === 'month')
                <!-- Month View -->
                <div class="grid grid-cols-7 gap-1 bg-gray-200 rounded-lg p-2">
                    <!-- Day Headers -->
                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                        <div class="bg-gray-50 p-3 text-center text-sm font-medium text-gray-600 rounded">
                            {{ $day }}
                        </div>
                    @endforeach
                    <!-- Calendar Days -->
                    @foreach($calendarDays as $day)
                        <div class="bg-white rounded min-h-[120px] sm:p-2 {{ $day['isCurrentMonth'] ? '' : 'opacity-50' }}">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-900">{{ $day['date']->format('j') }}</span>
                                @if($day['isToday'])
                                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                @endif
                            </div>
                            @if(isset($day['reservations']) && count($day['reservations']) > 0)
                                <div class="space-y-1">
                                    @foreach($day['reservations']->take(3) as $reservation)
                                        <div 
                                            x-data="{ 
                                                showTooltip: false,
                                                reservation: @js($reservation->toArray()),
                                                tooltipPosition: 'bottom',
                                                checkTooltipPosition(event) {
                                                    const rect = event.target.getBoundingClientRect();
                                                    const windowHeight = window.innerHeight;
                                                    const tooltipHeight = 300;
                                                    if (rect.bottom + tooltipHeight > windowHeight) {
                                                        this.tooltipPosition = 'top';
                                                    } else {
                                                        this.tooltipPosition = 'bottom';
                                                    }
                                                }
                                            }"
                                            @mouseenter="showTooltip = true; checkTooltipPosition($event)"
                                            @mouseleave="showTooltip = false"
                                            @click="$dispatch('open-modal', { reservation: reservation })"
                                            class="relative text-xs p-1 rounded cursor-pointer hover:opacity-80 transition-opacity
                                                {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $reservation->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                            "
                                        >
                                            <div class="font-medium">{{ $reservation->user->full_name }}</div>
                                            <div class="text-xs opacity-75">{{ $reservation->reservation_datetime->format('H:i') }}</div>
                                            <!-- Enhanced Tooltip -->
                                            <div 
                                                x-show="showTooltip"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                :class="tooltipPosition === 'top' ? 'bottom-full mb-2' : 'top-full mt-2'"
                                                class="absolute z-50 left-0 w-72 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                                                style="display: none;"
                                            >
                                                <!-- Tooltip Header -->
                                                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                                    <div class="flex justify-between items-center">
                                                        <span class="font-semibold text-gray-900 text-sm">{{ $reservation->reservation_number }}</span>
                                                        <span class="px-2 py-1 text-xs rounded-full font-medium
                                                            {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                            {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                            {{ $reservation->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                            {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                        ">
                                                            {{ $reservation->status->label() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- Tooltip Content -->
                                                <div class="px-4 py-3 space-y-3 text-sm">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reservation->user->full_name }}</div>
                                                            <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email not available' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reservation->menu->name }}</div>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-users text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->number_of_people }} people</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-money-bill-wave text-gray-400 text-xs"></i>
                                                        <span class="font-semibold text-gray-900">Rp {{ number_format($reservation->amount, 0, ',', '.') }}</span>
                                                    </div>
                                                    @if($reservation->notes)
                                                        <div class="bg-gray-50 rounded-lg p-3 mt-3">
                                                            <div class="flex items-start space-x-2">
                                                                <i class="fas fa-sticky-note text-gray-400 text-xs mt-1"></i>
                                                                <div>
                                                                    <div class="text-gray-600 text-xs mb-1">Notes:</div>
                                                                    <div class="text-gray-900 text-xs">{{ $reservation->notes }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- Tooltip Footer -->
                                                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
                                                    Click to view full details
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if(count($day['reservations']) > 3)
                                        <div class="text-xs text-gray-500 text-center py-1">
                                            +{{ count($day['reservations']) - 3 }} more
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif(($view ?? 'month') === 'week')
                <!-- Week View -->
                <div class="flex flex-wrap gap-4 justify-center items-center">
                    @foreach($weekDays as $day)
                        <div class="bg-gray-50 rounded-lg p-4 min-h-[300px]">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-center">
                                    <div class="text-xs text-gray-600 uppercase">{{ $day['date']->format('D') }}</div>
                                    <div class="text-lg font-semibold text-gray-900 {{ $day['isToday'] ? 'text-blue-600' : '' }}">
                                        {{ $day['date']->format('j') }}
                                    </div>
                                </div>
                                @if($day['isToday'])
                                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                @endif
                            </div>
                            @if(count($day['reservations']) > 0)
                                <div class="space-y-2">
                                    @foreach($day['reservations'] as $reservation)
                                        <div 
                                            x-data="{ 
                                                showTooltip: false,
                                                reservation: @js($reservation->toArray()),
                                                tooltipPosition: 'bottom',
                                                checkTooltipPosition(event) {
                                                    const rect = event.target.getBoundingClientRect();
                                                    const windowHeight = window.innerHeight;
                                                    const tooltipHeight = 300;
                                                    if (rect.bottom + tooltipHeight > windowHeight) {
                                                        this.tooltipPosition = 'top';
                                                    } else {
                                                        this.tooltipPosition = 'bottom';
                                                    }
                                                }
                                            }"
                                            @mouseenter="showTooltip = true; checkTooltipPosition($event)"
                                            @mouseleave="showTooltip = false"
                                            @click="$dispatch('open-modal', { reservation: reservation })"
                                            class="relative text-xs p-2 rounded cursor-pointer hover:opacity-80 transition-opacity
                                                {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $reservation->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                            "
                                        >
                                            <div class="font-medium">{{ $reservation->reservation_datetime->format('H:i') }}</div>
                                            <div class="truncate">{{ $reservation->user->full_name }}</div>
                                            <!-- Enhanced Tooltip (same as month view) -->
                                            <div 
                                                x-show="showTooltip"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                :class="tooltipPosition === 'top' ? 'bottom-full mb-2' : 'top-full mt-2'"
                                                class="absolute z-50 left-0 w-72 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                                                style="display: none;"
                                            >
                                                <!-- Tooltip Header -->
                                                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                                    <div class="flex justify-between items-center">
                                                        <span class="font-semibold text-gray-900 text-sm">{{ $reservation->reservation_number }}</span>
                                                        <span class="px-2 py-1 text-xs rounded-full font-medium
                                                            {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                            {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                            {{ $reservation->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                            {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                        ">
                                                            {{ $reservation->status->label() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <!-- Tooltip Content -->
                                                <div class="px-4 py-3 space-y-3 text-sm">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reservation->user->full_name }}</div>
                                                            <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email not available' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reservation->menu->name }}</div>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-users text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->number_of_people }} people</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-money-bill-wave text-gray-400 text-xs"></i>
                                                        <span class="font-semibold text-gray-900">Rp {{ number_format($reservation->amount, 0, ',', '.') }}</span>
                                                    </div>
                                                    @if($reservation->notes)
                                                        <div class="bg-gray-50 rounded-lg p-3 mt-3">
                                                            <div class="flex items-start space-x-2">
                                                                <i class="fas fa-sticky-note text-gray-400 text-xs mt-1"></i>
                                                                <div>
                                                                    <div class="text-gray-600 text-xs mb-1">Notes:</div>
                                                                    <div class="text-gray-900 text-xs">{{ $reservation->notes }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- Tooltip Footer -->
                                                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
                                                    Click to view full details
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Day View -->
                <div class="space-y-2">
                    @foreach($hourlySlots as $slot)
                        <div class="flex border-b border-gray-200 pb-2">
                            <div class="w-16 text-right pr-4 pt-2">
                                <span class="text-sm font-medium text-gray-600">{{ $slot['hour'] }}</span>
                            </div>
                            <div class="flex-1 min-h-[60px] bg-gray-50 rounded-lg p-2">
                                @if(count($slot['reservations']) > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                        @foreach($slot['reservations'] as $reservation)
                                            <div 
                                                x-data="{ 
                                                    showTooltip: false,
                                                    reservation: @js($reservation->toArray()),
                                                    tooltipPosition: 'bottom',
                                                    checkTooltipPosition(event) {
                                                        const rect = event.target.getBoundingClientRect();
                                                        const windowHeight = window.innerHeight;
                                                        const tooltipHeight = 300;
                                                        if (rect.bottom + tooltipHeight > windowHeight) {
                                                            this.tooltipPosition = 'top';
                                                        } else {
                                                            this.tooltipPosition = 'bottom';
                                                        }
                                                    }
                                                }"
                                                @mouseenter="showTooltip = true; checkTooltipPosition($event)"
                                                @mouseleave="showTooltip = false"
                                                @click="$dispatch('open-modal', { reservation: reservation })"
                                                class="relative text-xs p-2 rounded cursor-pointer hover:opacity-80 transition-opacity
                                                    {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $reservation->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                "
                                            >
                                                <div class="font-medium">{{ $reservation->reservation_datetime->format('H:i') }}</div>
                                                <div class="truncate">{{ $reservation->user->full_name }}</div>
                                                <div class="text-xs opacity-75">{{ $reservation->number_of_people }} people</div>
                                                <!-- Enhanced Tooltip (same as other views) -->
                                                <div 
                                                    x-show="showTooltip"
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 transform scale-95"
                                                    x-transition:enter-end="opacity-100 transform scale-100"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 transform scale-100"
                                                    x-transition:leave-end="opacity-0 transform scale-95"
                                                    :class="tooltipPosition === 'top' ? 'bottom-full mb-2' : 'top-full mt-2'"
                                                    class="absolute z-50 left-0 w-72 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                                                    style="display: none;"
                                                >
                                                    <!-- Tooltip Header -->
                                                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                                        <div class="flex justify-between items-center">
                                                            <span class="font-semibold text-gray-900 text-sm">{{ $reservation->reservation_number }}</span>
                                                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                                                {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                                {{ $reservation->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                            ">
                                                                {{ $reservation->status->label() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Tooltip Content -->
                                                    <div class="px-4 py-3 space-y-3 text-sm">
                                                        <div class="flex items-center space-x-3">
                                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-user text-gray-600 text-xs"></i>
                                                            </div>
                                                            <div>
                                                                <div class="font-medium text-gray-900">{{ $reservation->user->full_name }}</div>
                                                                <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email not available' }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-3">
                                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-utensils text-gray-600 text-xs"></i>
                                                            </div>
                                                            <div>
                                                                <div class="font-medium text-gray-900">{{ $reservation->menu->name }}</div>
                                                                
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-3">
                                                            <div class="flex items-center space-x-2">
                                                                <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                                <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                                                            </div>
                                                            <div class="flex items-center space-x-2">
                                                                <i class="fas fa-users text-gray-400 text-xs"></i>
                                                                <span class="text-gray-600">{{ $reservation->number_of_people }} people</span>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-money-bill-wave text-gray-400 text-xs"></i>
                                                            <span class="font-semibold text-gray-900">Rp {{ number_format($reservation->amount, 0, ',', '.') }}</span>
                                                        </div>
                                                        @if($reservation->notes)
                                                            <div class="bg-gray-50 rounded-lg p-3 mt-3">
                                                                <div class="flex items-start space-x-2">
                                                                    <i class="fas fa-sticky-note text-gray-400 text-xs mt-1"></i>
                                                                    <div>
                                                                        <div class="text-gray-600 text-xs mb-1">Notes:</div>
                                                                        <div class="text-gray-900 text-xs">{{ $reservation->notes }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <!-- Tooltip Footer -->
                                                    <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
                                                        Click to view full details
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <!-- Loading State -->
    <div 
        x-data="{ loading: false }"
        x-show="loading"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-40"
        style="display: none;"
    >
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Loading data...</span>
            </div>
        </div>
    </div>
    <!-- Scripts for Enhanced Functionality -->
    <script>
        setInterval(() => {
            const currentUrl = new URL(window.location.href);
            const view = currentUrl.searchParams.get('view') || 'month';
            const date = currentUrl.searchParams.get('date') || new Date().toISOString().split('T')[0];
            if (!document.querySelector('[x-show="showModal"]').style.display !== 'none' && 
                !document.querySelector('[x-show="showTooltip"]')) {
                console.log('Auto-refresh triggered');
            }
        }, 30000);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.dispatchEvent(new CustomEvent('close-modals'));
            }
            if (e.key === 'ArrowLeft' && e.ctrlKey) {
                const prevButton = document.querySelector('a[href*="previous"]');
                if (prevButton) prevButton.click();
            }
            if (e.key === 'ArrowRight' && e.ctrlKey) {
                const nextButton = document.querySelector('a[href*="next"]');
                if (nextButton) nextButton.click();
            }
        });
        let touchStartX = 0;
        let touchEndX = 0;
        document.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        });
        document.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        });
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    const nextButton = document.querySelector('a[href*="next"]');
                    if (nextButton) nextButton.click();
                } else {
                    const prevButton = document.querySelector('a[href*="previous"]');
                    if (prevButton) prevButton.click();
                }
            }
        }
        function printCalendar() {
            const printWindow = window.open('', '_blank');
            const calendarContent = document.querySelector('.calendar-container').innerHTML;
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Reservation Calendar</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            .no-print { display: none !important; }
                            @media print {
                                body { margin: 0; }
                                .tooltip { display: none !important; }
                            }
                        </style>
                    </head>
                    <body>
                        <h1>Reservation Calendar</h1>
                        ${calendarContent}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
        function exportCalendar() {
            const currentUrl = new URL(window.location.href);
            const view = currentUrl.searchParams.get('view') || 'month';
            const date = currentUrl.searchParams.get('date') || new Date().toISOString().split('T')[0];
            window.location.href = `/admin/reservations/calendar/export?view=${view}&date=${date}`;
        }
    </script>
</x-layouts.app>