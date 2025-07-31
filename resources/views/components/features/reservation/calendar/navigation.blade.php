@props([
    'view' => 'month',
    'currentMonth' => now(),
    'previousMonth' => now()->subMonth()->format('Y-m'),
    'nextMonth' => now()->addMonth()->format('Y-m'),
    'startOfWeek' => now()->startOfWeek(),
    'endOfWeek' => now()->endOfWeek(),
    'previousWeek' => now()->subWeek()->format('Y-m-d'),
    'nextWeek' => now()->addWeek()->format('Y-m-d'),
    'currentDate' => now(),
    'previousDay' => now()->subDay()->format('Y-m-d'),
    'nextDay' => now()->addDay()->format('Y-m-d'),
])

@php
    function getLocalizedMonth($month) {
        $monthKey = strtolower($month);
        return __('admin/reservation/calendar.months.' . $monthKey);
    }
    
    function getLocalizedMonthShort($month) {
        $monthKey = strtolower($month);
        return __('admin/reservation/calendar.months_short.' . $monthKey);
    }
@endphp

<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between w-full flex-wrap gap-4">
        @if(($view ?? 'month') === 'month')
        <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'month' => $previousMonth]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                <i class="fas fa-chevron-left mr-2"></i>
                {{ __('admin/reservation/calendar.navigation.previous_month') }}
            </a>
            <h2 class="text-xl font-semibold text-gray-900 flex-1 text-center whitespace-nowrap">
                {{ getLocalizedMonth($currentMonth->format('F')) }} {{ $currentMonth->format('Y') }}
            </h2>
            <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'month' => $nextMonth]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                {{ __('admin/reservation/calendar.navigation.next_month') }}
                <i class="fas fa-chevron-right ml-2"></i>
            </a>
        @elseif(($view ?? 'month') === 'week')
            <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => $previousWeek]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                <i class="fas fa-chevron-left mr-2"></i>
                {{ __('admin/reservation/calendar.navigation.previous_week') }}
            </a>
            <h2 class="text-xl font-semibold text-gray-900 flex-1 text-center whitespace-nowrap">
                {{ $startOfWeek->format('d') }} {{ getLocalizedMonthShort($startOfWeek->format('M')) }} {{ $startOfWeek->format('Y') }} - {{ $endOfWeek->format('d') }} {{ getLocalizedMonthShort($endOfWeek->format('M')) }} {{ $endOfWeek->format('Y') }}
            </h2>
            <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => $nextWeek]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                {{ __('admin/reservation/calendar.navigation.next_week') }}
                <i class="fas fa-chevron-right ml-2"></i>
            </a>
        @else
            <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => $previousDay]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                <i class="fas fa-chevron-left mr-2"></i>
                {{ __('admin/reservation/calendar.navigation.previous_day') }}
            </a>
            <h2 class="text-xl font-semibold text-gray-900 flex-1 text-center whitespace-nowrap">
                {{ $currentDate->format('d') }} {{ getLocalizedMonth($currentDate->format('F')) }} {{ $currentDate->format('Y') }}
            </h2>
            <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => $nextDay]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                {{ __('admin/reservation/calendar.navigation.next_day') }}
                <i class="fas fa-chevron-right ml-2"></i>
            </a>
        @endif
    </div>                
    
</div>