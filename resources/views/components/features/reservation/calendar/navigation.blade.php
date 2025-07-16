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
<div class="flex flex-col gap-4">
    <div class="flex items-center justify-between w-full flex-wrap gap-4">
        @if(($view ?? 'month') === 'month')
        <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'month' => $previousMonth]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                <i class="fas fa-chevron-left mr-2"></i>
                Previous Month
            </a>
            <h2 class="text-xl font-semibold text-gray-900 flex-1 text-center whitespace-nowrap">
                {{ $currentMonth->format('F Y') }}
            </h2>
            <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'month' => $nextMonth]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                Next Month
                <i class="fas fa-chevron-right ml-2"></i>
            </a>
        @elseif(($view ?? 'month') === 'week')
            <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => $previousWeek]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                <i class="fas fa-chevron-left mr-2"></i>
                Previous Week
            </a>
            <h2 class="text-xl font-semibold text-gray-900 flex-1 text-center whitespace-nowrap">
                {{ $startOfWeek->format('d M Y') }} - {{ $endOfWeek->format('d M Y') }}
            </h2>
            <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => $nextWeek]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                Next Week
                <i class="fas fa-chevron-right ml-2"></i>
            </a>
        @else
            <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => $previousDay]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                <i class="fas fa-chevron-left mr-2"></i>
                Previous Day
            </a>
            <h2 class="text-xl font-semibold text-gray-900 flex-1 text-center whitespace-nowrap">
                {{ $currentDate->format('d F Y') }}
            </h2>
            <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => $nextDay]) }}" 
            class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors mx-auto text-center">
                Next Day
                <i class="fas fa-chevron-right ml-2"></i>
            </a>
        @endif
    </div>                
    
</div>