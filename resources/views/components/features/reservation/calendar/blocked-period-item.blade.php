@props(['blockedPeriod'])

<div 
    x-data="blockedPeriodTooltip()"
    @mouseenter="showTooltip = true; checkTooltipPosition($event)"
    @mouseleave="showTooltip = false"
    class="relative mb-2 sm:p-2 bg-red-100 border border-red-300 rounded-lg transition-all duration-200"
>
    <div class="flex flex-col gap-0.5">
        <div class="flex items-center space-x-2">
            <span class="text-xs font-medium text-red-800">{{ __('admin/reservation/calendar.blocked_period.title') }}</span>
        </div>
        <span class="text-xs text-red-600">
            {{ $blockedPeriod->start_datetime->format('H:i') }} - 
            {{ $blockedPeriod->end_datetime->format('H:i') }}
        </span>
    </div>
    
    <div 
        x-show="showTooltip"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        :class="{
            'bottom-full mb-2': tooltipPosition === 'top',
            'top-full mt-2': tooltipPosition === 'bottom',
            'left-0': tooltipHorizontal === 'left',
            'right-0': tooltipHorizontal === 'right'
        }"
        class="absolute z-50 w-72 bg-white rounded-lg shadow-xl border border-red-200 overflow-hidden"
        style="display: none;"
    >
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

        <div class="px-4 py-3 bg-red-50 border-b border-red-200">
            <div class="flex items-center space-x-2">
                <i class="fas fa-ban text-red-600"></i>
                <span class="font-semibold text-red-800 text-sm">{{ __('admin/reservation/calendar.blocked_period.title') }}</span>
            </div>
        </div>
        
        <div class="px-4 py-3 space-y-3 text-sm">
            <div class="flex items-center space-x-2">
                <i class="fas fa-clock text-gray-400 text-xs"></i>
                <span class="text-gray-700">
                    <span x-text="blockedInfo.start_time"></span> - 
                    <span x-text="blockedInfo.end_time"></span>
                </span>
            </div>
            
            <div class="flex items-start space-x-2">
                <i class="fas fa-info-circle text-gray-400 text-xs mt-1"></i>
                <div>
                    <div class="text-gray-600 text-xs mb-1">{{ __('admin/reservation/calendar.blocked_period.reason') }}</div>
                    <div class="text-gray-900 text-xs" x-text="blockedInfo.reason"></div>
                </div>
            </div>
            
            <div class="flex items-start space-x-2">
                <i class="fas fa-utensils text-gray-400 text-xs mt-1"></i>
                <div>
                    <div class="text-gray-600 text-xs mb-1">{{ __('admin/reservation/calendar.blocked_period.affected_menu') }}</div>
                    <div class="text-gray-900 text-xs">
                        @if($blockedPeriod->all_menus)
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium">
                                {{ __('admin/reservation/calendar.blocked_period.all_menus_blocked') }}
                            </span>
                        @else
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium">
                                {{ $blockedPeriod->menu ? $blockedPeriod->menu->name : __('admin/reservation/calendar.blocked_period.specific_menu') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('blockedPeriodTooltip', () => ({
            showTooltip: false,
            blockedInfo: @json($blockedPeriod->getTooltipInfo()),
            tooltipPosition: 'bottom',
            tooltipHorizontal: 'left',
            checkTooltipPosition(event) {
                const rect = event.target.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const windowWidth = window.innerWidth;
                const tooltipHeight = 200;
                const tooltipWidth = 300;
                
                if (rect.bottom + tooltipHeight > windowHeight) {
                    this.tooltipPosition = 'top';
                } else {
                    this.tooltipPosition = 'bottom';
                }
                
                if (rect.left + tooltipWidth > windowWidth) {
                    this.tooltipHorizontal = 'right';
                } else {
                    this.tooltipHorizontal = 'left';
                }
            }
        }));
    });
</script>