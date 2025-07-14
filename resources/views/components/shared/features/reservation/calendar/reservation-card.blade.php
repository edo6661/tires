{{-- resources/views/components/shared/features/reservation/calendar/reservation-card.blade.php --}}
@props([
    'reservation',
    'showTime' => true,
    'showName' => true,
    'showPeople' => false,
    'size' => 'sm' // sm, md, lg
])

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
    class="relative cursor-pointer hover:opacity-80 transition-opacity rounded
        {{ $size === 'sm' ? 'text-xs p-1' : '' }}
        {{ $size === 'md' ? 'text-sm p-2' : '' }}
        {{ $size === 'lg' ? 'text-base p-3' : '' }}
        {{ $reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
        {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
        {{ $reservation->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
        {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
    "
>
    @if($showTime)
        <div class="font-medium">
            @if($showName)
                {{ $reservation->user->full_name }}
            @else
                {{ $reservation->reservation_datetime->format('H:i') }}
            @endif
        </div>
    @endif
    
    @if($showName && $showTime)
        <div class="{{ $size === 'sm' ? 'text-xs' : 'text-sm' }} opacity-75">
            {{ $reservation->reservation_datetime->format('H:i') }}
        </div>
    @endif
    
    @if($showName && !$showTime)
        <div class="truncate">{{ $reservation->user->full_name }}</div>
    @endif
    
    @if($showPeople)
        <div class="{{ $size === 'sm' ? 'text-xs' : 'text-sm' }} opacity-75">
            {{ $reservation->number_of_people }} people
        </div>
    @endif

    <!-- Enhanced Tooltip -->
    <x-shared.features.reservation.calendar.reservation-tooltip :reservation="$reservation" />
</div>