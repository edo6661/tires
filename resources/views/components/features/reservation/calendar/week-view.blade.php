<div class="flex flex-wrap gap-4 justify-center">
    @foreach($weekDays as $day)
        <div class="bg-gray-50 rounded-lg p-4 min-h-[300px] min-w-[174px]">
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
                        <x-features.reservation.calendar.blocked-period-item :blockedPeriod="$blockedPeriod" />
                    @endforeach
                </div>
            @endif
            
            @if(count($day['reservations']) > 0)
                <div class="space-y-2">
                    @foreach($day['reservations'] as $reservation)
                        <x-features.reservation.calendar.reservation-item 
                            :reservation="$reservation" 
                            :menu="$reservation->menu" 
                        />
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>