{{-- resources/views/components/features/reservation/calendar/reservation-tooltip.blade.php --}}
@props(['reservation', 'menu'])

@php
    $backgroundColor = $menu->getColorWithOpacity(10); 
    $borderColor = $menu->color;
@endphp

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
            <span class="text-gray-600">{{ $menu->required_time }} minute</span>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-users text-gray-400 text-xs"></i>
            <span class="text-gray-600">{{ $reservation->number_of_people }} people</span>
        </div>
        <div class="flex items-center space-x-2">
            <i class="fas fa-money-bill-wave text-gray-400 text-xs"></i>
            <span class="font-semibold text-gray-900">$ {{ number_format($reservation->amount, 0, ',', '.') }}</span>
        </div>
    </div>
    
    <!-- Menu Price Info -->
    @if($menu->price)
        <div class="bg-gray-50 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div class="text-gray-600 text-xs">Menu Price:</div>
                <div class="font-medium text-gray-900">$ {{ number_format($menu->price, 0, ',', '.') }}</div>
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