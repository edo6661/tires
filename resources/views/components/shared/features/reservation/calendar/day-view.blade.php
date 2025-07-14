{{-- resources/views/components/shared/features/reservation/calendar/day-view.blade.php --}}
@props(['hourlySlots'])

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
                            <x-shared.features.reservation.calendar.reservation-card 
                                :reservation="$reservation" 
                                :show-time="true"
                                :show-name="true"
                                :show-people="true"
                                size="sm"
                            />
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>