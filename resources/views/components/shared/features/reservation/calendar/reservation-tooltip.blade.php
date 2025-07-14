{{-- resources/views/components/shared/features/reservation/calendar/reservation-tooltip.blade.php --}}
@props(['reservation'])

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
            <x-shared.features.reservation.calendar.status-badge :status="$reservation->status" />
        </div>
    </div>
    
    <!-- Tooltip Content -->
    <div class="px-4 py-3 space-y-3 text-sm">
        <!-- User Info -->
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-user text-gray-600 text-xs"></i>
            </div>
            <div>
                <div class="font-medium text-gray-900">{{ $reservation->user->full_name }}</div>
                <div class="text-gray-600 text-xs">{{ $reservation->user->email ?? 'Email not available' }}</div>
            </div>
        </div>
        
        <!-- Menu Info -->
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                <i class="fas fa-utensils text-gray-600 text-xs"></i>
            </div>
            <div>
                <div class="font-medium text-gray-900">{{ $reservation->menu->name }}</div>
                <div class="text-gray-600 text-xs">{{ Str::limit($reservation->menu->description ?? '', 50) }}</div>
            </div>
        </div>
        
        <!-- Time & People Info -->
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
        
        <!-- Amount -->
        <div class="flex items-center space-x-2">
            <i class="fas fa-money-bill-wave text-gray-400 text-xs"></i>
            <span class="font-semibold text-gray-900">Rp {{ number_format($reservation->amount, 0, ',', '.') }}</span>
        </div>
        
        <!-- Notes -->
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