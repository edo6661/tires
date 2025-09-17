{{-- resources/views/components/features/reservation/calendar/calendar-day.blade.php --}}
@props(['day', 'isMonthView' => true])

<div class="{{ $isMonthView ? 'bg-white rounded min-h-[120px] sm:p-2' : 'min-w-[174px] bg-gray-50 rounded-lg p-4 min-h-[300px]' }} {{ $day['isCurrentMonth'] ? '' : 'opacity-50' }}">
    <div class="flex items-center justify-between mb-2">
        @if($isMonthView)
            <span class="text-sm font-medium {{ $day['isBlocked'] ? 'text-gray-500' : 'text-gray-900' }}">
                {{ $day['date']->format('j') }}
            </span>
        @else
            <div class="text-center">
                <div class="text-xs text-gray-600 uppercase">{{ $day['date']->format('D') }}</div>
                <div class="text-lg font-semibold text-gray-900 {{ $day['isToday'] ? 'text-blue-600' : '' }}">
                    {{ $day['date']->format('j') }}
                </div>
            </div>
        @endif

        <div class="flex items-center space-x-1">
            @if($day['isToday'])
                <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
            @endif
        </div>
    </div>

    {{-- Blocked Periods --}}
    @if($day['isBlocked'])
        <div class="mb-3">
            @foreach($day['blockedPeriods'] as $blockedPeriod)
                <x-features.reservation.calendar.blocked-period-item :blockedPeriod="$blockedPeriod" />
            @endforeach
        </div>
    @endif

    {{-- Reservations --}}
    @if(isset($day['reservations']) && count($day['reservations']) > 0)
        <div class="space-y-{{ $isMonthView ? '4' : '2' }}">
            @php
                $displayLimit = $isMonthView ? 4 : count($day['reservations']);
            @endphp

            @foreach($day['reservations']->take($displayLimit) as $reservation)
                <x-features.reservation.calendar.reservation-item
                    :reservation="$reservation"
                    :isMonthView="$isMonthView"
                />
            @endforeach

            @if($isMonthView && count($day['reservations']) > 4)
                <div class="text-xs text-gray-500 text-center py-1 bg-gray-50 rounded">
                    +{{ count($day['reservations']) - 4 }} more reservations
                </div>
            @endif
        </div>
    @endif
</div>
