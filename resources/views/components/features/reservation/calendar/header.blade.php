@props(['view' => 'month'])

<div class="bg-white rounded-lg shadow-sm p-6 space-y-8">
    <div class="flex items-center justify-between flex-wrap gap-2">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('admin/reservation/calendar.calendar.title') }}</h1>
        <div class="flex items-center space-x-4 flex-wrap gap-2">
            <div class="flex bg-gray-100 rounded-lg p-1">
                <a href="{{ route('admin.reservation.calendar', ['view' => 'month', 'date' => request('date', now()->format('Y-m-d'))]) }}" 
                class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $view === 'month' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    {{ __('admin/reservation/calendar.views.month') }}
                </a>
                <a href="{{ route('admin.reservation.calendar', ['view' => 'week', 'date' => request('date', now()->format('Y-m-d'))]) }}" 
                class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $view === 'week' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    {{ __('admin/reservation/calendar.views.week') }}
                </a>
                <a href="{{ route('admin.reservation.calendar', ['view' => 'day', 'date' => request('date', now()->format('Y-m-d'))]) }}" 
                class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $view === 'day' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                    {{ __('admin/reservation/calendar.views.day') }}
                </a>
            </div>
           <div x-data>
                <button 
                    @click="$refs.dateSelect.showPicker()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                >
                    <i class="fas fa-calendar-alt mr-2"></i>
                    {{ __('admin/reservation/calendar.views.select_date') }}
                </button>
                <input 
                    type="date" 
                    x-ref="dateSelect"
                    @change="window.location.href = '{{ route('admin.reservation.calendar') }}?view={{ $view }}&date=' + $event.target.value"
                    value="{{ request('date', now()->format('Y-m-d')) }}"
                    class="sr-only"
                />
            </div>
        </div>
    </div>
</div>