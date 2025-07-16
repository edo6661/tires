@if(($view ?? 'month') === 'month')
    <x-features.reservation.calendar.month-view :calendarDays="$calendarDays" :menus="$menus ?? []" />
@elseif(($view ?? 'month') === 'week')
    <x-features.reservation.calendar.week-view :weekDays="$weekDays" />
@else
    <x-features.reservation.calendar.day-view 
        :hourlySlots="$hourlySlots" 
        :blockedHours="$blockedHours ?? []" 
        :blockedPeriods="$blockedPeriods ?? []" 
        :currentDate="$currentDate" 
    />
@endif