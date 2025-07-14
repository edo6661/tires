{{-- resources/views/components/shared/features/reservation/calendar/week-view.blade.php --}}
@props(['weekDays'])

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
                        <x-shared.features.reservation.calendar.reservation-card 
                            :reservation="$reservation" 
                            :show-time="true"
                            :show-name="true"
                            size="sm"
                        />
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>