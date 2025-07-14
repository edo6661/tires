{{-- resources/views/components/shared/features/reservation/calendar/calendar-views.blade.php --}}
@props([
    'view' => 'month',
    'calendarDays' => [],
    'weekDays' => [],
    'hourlySlots' => []
])
@dd("Rendering calendar view: $view with " . count($calendarDays) . " days, " . count($weekDays) . " week days, and " . count($hourlySlots) . " hourly slots.")

<!-- Calendar Views -->
@if($view === 'month')
    <x-shared.features.reservation.calendar.month-view :calendarDays="$calendarDays" />
@elseif($view === 'week')
    <x-shared.features.reservation.calendar.week-view :weekDays="$weekDays" />
@else
    <x-shared.features.reservation.calendar.day-view :hourlySlots="$hourlySlots" />
@endif