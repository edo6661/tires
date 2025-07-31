<div class="grid grid-cols-7 sm:gap-1 gap-0.5 bg-gray-200 rounded-lg">
    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
        <div class="bg-gray-50 p-3 text-center text-sm font-medium text-gray-600 rounded">
            {{ __('admin/reservation/calendar.calendar.days.' . $day) }}
        </div>
    @endforeach

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
                        <x-features.reservation.calendar.blocked-period-item :blockedPeriod="$blockedPeriod" />
                    @endforeach
                </div>
            @endif
            
            @if(isset($day['reservations']) && count($day['reservations']) > 0)
                <div class="space-y-4">
                    @foreach($day['reservations']->take(4) as $reservation)
                        <x-features.reservation.calendar.reservation-item 
                            :reservation="$reservation" 
                            :menu="$reservation->menu" 
                        />
                    @endforeach
                    
                    @if(count($day['reservations']) > 4)
                        <div class="text-xs text-gray-500 text-center py-1 bg-gray-50 rounded">
                            +{{ count($day['reservations']) - 4 }} {{ __('admin/reservation/calendar.calendar.more_reservations') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endforeach
</div>

@if(isset($menus) && count($menus) > 0)
    <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
        <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('admin/reservation/calendar.calendar.menu_legend') }}</h4>
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