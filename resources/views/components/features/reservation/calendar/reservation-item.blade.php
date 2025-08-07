@props(['reservation', 'menu'])
@php
    $backgroundColor = $menu->getColorWithOpacity(10); 
    $borderColor = $menu->color;
    $textColor = $menu->getDarkenedTextColor();
@endphp
<div 
    x-data="reservationTooltip()"
    @mouseenter="showTooltip = true; checkTooltipPosition($event)"
    @mouseleave="showTooltip = false"
    @click="$dispatch('open-modal', { reservation: reservation })"
    class="relative text-xs sm:py-2 sm:px-2 py-2 px-0 rounded-lg cursor-pointer transition-all duration-200 sm:border-l-4 border-l-0"
    style="background-color: {{ $backgroundColor }}; border-left-color: {{ $borderColor }}; color: {{ $textColor }};"
>
    <div class="flex items-start flex-col gap-2">
        <div class="flex-1">
            <div class="font-medium mx-2">
                {{ $reservation->getFullName() }}
            </div>
        </div>
        <div class="flex items-start flex-col">
            <div class="text-gray-900 mx-2">{{ $reservation->reservation_datetime->format('H:i') }}
                - {{ $reservation->reservation_datetime->addMinutes($menu->required_time)->format('H:i') }}
            </div>
        </div>
    </div>
    <a 
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
        class="absolute z-50 w-80 bg-white rounded-lg shadow-xl border border-gray-200 overflow-hidden md:max-w-full max-w-60"
        style="display: none;"
        href="{{ route('admin.reservation.show', $reservation->id) }}"
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
        <div class="px-4 py-3 border-b border-gray-200"
            style="background-color: {{ $backgroundColor }}; border-top: 3px solid {{ $borderColor }};">
            <div class="flex justify-between items-center gap-2">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $borderColor }};"></div>
                    <span class="font-semibold text-gray-900 text-sm">{{ $reservation->reservation_number }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs rounded-full font-medium
                        {{ $reservation->status->value === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $reservation->status->value === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $reservation->status->value === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $reservation->status->value === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                    ">
                        {{ __('admin/reservation/calendar.status_labels.' . $reservation->status->value) }}
                    </span>
                </div>
            </div>
        </div>
        <div class="px-4 py-3 space-y-3 text-sm">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                    style="background-color: {{ $backgroundColor }};">
                    <i class="fas fa-user text-gray-600 text-xs"></i>
                </div>
                <div>
                    <div class="font-medium text-gray-900">{{ $reservation->getFullName() }}</div>
                    <div class="text-gray-600 text-xs">{{ $reservation->getEmail() }}</div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center"
                    style="background-color: {{ $borderColor }};">
                    <i class="fas fa-cog text-white text-xs"></i>
                </div>
                <div class="flex-1">
                    <div class="font-medium text-gray-900">{{ $menu->name }}</div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-gray-400 text-xs"></i>
                    <span class="text-gray-600">{{ $reservation->reservation_datetime->format('H:i') }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-hourglass-half text-gray-400 text-xs"></i>
                    <span class="text-gray-600">{{ $menu->required_time }} {{ __('admin/reservation/calendar.reservation_tooltip.minute') }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-users text-gray-400 text-xs"></i>
                    <span class="text-gray-600">{{ $reservation->number_of_people }} {{ __('admin/reservation/calendar.reservation_tooltip.people') }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-money-bill-wave text-gray-400 text-xs"></i>
                    <span class="font-semibold text-gray-900">$ {{ number_format($reservation->amount, 0, ',', '.') }}</span>
                </div>
            </div>
            {{-- @if($menu->price)
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div class="text-gray-600 text-xs">{{ __('admin/reservation/calendar.reservation_tooltip.menu_price') }}</div>
                        <div class="font-medium text-gray-900">$ {{ number_format($menu->price, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endif --}}
            @if($reservation->notes)
                <div class="bg-gray-50 rounded-lg p-3">
                    <div class="flex items-start space-x-2">
                        <i class="fas fa-sticky-note text-gray-400 text-xs mt-1"></i>
                        <div>
                            <div class="text-gray-600 text-xs mb-1">{{ __('admin/reservation/calendar.reservation_tooltip.notes') }}</div>
                            <div class="text-gray-900 text-xs">{{ $reservation->notes }}</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 text-xs text-gray-500">
            <div class="flex items-center justify-between">
                <span>{{ __('admin/reservation/calendar.reservation_tooltip.click_to_view') }}</span>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 rounded-full" style="background-color: {{ $borderColor }};"></div>
                </div>
            </div>
        </div>
    </a>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('reservationTooltip', () => ({
            showTooltip: false,
            reservation: @json($reservation->toArray()),
            menu: @json($menu->toArray()),
            tooltipPosition: 'bottom',
            tooltipHorizontal: 'left',
            checkTooltipPosition(event) {
                const rect = event.target.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const windowWidth = window.innerWidth;
                const tooltipHeight = 300;
                const tooltipWidth = 320;
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