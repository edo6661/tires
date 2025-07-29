<x-layouts.app>
    <div class="container">
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 mb-6 transform transition-all duration-300 hover:shadow-lg">
            <h1 class="text-title-lg font-bold text-main-text mb-2">{{ $menu->name }}</h1>
            <p class="text-body-md text-main-text/70 mb-4">{{ $menu->required_time }} {{ __('first-step.duration_unit') }}</p>
            @if($menu->description)
                <div class="bg-sub rounded-lg p-4 mb-4 transform transition-all duration-200 hover:bg-sub/80">
                    <p class="text-body-md text-main-text">{{ $menu->description }}</p>
                </div>
            @endif
            <div class="space-y-3 text-body-md text-main-text/80">
                <div>
                    <p class="font-medium text-main-text text-heading-md">{{ __('first-step.notes_title') }}</p>
                    <ul class="mt-2 space-y-1 list-disc list-inside text-body-md">
                        <li class="transition-all duration-200 hover:text-main-text">{{ __('first-step.notes.item1') }}</li>
                        <li class="transition-all duration-200 hover:text-main-text">{{ __('first-step.notes.item2') }}</li>
                        <li class="transition-all duration-200 hover:text-main-text">{{ __('first-step.notes.item3') }}</li>
                        <li class="transition-all duration-200 hover:text-main-text">{{ __('first-step.notes.item4') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg" x-data="bookingCalendar()">
            <h2 class="text-heading-lg font-semibold text-main-text mb-4">{{ __('first-step.select_date_title') }}</h2>
            <div class="flex items-center justify-between mb-6">
                <button @click="previousMonth()"
                        class="flex items-center px-3 py-2 text-main-text/70 hover:text-main-text hover:bg-sub rounded-md transition-all duration-200 transform ">
                    <i class="fas fa-chevron-left mr-2"></i>
                    {{ __('first-step.prev_month') }}
                </button>
                <h3 class="text-heading-lg font-semibold text-main-text transition-all duration-300" x-text="currentMonthDisplay"></h3>
                <button @click="nextMonth()"
                        class="flex items-center px-3 py-2 text-main-text/70 hover:text-main-text hover:bg-sub rounded-md transition-all duration-200 transform ">
                    {{ __('first-step.next_month') }}
                    <i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
            <div class="grid grid-cols-7 gap-1 bg-disabled/20 rounded-lg p-2">
                <template x-for="day in translations.daysOfWeek" :key="day">
                    <div class="bg-sub p-2 text-center text-body-md font-medium text-brand rounded transition-all duration-200">
                        <span x-text="day"></span>
                    </div>
                </template>
                <template x-for="day in calendarDays" :key="day.dateString">
                    <div class="relative">
                        <button @click="selectDate(day)"
                                :disabled="day.bookingStatus === 'past' || day.bookingStatus === 'full'"
                                :class="{
                                    'bg-white hover:bg-sub text-main-text cursor-pointer transform ': day.bookingStatus === 'available' && day.isCurrentMonth && selectedDate !== day.dateString,
                                    'bg-disabled text-main-text/40 cursor-not-allowed': day.bookingStatus === 'past' || !day.isCurrentMonth,
                                    'bg-main-button/20 text-main-button cursor-not-allowed': day.bookingStatus === 'full',
                                    'bg-brand text-white shadow-lg transform scale-105': selectedDate === day.dateString,
                                    'ring-2 ring-brand/30': day.isToday
                                }"
                                class="w-full h-12 p-2 text-body-md rounded transition-all duration-200 relative group">
                            <span x-text="day.day"></span>
                            <template x-if="day.bookingStatus === 'full'">
                                <div class="absolute bottom-0 left-0 right-0 h-1 bg-main-button rounded-b"></div>
                            </template>
                            <template x-if="day.reservationCount > 0 && day.bookingStatus === 'available'">
                                <div class="absolute top-1 right-1 w-2 h-2 bg-main-button rounded-full animate-pulse"></div>
                            </template>
                            <template x-if="day.bookingStatus === 'full' || day.bookingStatus === 'past'">
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-main-text text-white text-body-md rounded opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none whitespace-nowrap z-10">
                                    <span x-text="getTooltipText(day)"></span>
                                </div>
                            </template>
                        </button>
                    </div>
                </template>
            </div>
            <div class="flex flex-wrap gap-4 mt-4 text-body-md">
                <div class="flex items-center transition-all duration-200 ">
                    <div class="w-3 h-3 bg-white border border-disabled rounded mr-2"></div>
                    <span class="text-main-text/70">{{ __('first-step.legend.available') }}</span>
                </div>
                <div class="flex items-center transition-all duration-200 ">
                    <div class="w-3 h-3 bg-main-button/20 border border-main-button/30 rounded mr-2"></div>
                    <span class="text-main-text/70">{{ __('first-step.legend.fully_booked') }}</span>
                </div>
                <div class="flex items-center transition-all duration-200 ">
                    <div class="w-3 h-3 bg-disabled border border-disabled rounded mr-2"></div>
                    <span class="text-main-text/70">{{ __('first-step.legend.past_date') }}</span>
                </div>
            </div>
            <div x-show="selectedDate && availableHours.length > 0"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mt-8 border-t border-disabled/50 pt-6">
                <h3 class="text-heading-lg font-semibold text-main-text mb-4">{{ __('first-step.select_time_title') }}</h3>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                    <template x-for="hour in availableHours" :key="hour.time">
                        <button @click="selectTime(hour)"
                                :disabled="!hour.available"
                                :class="{
                                    'bg-sub border-brand text-brand hover:bg-brand/10 transform ': hour.available && selectedTime !== hour.time,
                                    'bg-brand border-brand text-white shadow-lg transform scale-105': selectedTime === hour.time,
                                    'bg-disabled border-disabled text-main-text/40 cursor-not-allowed': !hour.available
                                }"
                                class="relative px-3 py-2 text-button-md font-medium border rounded-lg transition-all duration-200">
                            <span x-text="hour.time"></span>
                            <template x-if="hour.indicator">
                                <div class="absolute -top-1 -right-1 px-1 text-body-md bg-main-button text-white rounded animate-bounce">
                                    <span x-text="hour.indicator"></span>
                                </div>
                            </template>
                        </button>
                    </template>
                </div>
            </div>
            <div x-show="selectedDate && availableHours.length === 0"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mt-8 border-t border-disabled/50 pt-6">
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-4xl text-disabled mb-4 animate-pulse"></i>
                    <p class="text-main-text/70">{{ __('first-step.no_slots_title') }}</p>
                    <p class="text-body-md text-main-text/50 mt-2">{{ __('first-step.no_slots_subtitle') }}</p>
                </div>
            </div>
            <div x-show="selectedDate && selectedTime"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-y-4"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 class="mt-8 bg-sub rounded-lg p-4 border border-brand/20">
                <h4 class="font-semibold text-main-text mb-2 text-heading-md">{{ __('first-step.summary_title') }}</h4>
                <div class="space-y-2 text-body-md text-main-text">
                    <p><span class="font-medium text-brand">{{ __('first-step.summary_service') }}</span> {{ $menu->name }}</p>
                    <p><span class="font-medium text-brand">{{ __('first-step.summary_duration') }}</span> {{ $menu->required_time }} {{ __('first-step.duration_unit') }}</p>
                    <p x-show="selectedDate"><span class="font-medium text-brand">{{ __('first-step.summary_date') }}</span> <span x-text="formatSelectedDate()"></span></p>
                    <p x-show="selectedTime"><span class="font-medium text-brand">{{ __('first-step.summary_time') }}</span> <span x-text="selectedTime"></span></p>
                </div>
            </div>
            <div class="flex justify-between mt-8">
                <a href="{{ route('home') }}"
                   class="px-6 py-2 text-main-text/70 hover:text-main-text hover:bg-sub rounded-lg transition-all duration-200 transform ">
                    {{ __('first-step.back_button') }}
                </a>
                <button x-show="selectedDate && selectedTime"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-x-4"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        @click="proceedToNextStep()"
                        class="px-8 py-2 bg-main-button hover:bg-btn-main-hover text-white rounded-lg font-medium transition-all duration-200 transform  shadow-md hover:shadow-lg text-button-lg">
                    {{ __('first-step.proceed_button') }}
                </button>
            </div>
        </div>
    </div>
    <script>
        function bookingCalendar() {
            return {
                menuId: {{ $menu->id }},
                currentMonth: '{{ $currentMonth->format("Y-m") }}',
                currentMonthDisplay: '{{ $currentMonth->translatedFormat("F Y") }}', // Menggunakan translatedFormat untuk bulan
                calendarDays: @json($calendarData['days']),
                selectedDate: null,
                selectedTime: null,
                availableHours: [],
                loading: false,
                translations: {
                    daysOfWeek: @json(__('first-step.days_of_week')),
                    tooltipFullyBooked: @json(__('first-step.js.tooltip_fully_booked')),
                    tooltipPastDate: @json(__('first-step.js.tooltip_past_date')),
                    alertSelectDateTime: @json(__('first-step.js.alert_select_date_time')),
                    dateLocale: @json(__('first-step.js.date_locale'))
                },
                selectDate(day) {
                    if (day.bookingStatus !== 'available' || !day.isCurrentMonth) {
                        return;
                    }
                    this.selectedDate = day.dateString;
                    this.selectedTime = null;
                    this.loadAvailableHours(day.dateString);
                },
                selectTime(hour) {
                    if (!hour.available) return;
                    this.selectedTime = hour.time;
                },
                async loadAvailableHours(date) {
                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('booking.available-hours') }}?date=${date}&menu_id=${this.menuId}`);
                        const data = await response.json();
                        if (data.success) {
                            this.availableHours = data.hours;
                        } else {
                            this.availableHours = [];
                            console.error('Failed to load available hours:', data.message);
                        }
                    } catch (error) {
                        console.error('Error loading available hours:', error);
                        this.availableHours = [];
                    } finally {
                        this.loading = false;
                    }
                },
                async loadCalendarData(month) {
                    this.loading = true;
                    try {
                        const response = await fetch(`{{ route('booking.calendar-data') }}?month=${month}&menu_id=${this.menuId}&locale={{ app()->getLocale() }}`);
                        const data = await response.json();
                        if (data.success) {
                            this.calendarDays = data.data.days;
                            this.currentMonthDisplay = data.currentMonth;
                        }
                    } catch (error) {
                        console.error('Error loading calendar data:', error);
                    } finally {
                        this.loading = false;
                    }
                },
                async previousMonth() {
                    const current = new Date(this.currentMonth + '-01T00:00:00');
                    current.setMonth(current.getMonth() - 1);
                    this.currentMonth = current.getFullYear() + '-' + String(current.getMonth() + 1).padStart(2, '0');
                    await this.loadCalendarData(this.currentMonth);
                    this.selectedDate = null;
                    this.selectedTime = null;
                    this.availableHours = [];
                },
                async nextMonth() {
                    const current = new Date(this.currentMonth + '-01T00:00:00');
                    current.setMonth(current.getMonth() + 1);
                    this.currentMonth = current.getFullYear() + '-' + String(current.getMonth() + 1).padStart(2, '0');
                    await this.loadCalendarData(this.currentMonth);
                    this.selectedDate = null;
                    this.selectedTime = null;
                    this.availableHours = [];
                },
                formatSelectedDate() {
                    if (!this.selectedDate) return '';
                    const date = new Date(this.selectedDate + 'T00:00:00');
                    return date.toLocaleDateString(this.translations.dateLocale, { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                },
                getTooltipText(day) {
                    switch (day.bookingStatus) {
                        case 'full':
                            return this.translations.tooltipFullyBooked;
                        case 'past':
                            return this.translations.tooltipPastDate;
                        default:
                            return '';
                    }
                },
                proceedToNextStep() {
                    if (!this.selectedDate || !this.selectedTime) {
                        alert(this.translations.alertSelectDateTime);
                        return;
                    }
                    const bookingData = {
                        menuId: this.menuId,
                        date: this.selectedDate,
                        time: this.selectedTime,
                        datetime: this.selectedDate + ' ' + this.selectedTime
                    };
                    sessionStorage.setItem('bookingData', JSON.stringify(bookingData));
                    window.location.href = '{{ route("booking.second-step") }}';
                }
            }
        }
    </script>
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.3s ease-out;
        }
    </style>
</x-layouts.app>