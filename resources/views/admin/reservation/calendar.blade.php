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
        <div class="bg-white rounded-lg shadow-sm sm:p-6 p-0 space-y-4">
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
                <div class="grid grid-cols-7 sm:gap-1 gap-0.5 bg-gray-200 rounded-lg">
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
                                <span class="text-sm font-medium {{ $day['isBlocked'] ? 'text-gray-500' : 'text-gray-900' }}">
                                    {{ $day['date']->format('j') }}
                                </span>
                                <div class="flex items-center space-x-1">
                                    @if($day['isToday'])
                                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                            @if($day['isBlocked'])
                                <div class="mb-3">
                                    @foreach($day['blockedPeriods'] as $blockedPeriod)
                                        <div 
                                            x-data="{ 
                                                showTooltip: false,
                                                blockedInfo: @js($blockedPeriod->getTooltipInfo()),
                                                tooltipPosition: 'bottom',
                                                tooltipHorizontal: 'left',
                                                checkTooltipPosition(event) {
                                                    const rect = event.target.getBoundingClientRect();
                                                    const windowHeight = window.innerHeight;
                                                    const windowWidth = window.innerWidth;
                                                    const tooltipHeight = 200;
                                                    const tooltipWidth = 300;
                                                    
                                                    if (rect.bottom + tooltipHeight > windowHeight) {
                                                        this.tooltipPosition = 'top';
                                                    } else {
                                                        this.tooltipPosition = 'bottom';
                                                    }
                                                    
                                                    if (rect.left + tooltipWidth > windowWidth) {
                                                        this.tooltipHorizontal = 'right';
                                                    } else {
                                                        this.tooltipHorizontal = 'left';
                                                    }
                                                }
                                            }"
                                            @mouseenter="showTooltip = true; checkTooltipPosition($event)"
                                            @mouseleave="showTooltip = false"
                                            class="relative mb-2 p-2 bg-red-100 border border-red-300 rounded-lg cursor-pointer transition-all duration-200"
                                        >
                                            <div class="flex flex-col gap-0.5">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs font-medium text-red-800">Blocked Period</span>
                                                </div>
                                                <span class="text-xs text-red-600">
                                                    {{ $blockedPeriod->start_datetime->format('H:i') }} - 
                                                    {{ $blockedPeriod->end_datetime->format('H:i') }}
                                                </span>
                                            </div>
                                            
                                            <!-- Blocked Period Tooltip -->
                                            <div 
                                                x-show="showTooltip"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                :class="{
                                                    'bottom-full mb-2': tooltipPosition === 'top',
                                                    'top-full mt-2': tooltipPosition === 'bottom',
                                                    'left-0': tooltipHorizontal === 'left',
                                                    'right-0': tooltipHorizontal === 'right'
                                                }"
                                                class="absolute z-50 w-72 bg-white rounded-lg shadow-xl border border-red-200 overflow-hidden"
                                                style="display: none;"
                                            >
                                                <!-- Tooltip Arrow -->
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'left'"
                                                    class="absolute -top-2 left-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'right'"
                                                    class="absolute -top-2 right-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'left'"
                                                    class="absolute -bottom-2 left-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'right'"
                                                    class="absolute -bottom-2 right-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>

                                                <!-- Tooltip Header -->
                                                <div class="px-4 py-3 bg-red-50 border-b border-red-200">
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-ban text-red-600"></i>
                                                        <span class="font-semibold text-red-800 text-sm">Blocked Period</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tooltip Content -->
                                                <div class="px-4 py-3 space-y-3 text-sm">
                                                    <!-- Time Range -->
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                        <span class="text-gray-700">
                                                            <span x-text="blockedInfo.start_time"></span> - 
                                                            <span x-text="blockedInfo.end_time"></span>
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Reason -->
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-info-circle text-gray-400 text-xs mt-1"></i>
                                                        <div>
                                                            <div class="text-gray-600 text-xs mb-1">Reason:</div>
                                                            <div class="text-gray-900 text-xs" x-text="blockedInfo.reason"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Menu Status -->
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-utensils text-gray-400 text-xs mt-1"></i>
                                                        <div>
                                                            <div class="text-gray-600 text-xs mb-1">Affected Menu:</div>
                                                            <div class="text-gray-900 text-xs">
                                                                @if($blockedPeriod->all_menus)
                                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                                                        All Menus Blocked
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                                                        {{ $blockedPeriod->menu ? $blockedPeriod->menu->name : 'Specific Menu' }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if(isset($day['reservations']) && count($day['reservations']) > 0)
                                <div class="space-y-4">
                                    @foreach($day['reservations']->take(3) as $reservation)
                                        @php
                                            $menu = $reservation->menu;
                                            $backgroundColor = $menu->getColorWithOpacity(10); 
                                            $borderColor = $menu->color;
                                            $textColor = $menu->getTextColor();
                                        @endphp
                                        
                                        
                                        <div 
                                            x-data="{ 
                                                showTooltip: false,
                                                reservation: @js($reservation->toArray()),
                                                menu: @js($menu->toArray()),
                                                tooltipPosition: 'bottom',
                                                tooltipHorizontal: 'left',
                                                checkTooltipPosition(event) {
                                                    const rect = event.target.getBoundingClientRect();
                                                    const windowHeight = window.innerHeight;
                                                    const windowWidth = window.innerWidth;
                                                    const tooltipHeight = 300;
                                                    const tooltipWidth = 320; // w-80 = 20rem = 320px
                                                    
                                                    // Check vertical position (top/bottom)
                                                    if (rect.bottom + tooltipHeight > windowHeight) {
                                                        this.tooltipPosition = 'top';
                                                    } else {
                                                        this.tooltipPosition = 'bottom';
                                                    }
                                                    
                                                    // Check horizontal position (left/right)
                                                    if (rect.left + tooltipWidth > windowWidth) {
                                                        this.tooltipHorizontal = 'right';
                                                    } else {
                                                        this.tooltipHorizontal = 'left';
                                                    }
                                                }
                                            }"
                                            @mouseenter="showTooltip = true; checkTooltipPosition($event)"
                                            @mouseleave="showTooltip = false"
                                            @click="$dispatch('open-modal', { reservation: reservation })"
                                            class="relative text-xs sm:py-2 sm:px-2 py-2 px-0 rounded-lg cursor-pointer transition-all duration-200 sm:border-l-4 border-l-0"
                                            style="background-color: {{ $backgroundColor }}; border-left-color: {{ $borderColor }}; color: {{ $textColor }};"
                                        >
                                            <div class="flex items-start flex-col gap-2">
                                                <div class="flex-1">
                                                    <div class="font-medium">{{ $reservation->user->full_name }}</div>
                                                </div>
                                                <div class="flex items-start flex-col">
                                                    <div class="text-gray-900">{{ $reservation->reservation_datetime->format('H:i') }}
                                                        - {{ $reservation->reservation_datetime->addMinutes($menu->required_time)->format('H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Enhanced Tooltip dengan posisi kiri/kanan -->
                                            <div 
                                                x-show="showTooltip"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                :class="{
                                                    'bottom-full mb-2': tooltipPosition === 'top',
                                                    'top-full mt-2': tooltipPosition === 'bottom',
                                                    'left-0': tooltipHorizontal === 'left',
                                                    'right-0': tooltipHorizontal === 'right'
                                                }"
                                                class="absolute z-50 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                                                style="display: none;"
                                            >
                                                <!-- Arrow untuk tooltip -->
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'left'"
                                                    class="absolute -top-2 left-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'right'"
                                                    class="absolute -top-2 right-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'left'"
                                                    class="absolute -bottom-2 left-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'right'"
                                                    class="absolute -bottom-2 right-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>

                                                <!-- Tooltip Header dengan Menu Color -->
                                                <div class="px-4 py-3 border-b border-gray-200"
                                                    style="background-color: {{ $backgroundColor }}; border-top: 3px solid {{ $borderColor }};">
                                                    <div class="flex justify-between items-center gap-2">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $borderColor }};"></div>
                                                            <span class="font-semibold text-gray-900 text-sm">{{ $reservation->reservation_number }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                                                {{ $reservation->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                {{ $reservation->status->value === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                                {{ $reservation->status->value === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                {{ $reservation->status->value === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                            ">
                                                                {{ $reservation->status->label() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tooltip Content -->
                                                <div class="px-4 py-3 space-y-3 text-sm">
                                                    <!-- Customer Info -->
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                            style="background-color: {{ $backgroundColor }};">
                                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reservation->user->full_name }}</div>
                                                            <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email not available' }}</div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Menu Info dengan Color -->
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                            style="background-color: {{ $borderColor }};">
                                                            <i class="fas fa-cog text-white text-xs"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900">{{ $menu->name }}</div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Reservation Details -->
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-hourglass-half text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $menu->required_time }} menit</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-users text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->number_of_people }} people</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-money-bill-wave text-gray-400 text-xs"></i>
                                                            <span class="font-semibold text-gray-900">Rp {{ number_format($reservation->amount, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Menu Price Info -->
                                                    @if($menu->price)
                                                        <div class="bg-gray-50 rounded-lg p-3">
                                                            <div class="flex items-center justify-between">
                                                                <div class="text-gray-600 text-xs">Menu Price:</div>
                                                                <div class="font-medium text-gray-900">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Notes -->
                                                    @if($reservation->notes)
                                                        <div class="bg-gray-50 rounded-lg p-3">
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
                                                    <div class="flex items-center justify-between">
                                                        <span>Click to view full details</span>
                                                        <div class="flex items-center space-x-1">
                                                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $borderColor }};"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    @if(count($day['reservations']) > 3)
                                        <div class="text-xs text-gray-500 text-center py-1 bg-gray-50 rounded">
                                            +{{ count($day['reservations']) - 3 }} more reservations
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Legend untuk Menu Colors (optional, bisa ditambahkan di bagian atas calendar) --}}
                            @if(isset($menus) && count($menus) > 0)
                                <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Menu Legend:</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($menus as $menu)
                                            <div class="flex items-center space-x-2 px-2 py-1 rounded-md text-xs"
                                                style="background-color: {{ $menu->getColorWithOpacity(10) }}; border-left: 3px solid {{ $menu->color }};">
                                                <div class="w-2 h-2 rounded-full" style="background-color: {{ $menu->color }};"></div>
                                                <span class="text-gray-700">{{ $menu->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            
            @elseif(($view ?? 'month') === 'week')
                <!-- Week View -->
                <div class="flex flex-wrap gap-4 justify-center">
                    @foreach($weekDays as $day)
                        <div class="bg-gray-50 rounded-lg p-4 min-h-[300px]">
                            <div class="flex items-center justify-between mb-3">
                                <div class="text-center">
                                    <div class="text-xs text-gray-600 uppercase">{{ $day['date']->format('D') }}</div>
                                    <div class="text-lg font-semibold text-gray-900 {{ $day['isToday'] ? 'text-blue-600' : '' }}">
                                        {{ $day['date']->format('j') }}
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($day['isToday'])
                                        <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                    @endif
                                </div>
                            </div>
                            @if($day['isBlocked'])
                                <div class="mb-3">
                                    @foreach($day['blockedPeriods'] as $blockedPeriod)
                                        <div 
                                            x-data="{ 
                                                showTooltip: false,
                                                blockedInfo: @js($blockedPeriod->getTooltipInfo()),
                                                tooltipPosition: 'bottom',
                                                tooltipHorizontal: 'left',
                                                checkTooltipPosition(event) {
                                                    const rect = event.target.getBoundingClientRect();
                                                    const windowHeight = window.innerHeight;
                                                    const windowWidth = window.innerWidth;
                                                    const tooltipHeight = 200;
                                                    const tooltipWidth = 300;
                                                    
                                                    if (rect.bottom + tooltipHeight > windowHeight) {
                                                        this.tooltipPosition = 'top';
                                                    } else {
                                                        this.tooltipPosition = 'bottom';
                                                    }
                                                    
                                                    if (rect.left + tooltipWidth > windowWidth) {
                                                        this.tooltipHorizontal = 'right';
                                                    } else {
                                                        this.tooltipHorizontal = 'left';
                                                    }
                                                }
                                            }"
                                            @mouseenter="showTooltip = true; checkTooltipPosition($event)"
                                            @mouseleave="showTooltip = false"
                                            class="relative mb-2 p-2 bg-red-100 border border-red-300 rounded-lg cursor-pointer transition-all duration-200"
                                        >
                                            <div class="flex flex-col gap-0.5">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs font-medium text-red-800">Blocked Period</span>
                                                </div>
                                                <span class="text-xs text-red-600">
                                                    {{ $blockedPeriod->start_datetime->format('H:i') }} - 
                                                    {{ $blockedPeriod->end_datetime->format('H:i') }}
                                                </span>
                                            </div>
                                            
                                            <!-- Blocked Period Tooltip -->
                                            <div 
                                                x-show="showTooltip"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                :class="{
                                                    'bottom-full mb-2': tooltipPosition === 'top',
                                                    'top-full mt-2': tooltipPosition === 'bottom',
                                                    'left-0': tooltipHorizontal === 'left',
                                                    'right-0': tooltipHorizontal === 'right'
                                                }"
                                                class="absolute z-50 w-72 bg-white rounded-lg shadow-xl border border-red-200 overflow-hidden"
                                                style="display: none;"
                                            >
                                                <!-- Tooltip Arrow -->
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'left'"
                                                    class="absolute -top-2 left-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'right'"
                                                    class="absolute -top-2 right-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'left'"
                                                    class="absolute -bottom-2 left-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'right'"
                                                    class="absolute -bottom-2 right-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>

                                                <!-- Tooltip Header -->
                                                <div class="px-4 py-3 bg-red-50 border-b border-red-200">
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-ban text-red-600"></i>
                                                        <span class="font-semibold text-red-800 text-sm">Blocked Period</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tooltip Content -->
                                                <div class="px-4 py-3 space-y-3 text-sm">
                                                    <!-- Time Range -->
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                        <span class="text-gray-700">
                                                            <span x-text="blockedInfo.start_time"></span> - 
                                                            <span x-text="blockedInfo.end_time"></span>
                                                        </span>
                                                    </div>
                                                    
                                                    <!-- Reason -->
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-info-circle text-gray-400 text-xs mt-1"></i>
                                                        <div>
                                                            <div class="text-gray-600 text-xs mb-1">Reason:</div>
                                                            <div class="text-gray-900 text-xs" x-text="blockedInfo.reason"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Menu Status -->
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-utensils text-gray-400 text-xs mt-1"></i>
                                                        <div>
                                                            <div class="text-gray-600 text-xs mb-1">Affected Menu:</div>
                                                            <div class="text-gray-900 text-xs">
                                                                @if($blockedPeriod->all_menus)
                                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                                                        All Menus Blocked
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                                                        {{ $blockedPeriod->menu ? $blockedPeriod->menu->name : 'Specific Menu' }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if(count($day['reservations']) > 0)
                                <div class="space-y-2">
                                    @foreach($day['reservations'] as $reservation)
                                        @php
                                            $menu = $reservation->menu;
                                            $backgroundColor = $menu->getColorWithOpacity(10); 
                                            $borderColor = $menu->color;
                                            $textColor = $menu->getTextColor();
                                        @endphp
                                        
                                        <div 
                                            x-data="{ 
                                                showTooltip: false,
                                                reservation: @js($reservation->toArray()),
                                                menu: @js($menu->toArray()),
                                                tooltipPosition: 'bottom',
                                                tooltipHorizontal: 'left',
                                                checkTooltipPosition(event) {
                                                    const rect = event.target.getBoundingClientRect();
                                                    const windowHeight = window.innerHeight;
                                                    const windowWidth = window.innerWidth;
                                                    const tooltipHeight = 300;
                                                    const tooltipWidth = 320; // w-80 = 20rem = 320px
                                                    
                                                    // Check vertical position (top/bottom)
                                                    if (rect.bottom + tooltipHeight > windowHeight) {
                                                        this.tooltipPosition = 'top';
                                                    } else {
                                                        this.tooltipPosition = 'bottom';
                                                    }
                                                    
                                                    // Check horizontal position (left/right)
                                                    if (rect.left + tooltipWidth > windowWidth) {
                                                        this.tooltipHorizontal = 'right';
                                                    } else {
                                                        this.tooltipHorizontal = 'left';
                                                    }
                                                }
                                            }"
                                            @mouseenter="showTooltip = true; checkTooltipPosition($event)"
                                            @mouseleave="showTooltip = false"
                                            @click="$dispatch('open-modal', { reservation: reservation })"
                                            class="relative text-xs p-2 rounded-lg cursor-pointer transition-all duration-200 border-l-4"
                                            style="background-color: {{ $backgroundColor }}; border-left-color: {{ $borderColor }}; color: {{ $textColor }};"
                                        >
                                            <div class="font-medium">{{ $reservation->reservation_datetime->format('H:i') }}
                                                - {{ $reservation->reservation_datetime->addMinutes($menu->required_time)->format('H:i') }}
                                            </div>
                                            <div class="truncate">{{ $reservation->user->full_name }}</div>
                                            
                                            <!-- Enhanced Tooltip dengan posisi kiri/kanan -->
                                            <div 
                                                x-show="showTooltip"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                :class="{
                                                    'bottom-full mb-2': tooltipPosition === 'top',
                                                    'top-full mt-2': tooltipPosition === 'bottom',
                                                    'left-0': tooltipHorizontal === 'left',
                                                    'right-0': tooltipHorizontal === 'right'
                                                }"
                                                class="absolute z-50 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                                                style="display: none;"
                                            >
                                                <!-- Arrow untuk tooltip -->
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'left'"
                                                    class="absolute -top-2 left-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'right'"
                                                    class="absolute -top-2 right-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'left'"
                                                    class="absolute -bottom-2 left-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'right'"
                                                    class="absolute -bottom-2 right-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>

                                                <!-- Tooltip Header dengan Menu Color -->
                                                <div class="px-4 py-3 border-b border-gray-200"
                                                    style="background-color: {{ $backgroundColor }}; border-top: 3px solid {{ $borderColor }};">
                                                    <div class="flex justify-between items-center gap-2">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $borderColor }};"></div>
                                                            <span class="font-semibold text-gray-900 text-sm">{{ $reservation->reservation_number }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                                                {{ $reservation->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                {{ $reservation->status->value === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                                {{ $reservation->status->value === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                {{ $reservation->status->value === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                            ">
                                                                {{ $reservation->status->label() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Tooltip Content -->
                                                <div class="px-4 py-3 space-y-3 text-sm">
                                                    <!-- Customer Info -->
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                            style="background-color: {{ $backgroundColor }};">
                                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reservation->user->full_name }}</div>
                                                            <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email not available' }}</div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Menu Info dengan Color -->
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                            style="background-color: {{ $borderColor }};">
                                                            <i class="fas fa-cog text-white text-xs"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900">{{ $menu->name }}</div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Reservation Details -->
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-hourglass-half text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $menu->required_time }} menit</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-users text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->number_of_people }} people</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-money-bill-wave text-gray-400 text-xs"></i>
                                                            <span class="font-semibold text-gray-900">Rp {{ number_format($reservation->amount, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Menu Price Info -->
                                                    @if($menu->price)
                                                        <div class="bg-gray-50 rounded-lg p-3">
                                                            <div class="flex items-center justify-between">
                                                                <div class="text-gray-600 text-xs">Menu Price:</div>
                                                                <div class="font-medium text-gray-900">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Notes -->
                                                    @if($reservation->notes)
                                                        <div class="bg-gray-50 rounded-lg p-3">
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
                                                    <div class="flex items-center justify-between">
                                                        <span>Click to view full details</span>
                                                        <div class="flex items-center space-x-1">
                                                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $borderColor }};"></div>
                                                        </div>
                                                    </div>
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
                    @php
                        $isHourBlocked = in_array($slot['hour'], $blockedHours ?? []);
                        $hourBlockedPeriods = collect($blockedPeriods ?? [])->filter(function($period) use ($slot, $currentDate) {
                            $startHour = $period->start_datetime->format('H:i');
                            $endHour = $period->end_datetime->format('H:i');
                            $periodDate = $period->start_datetime->format('Y-m-d');
                            return $periodDate === $currentDate->format('Y-m-d') && $startHour <= $slot['hour'] && $endHour > $slot['hour'];
                        });
                    @endphp
                    
                    <div class="flex border-b border-gray-200 pb-2">
                        <div class="w-16 text-right pr-4 pt-2">
                            <span class="text-sm font-medium text-gray-600">{{ $slot['hour'] }}</span>
                        </div>
                        <div class="flex-1 min-h-[60px] ">
                            
                            {{-- Tampilkan Blocked Periods untuk jam ini --}}
                            @if($hourBlockedPeriods->count() > 0)
                                <div class="mb-2">
                                    @foreach($hourBlockedPeriods as $blockedPeriod)
                                        <div 
                                            x-data="{ 
                                                showTooltip: false,
                                                blockedInfo: @js($blockedPeriod->getTooltipInfo()),
                                                tooltipPosition: 'bottom',
                                                tooltipHorizontal: 'left',
                                                checkTooltipPosition(event) {
                                                    const rect = event.target.getBoundingClientRect();
                                                    const windowHeight = window.innerHeight;
                                                    const windowWidth = window.innerWidth;
                                                    const tooltipHeight = 200;
                                                    const tooltipWidth = 300;
                                                    
                                                    if (rect.bottom + tooltipHeight > windowHeight) {
                                                        this.tooltipPosition = 'top';
                                                    } else {
                                                        this.tooltipPosition = 'bottom';
                                                    }
                                                    
                                                    if (rect.left + tooltipWidth > windowWidth) {
                                                        this.tooltipHorizontal = 'right';
                                                    } else {
                                                        this.tooltipHorizontal = 'left';
                                                    }
                                                }
                                            }"
                                            @mouseenter="showTooltip = true; checkTooltipPosition($event)"
                                            @mouseleave="showTooltip = false"
                                            class="relative mb-1 p-2 bg-red-100 border border-red-300 rounded-lg cursor-pointer transition-all duration-200"
                                        >
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-medium text-red-800">Blocked Period</span>
                                                <span class="text-xs text-red-600">
                                                    {{ $blockedPeriod->start_datetime->format('H:i') }} - 
                                                    {{ $blockedPeriod->end_datetime->format('H:i') }}
                                                </span>
                                            </div>
                                            
                                            {{-- Blocked Period Tooltip --}}
                                            <div 
                                                x-show="showTooltip"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                :class="{
                                                    'bottom-full mb-2': tooltipPosition === 'top',
                                                    'top-full mt-2': tooltipPosition === 'bottom',
                                                    'left-0': tooltipHorizontal === 'left',
                                                    'right-0': tooltipHorizontal === 'right'
                                                }"
                                                class="absolute z-50 w-72 bg-white rounded-lg shadow-xl border border-red-200 overflow-hidden"
                                                style="display: none;"
                                            >
                                                {{-- Tooltip Arrow --}}
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'left'"
                                                    class="absolute -top-2 left-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'right'"
                                                    class="absolute -top-2 right-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'left'"
                                                    class="absolute -bottom-2 left-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'right'"
                                                    class="absolute -bottom-2 right-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>

                                                {{-- Tooltip Header --}}
                                                <div class="px-4 py-3 bg-red-50 border-b border-red-200">
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-ban text-red-600"></i>
                                                        <span class="font-semibold text-red-800 text-sm">Blocked Period</span>
                                                    </div>
                                                </div>
                                                
                                                {{-- Tooltip Content --}}
                                                <div class="px-4 py-3 space-y-3 text-sm">
                                                    {{-- Time Range --}}
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                        <span class="text-gray-700">
                                                            <span x-text="blockedInfo.start_time"></span> - 
                                                            <span x-text="blockedInfo.end_time"></span>
                                                        </span>
                                                    </div>
                                                    
                                                    {{-- Reason --}}
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-info-circle text-gray-400 text-xs mt-1"></i>
                                                        <div>
                                                            <div class="text-gray-600 text-xs mb-1">Reason:</div>
                                                            <div class="text-gray-900 text-xs" x-text="blockedInfo.reason"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Menu Status --}}
                                                    <div class="flex items-start space-x-2">
                                                        <i class="fas fa-utensils text-gray-400 text-xs mt-1"></i>
                                                        <div>
                                                            <div class="text-gray-600 text-xs mb-1">Affected Menu:</div>
                                                            <div class="text-gray-900 text-xs">
                                                                @if($blockedPeriod->all_menus)
                                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                                                        All Menus Blocked
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                                                        {{ $blockedPeriod->menu ? $blockedPeriod->menu->name : 'Specific Menu' }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            {{-- Reservations untuk jam ini --}}
                            @if(count($slot['reservations']) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                    @foreach($slot['reservations'] as $reservation)
                                        @php
                                            $menu = $reservation->menu;
                                            $backgroundColor = $menu->getColorWithOpacity(10); 
                                            $borderColor = $menu->color;
                                            $textColor = $menu->getTextColor();
                                        @endphp
                                        
                                        <div 
                                            x-data="{ 
                                                showTooltip: false,
                                                reservation: @js($reservation->toArray()),
                                                menu: @js($menu->toArray()),
                                                tooltipPosition: 'bottom',
                                                tooltipHorizontal: 'left',
                                                checkTooltipPosition(event) {
                                                    const rect = event.target.getBoundingClientRect();
                                                    const windowHeight = window.innerHeight;
                                                    const windowWidth = window.innerWidth;
                                                    const tooltipHeight = 300;
                                                    const tooltipWidth = 320;
                                                    
                                                    if (rect.bottom + tooltipHeight > windowHeight) {
                                                        this.tooltipPosition = 'top';
                                                    } else {
                                                        this.tooltipPosition = 'bottom';
                                                    }
                                                    
                                                    if (rect.left + tooltipWidth > windowWidth) {
                                                        this.tooltipHorizontal = 'right';
                                                    } else {
                                                        this.tooltipHorizontal = 'left';
                                                    }
                                                }
                                            }"
                                            @mouseenter="showTooltip = true; checkTooltipPosition($event)"
                                            @mouseleave="showTooltip = false"
                                            @click="$dispatch('open-modal', { reservation: reservation })"
                                            class="relative text-xs p-2 rounded-lg cursor-pointer transition-all duration-200 border-l-4"
                                            style="background-color: {{ $backgroundColor }}; border-left-color: {{ $borderColor }}; color: {{ $textColor }};"
                                        >
                                            <div class="font-medium">{{ $reservation->reservation_datetime->format('H:i') }}
                                                - {{ $reservation->reservation_datetime->addMinutes($menu->required_time)->format('H:i') }}
                                            </div>
                                            <div class="truncate">{{ $reservation->user->full_name }}</div>
                                            <div class="text-xs opacity-75">{{ $reservation->number_of_people }} people</div>
                                            
                                            {{-- Enhanced Tooltip --}}
                                            <div 
                                                x-show="showTooltip"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 transform scale-95"
                                                x-transition:enter-end="opacity-100 transform scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 transform scale-100"
                                                x-transition:leave-end="opacity-0 transform scale-95"
                                                :class="{
                                                    'bottom-full mb-2': tooltipPosition === 'top',
                                                    'top-full mt-2': tooltipPosition === 'bottom',
                                                    'left-0': tooltipHorizontal === 'left',
                                                    'right-0': tooltipHorizontal === 'right'
                                                }"
                                                class="absolute z-50 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden"
                                                style="display: none;"
                                            >
                                                {{-- Tooltip Arrow --}}
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'left'"
                                                    class="absolute -top-2 left-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'right'"
                                                    class="absolute -top-2 right-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'left'"
                                                    class="absolute -bottom-2 left-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>
                                                <div 
                                                    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'right'"
                                                    class="absolute -bottom-2 right-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
                                                ></div>

                                                {{-- Tooltip Header --}}
                                                <div class="px-4 py-3 border-b border-gray-200"
                                                    style="background-color: {{ $backgroundColor }}; border-top: 3px solid {{ $borderColor }};">
                                                    <div class="flex justify-between items-center gap-2">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-3 h-3 rounded-full" style="background-color: {{ $borderColor }};"></div>
                                                            <span class="font-semibold text-gray-900 text-sm">{{ $reservation->reservation_number }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="px-2 py-1 text-xs rounded-full font-medium
                                                                {{ $reservation->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                                {{ $reservation->status->value === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                                                {{ $reservation->status->value === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                                {{ $reservation->status->value === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                                            ">
                                                                {{ $reservation->status->label() }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Tooltip Content --}}
                                                <div class="px-4 py-3 space-y-3 text-sm">
                                                    {{-- Customer Info --}}
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                            style="background-color: {{ $backgroundColor }};">
                                                            <i class="fas fa-user text-gray-600 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reservation->user->full_name }}</div>
                                                            <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email not available' }}</div>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Menu Info --}}
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                            style="background-color: {{ $borderColor }};">
                                                            <i class="fas fa-cog text-white text-xs"></i>
                                                        </div>
                                                        <div class="flex-1">
                                                            <div class="font-medium text-gray-900">{{ $menu->name }}</div>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Reservation Details --}}
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-hourglass-half text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $menu->required_time }} menit</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-users text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->number_of_people }} people</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-money-bill-wave text-gray-400 text-xs"></i>
                                                            <span class="font-semibold text-gray-900">Rp {{ number_format($reservation->amount, 0, ',', '.') }}</span>
                                                        </div>
                                                    </div>
                                                    
                                                    {{-- Menu Price Info --}}
                                                    @if($menu->price)
                                                        <div class="bg-gray-50 rounded-lg p-3">
                                                            <div class="flex items-center justify-between">
                                                                <div class="text-gray-600 text-xs">Menu Price:</div>
                                                                <div class="font-medium text-gray-900">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    {{-- Notes --}}
                                                    @if($reservation->notes)
                                                        <div class="bg-gray-50 rounded-lg p-3">
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
                                                
                                                {{-- Tooltip Footer --}}
                                                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
                                                    <div class="flex items-center justify-between">
                                                        <span>Click to view full details</span>
                                                        <div class="flex items-center space-x-1">
                                                            <div class="w-2 h-2 rounded-full" style="background-color: {{ $borderColor }};"></div>
                                                        </div>
                                                    </div>
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