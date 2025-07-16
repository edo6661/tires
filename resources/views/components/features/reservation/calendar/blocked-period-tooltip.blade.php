{{-- resources/views/components/features/reservation/calendar/blocked-period-tooltip.blade.php --}}
@props(['blockedPeriod'])

<!-- Tooltip Arrow -->
<div 
    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'left'"
    class="absolute -top-2 left-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
></div>
<div 
    x-show="tooltipPosition === 'bottom' && tooltipHorizontal === 'right'"
    class="absolute -top-2 right-4 w-0 h-0 border-l-8 border-r-8 border-b-8 border-transparent border-b-white"
></div>
<div 
    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'left'"
    class="absolute -bottom-2 left-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
></div>
<div 
    x-show="tooltipPosition === 'top' && tooltipHorizontal === 'right'"
    class="absolute -bottom-2 right-4 w-0 h-0 border-l-8 border-r-8 border-t-8 border-transparent border-t-white"
></div>

<!-- Tooltip Header -->
<div class="px-4 py-3 bg-red-50 border-b border-red-200">
    <div class="flex items-center space-x-2">
        <i class="fas fa-ban text-red-600"></i>
        <span class="font-semibold text-red-800 text-sm">Blocked Period</span>
    </div>
</div>

<!-- Tooltip Content -->
<div class="px-4 py-3 space-y-3 text-sm">
    <!-- Time Range -->
    <div class="flex items-center space-x-2">
        <i class="fas fa-clock text-gray-400 text-xs"></i>
        <span class="text-gray-700">
            <span x-text="blockedInfo.start_time"></span> - 
            <span x-text="blockedInfo.end_time"></span>
        </span>
    </div>
    
    <!-- Reason -->
    <div class="flex items-start space-x-2">
        <i class="fas fa-info-circle text-gray-400 text-xs mt-1"></i>
        <div>
            <div class="text-gray-600 text-xs mb-1">Reason:</div>
            <div class="text-gray-900 text-xs" x-text="blockedInfo.reason"></div>
        </div>
    </div>
    
    <!-- Menu Status -->
    <div class="flex items-start space-x-2">
        <i class="fas fa-utensils text-gray-400 text-xs mt-1"></i>
        <div>
            <div class="text-gray-600 text-xs mb-1">Affected Menu:</div>
            <div class="text-gray-900 text-xs">
                @if($blockedPeriod->all_menus)
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                        All Menus Blocked
                    </span>
                @else
                    <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                        {{ $blockedPeriod->menu ? $blockedPeriod->menu->name : 'Specific Menu' }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>