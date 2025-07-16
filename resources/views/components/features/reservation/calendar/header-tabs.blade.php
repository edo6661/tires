@props([
    'activeTab' => 'calendar',
])
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
    <div class="px-6 pt-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Reservation Management</h2>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zM4 8h12v8H4V8z" clip-rule="evenodd"/>
                    </svg>
                    Active
                </span>
            </div>
        </div>
        
        <div class="flex space-x-1" role="tablist" aria-label="Tabs">
            <a href="{{ route('admin.reservation.calendar', ['tab' => 'calendar'] + request()->query()) }}" 
            class="group relative inline-flex items-center px-6 py-3 text-sm font-medium rounded-t-lg transition-all duration-200 ease-in-out
                    {{ request('tab', 'calendar') === 'calendar' ? 'bg-white text-blue-600 shadow-sm border-t-2 border-blue-500' : 'text-gray-600 hover:text-blue-600 hover:bg-white/50' }}"
            role="tab"
            aria-selected="{{ request('tab', 'calendar') === 'calendar' ? 'true' : 'false' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Calendar View
                @if(request('tab', 'calendar') === 'calendar')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-500 rounded-t-full"></span>
                @endif
            </a>
            
            <a href="{{ route('admin.reservation.calendar', ['tab' => 'list'] + request()->query()) }}" 
            class="group relative inline-flex items-center px-6 py-3 text-sm font-medium rounded-t-lg transition-all duration-200 ease-in-out
                    {{ request('tab') === 'list' ? 'bg-white text-blue-600 shadow-sm border-t-2 border-blue-500' : 'text-gray-600 hover:text-blue-600 hover:bg-white/50' }}"
            role="tab"
            aria-selected="{{ request('tab') === 'list' ? 'true' : 'false' }}">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                List View
                @if(request('tab') === 'list')
                    <span class="absolute bottom-0 left-0 right-0 h-0.5 bg-blue-500 rounded-t-full"></span>
                @endif
            </a>
        </div>
    </div>
</div>
<div class="bg-white px-6 py-2 border-b border-gray-100">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-sm text-gray-600">
            @if(request('tab', 'calendar') === 'calendar')
                <span class="inline-flex items-center">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                    Calendar Mode - Visual overview of reservations
                </span>
            @else
                <span class="inline-flex items-center">
                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                    List Mode - Detailed reservation data
                </span>
            @endif
        </div>
        
        <div class="flex items-center space-x-2">
            <button class="text-gray-400 hover:text-gray-600 transition-colors" title="Refresh">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
            <button class="text-gray-400 hover:text-gray-600 transition-colors" title="Settings">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
        </div>
    </div>
    </div>