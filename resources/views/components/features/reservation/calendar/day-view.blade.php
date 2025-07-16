<div class="space-y-2">
    @foreach($hourlySlots as $slot)
        @php
            $isHourBlocked = in_array($slot['hour'], $blockedHours);
            $hourBlockedPeriods = collect($blockedPeriods)->filter(function($period) use ($slot, $currentDate) {
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
            <div class="flex-1 min-h-[60px]">
                @if($hourBlockedPeriods->count() > 0)
                    <div class="mb-2">
                        @foreach($hourBlockedPeriods as $blockedPeriod)
                            <x-features.reservation.calendar.blocked-period-item :blockedPeriod="$blockedPeriod" />
                        @endforeach
                    </div>
                @endif
                
                @if(count($slot['reservations']) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach($slot['reservations'] as $reservation)
                            <x-features.reservation.calendar.reservation-item 
                                :reservation="$reservation" 
                                :menu="$reservation->menu" 
                            />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>