<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-bold text-gray-900">Kalender Reservasi</h1>
                <div class="flex items-center space-x-4">
                    <!-- View Toggle -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'date' => request('date', now()->format('Y-m-d'))]) }}" 
                           class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ ($view ?? 'month') === 'month' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            Bulan
                        </a>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => request('date', now()->format('Y-m-d'))]) }}" 
                           class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ ($view ?? 'month') === 'week' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            Minggu
                        </a>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => request('date', now()->format('Y-m-d'))]) }}" 
                           class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ ($view ?? 'month') === 'day' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                            Hari
                        </a>
                    </div>
                    <!-- Date Picker -->
                    <button 
                        x-data 
                        @click="$refs.dateSelect.showPicker()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                    >
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Pilih Tanggal
                    </button>
                    <input 
                        type="date" 
                        x-ref="dateSelect"
                        x-data
                        @change="window.location.href = '{{ route('admin.reservation.calendar') }}?view={{ $view ?? 'month' }}&date=' + $event.target.value"
                        value="{{ request('date', now()->format('Y-m-d')) }}"
                        class="sr-only"
                    >
                </div>
            </div>
        </div>
        <!-- Calendar Navigation -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    @if(($view ?? 'month') === 'month')
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'month' => $previousMonth]) }}" 
                           class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-chevron-left mr-2"></i>
                            Bulan Sebelumnya
                        </a>
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ $currentMonth->format('F Y') }}
                        </h2>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'month' => $nextMonth]) }}" 
                           class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                            Bulan Selanjutnya
                            <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    @elseif(($view ?? 'month') === 'week')
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => $previousWeek]) }}" 
                           class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-chevron-left mr-2"></i>
                            Minggu Sebelumnya
                        </a>
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ $startOfWeek->format('d M Y') }} - {{ $endOfWeek->format('d M Y') }}
                        </h2>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => $nextWeek]) }}" 
                           class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                            Minggu Selanjutnya
                            <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    @else
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => $previousDay]) }}" 
                           class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-chevron-left mr-2"></i>
                            Hari Sebelumnya
                        </a>
                        <h2 class="text-xl font-semibold text-gray-900">
                            {{ $currentDate->format('d F Y') }}
                        </h2>
                        <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => $nextDay]) }}" 
                           class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                            Hari Selanjutnya
                            <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    @endif
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Legend -->
                    <div class="flex items-center space-x-4 text-sm">
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
                </div>
            </div>
            <!-- Calendar Views -->
            @if(($view ?? 'month') === 'month')
                <!-- Month View -->
                <div class="grid grid-cols-7 gap-1 bg-gray-200 rounded-lg p-2">
                    <!-- Day Headers -->
                    @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                        <div class="bg-gray-50 p-3 text-center text-sm font-medium text-gray-600 rounded">
                            {{ $day }}
                        </div>
                    @endforeach
                    <!-- Calendar Days -->
                    @foreach($calendarDays as $day)
                        <div class="bg-white rounded min-h-[120px] p-2 {{ $day['isCurrentMonth'] ? '' : 'opacity-50' }}">
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
                                            <div class="font-medium truncate">{{ $reservation->user->name }}</div>
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
                                                            <div class="font-medium text-gray-900">{{ $reservation->user->name }}</div>
                                                            <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email tidak tersedia' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-utensils text-gray-600 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reservation->menu->name }}</div>
                                                            <div class="text-gray-600 text-xs">{{ Str::limit($reservation->menu->description ?? '', 50) }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-users text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->number_of_people }} orang</span>
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
                                                                    <div class="text-gray-600 text-xs mb-1">Catatan:</div>
                                                                    <div class="text-gray-900 text-xs">{{ $reservation->notes }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- Tooltip Footer -->
                                                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
                                                    Klik untuk melihat detail lengkap
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if(count($day['reservations']) > 3)
                                        <div class="text-xs text-gray-500 text-center py-1">
                                            +{{ count($day['reservations']) - 3 }} lainnya
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @elseif(($view ?? 'month') === 'week')
                <!-- Week View -->
                <div class="grid grid-cols-7 gap-4">
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
                                            <div class="truncate">{{ $reservation->user->name }}</div>
                                            <div class="text-xs opacity-75">{{ $reservation->menu->name }}</div>
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
                                                            <div class="font-medium text-gray-900">{{ $reservation->user->name }}</div>
                                                            <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email tidak tersedia' }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-utensils text-gray-600 text-xs"></i>
                                                        </div>
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $reservation->menu->name }}</div>
                                                            <div class="text-gray-600 text-xs">{{ Str::limit($reservation->menu->description ?? '', 50) }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                                                        </div>
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-users text-gray-400 text-xs"></i>
                                                            <span class="text-gray-600">{{ $reservation->number_of_people }} orang</span>
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
                                                                    <div class="text-gray-600 text-xs mb-1">Catatan:</div>
                                                                    <div class="text-gray-900 text-xs">{{ $reservation->notes }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- Tooltip Footer -->
                                                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
                                                    Klik untuk melihat detail lengkap
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
                                                <div class="truncate">{{ $reservation->user->name }}</div>
                                                <div class="text-xs opacity-75">{{ $reservation->menu->name }}</div>
                                                <div class="text-xs opacity-75">{{ $reservation->number_of_people }} orang</div>
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
                                                                <div class="font-medium text-gray-900">{{ $reservation->user->name }}</div>
                                                                <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email tidak tersedia' }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-3">
                                                            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-utensils text-gray-600 text-xs"></i>
                                                            </div>
                                                            <div>
                                                                <div class="font-medium text-gray-900">{{ $reservation->menu->name }}</div>
                                                                <div class="text-gray-600 text-xs">{{ Str::limit($reservation->menu->description ?? '', 50) }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-3">
                                                            <div class="flex items-center space-x-2">
                                                                <i class="fas fa-clock text-gray-400 text-xs"></i>
                                                                <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                                                            </div>
                                                            <div class="flex items-center space-x-2">
                                                                <i class="fas fa-users text-gray-400 text-xs"></i>
                                                                <span class="text-gray-600">{{ $reservation->number_of_people }} orang</span>
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
                                                                        <div class="text-gray-600 text-xs mb-1">Catatan:</div>
                                                                        <div class="text-gray-900 text-xs">{{ $reservation->notes }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <!-- Tooltip Footer -->
                                                    <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
                                                        Klik untuk melihat detail lengkap
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
        <!-- Modal untuk Detail Reservasi -->
        <div 
            x-data="{ 
                showModal: false, 
                reservation: null,
                init() {
                    this.$watch('showModal', (value) => {
                        if (value) {
                            document.body.style.overflow = 'hidden';
                        } else {
                            document.body.style.overflow = 'auto';
                        }
                    });
                    this.$root.addEventListener('open-modal', (event) => {
                        this.reservation = event.detail.reservation;
                        this.showModal = true;
                    });
                }
            }"
            x-show="showModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
            style="display: none;"
        >
            <div class="flex items-center justify-center min-h-screen p-4">
                <div 
                    x-show="showModal"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    @click.away="showModal = false"
                    class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-hidden"
                >
                    <!-- Modal Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Detail Reservasi</h3>
                            <button 
                                @click="showModal = false"
                                class="text-gray-400 hover:text-gray-600 text-2xl font-bold"
                            >
                                &times;
                            </button>
                        </div>
                    </div>
                    <!-- Modal Content -->
                    <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]" x-show="reservation">
                        <div class="space-y-6">
                            <!-- Reservation Info -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 text-lg" x-text="reservation?.reservation_number"></h4>
                                        <p class="text-sm text-gray-600" x-text="new Date(reservation?.reservation_datetime).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })"></p>
                                    </div>
                                    <span 
                                        class="px-3 py-1 text-sm rounded-full font-medium"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800': reservation?.status === 'pending',
                                            'bg-green-100 text-green-800': reservation?.status === 'confirmed',
                                            'bg-blue-100 text-blue-800': reservation?.status === 'completed',
                                            'bg-red-100 text-red-800': reservation?.status === 'cancelled'
                                        }"
                                        x-text="reservation?.status ? reservation.status.charAt(0).toUpperCase() + reservation.status.slice(1) : ''"
                                    ></span>
                                </div>
                            </div>
                            <!-- Customer Info -->
                            <div>
                                <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-user text-gray-400 mr-2"></i>
                                    Informasi Pelanggan
                                </h5>
                                <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Nama:</span>
                                        <span class="font-medium" x-text="reservation?.user?.name"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="font-medium" x-text="reservation?.user?.email || 'Tidak tersedia'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Telepon:</span>
                                        <span class="font-medium" x-text="reservation?.user?.phone || 'Tidak tersedia'"></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Menu Info -->
                            <div>
                                <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-utensils text-gray-400 mr-2"></i>
                                    Informasi Menu
                                </h5>
                                <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Menu:</span>
                                        <span class="font-medium" x-text="reservation?.menu?.name"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Deskripsi:</span>
                                        <span class="font-medium text-right max-w-xs" x-text="reservation?.menu?.description || 'Tidak ada deskripsi'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Jumlah Orang:</span>
                                        <span class="font-medium" x-text="reservation?.number_of_people + ' orang'"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Harga:</span>
                                        <span class="font-bold text-green-600" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(reservation?.amount || 0)"></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Notes -->
                            <div x-show="reservation?.notes">
                                <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-sticky-note text-gray-400 mr-2"></i>
                                    Catatan
                                </h5>
                                <div class="bg-white border border-gray-200 rounded-lg p-4">
                                    <p class="text-gray-700" x-text="reservation?.notes"></p>
                                </div>
                            </div>
                            <!-- Reservation Timeline -->
                            <div>
                                <h5 class="font-semibold text-gray-900 mb-3 flex items-center">
                                    <i class="fas fa-clock text-gray-400 mr-2"></i>
                                    Timeline
                                </h5>
                                <div class="bg-white border border-gray-200 rounded-lg p-4 space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Dibuat:</span>
                                        <span class="font-medium" x-text="new Date(reservation?.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Diupdate:</span>
                                        <span class="font-medium" x-text="new Date(reservation?.updated_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <div class="flex space-x-2">
                                <button 
                                    @click="window.location.href = '/admin/reservations/' + reservation?.id"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                >
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Detail
                                </button>
                                <button 
                                    @click="window.location.href = '/admin/reservations/' + reservation?.id + '/edit'"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                >
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit
                                </button>
                            </div>
                            <button 
                                @click="showModal = false"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                <span class="text-gray-700">Memuat data...</span>
            </div>
        </div>
    </div>
    <!-- Scripts untuk Enhanced Functionality -->
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
                        <title>Kalender Reservasi</title>
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
                        <h1>Kalender Reservasi</h1>
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