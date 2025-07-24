<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $menu->name }}</h1>
            <p class="text-sm text-gray-600 mb-4">{{ $menu->required_time }} minutes</p>
            @if($menu->description)
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <p class="text-sm text-gray-700">{{ $menu->description }}</p>
                </div>
            @endif
            <div class="space-y-3 text-sm text-gray-600">
                <div>
                    <p class="font-medium text-gray-800">Reservation Notes</p>
                    <ul class="mt-2 space-y-1 list-disc list-inside text-xs">
                        <li>The work time is an approximate guide</li>
                        <li>Please note that it may take some time depending on the work content</li>
                        <li>Reservation deadline: Until 23:59 one day before</li>
                        <li>Cancellation not allowed after confirmation</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="bookingCalendar()">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Select Date</h2>
            <div class="flex items-center justify-between mb-6">
                <button @click="previousMonth()" 
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-chevron-left mr-2"></i>
                    Previous Month
                </button>
                <h3 class="text-lg font-semibold text-gray-900" x-text="currentMonthDisplay"></h3>
                <button @click="nextMonth()" 
                        class="flex items-center px-3 py-2 text-gray-600 hover:text-gray-900 transition-colors">
                    Next Month
                    <i class="fas fa-chevron-right ml-2"></i>
                </button>
            </div>
            <div class="grid grid-cols-7 gap-1 bg-gray-100 rounded-lg p-2">
                <template x-for="day in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']" :key="day">
                    <div class="bg-gray-50 p-2 text-center text-sm font-medium text-gray-600 rounded">
                        <span x-text="day"></span>
                    </div>
                </template>
                <template x-for="day in calendarDays" :key="day.dateString">
                    <div class="relative">
                        <button @click="selectDate(day)" 
                                :disabled="day.bookingStatus === 'past' || day.bookingStatus === 'full'"
                                :class="{
                                    'bg-white hover:bg-blue-50 text-gray-900 cursor-pointer': day.bookingStatus === 'available' && day.isCurrentMonth && selectedDate !== day.dateString,
                                    'bg-gray-100 text-gray-400 cursor-not-allowed': day.bookingStatus === 'past' || !day.isCurrentMonth,
                                    'bg-yellow-100 text-yellow-700 cursor-not-allowed': day.bookingStatus === 'full',
                                    'bg-blue-500 text-white !important': selectedDate === day.dateString,
                                    'ring-2 ring-blue-300': day.isToday
                                }"
                                class="w-full h-12 p-2 text-sm rounded transition-colors relative group">
                            <span x-text="day.day"></span>
                            <template x-if="day.bookingStatus === 'full'">
                                <div class="absolute bottom-0 left-0 right-0 h-1 bg-yellow-400 rounded-b"></div>
                            </template>
                            <template x-if="day.reservationCount > 0 && day.bookingStatus === 'available'">
                                <div class="absolute top-1 right-1 w-2 h-2 bg-orange-400 rounded-full"></div>
                            </template>
                            <template x-if="day.bookingStatus === 'full' || day.bookingStatus === 'past'">
                                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                                    <span x-text="getTooltipText(day)"></span>
                                </div>
                            </template>
                        </button>
                    </div>
                </template>
            </div>
            <div class="flex flex-wrap gap-4 mt-4 text-xs">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-white border border-gray-300 rounded mr-2"></div>
                    <span class="text-gray-600">Available</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-100 border border-yellow-200 rounded mr-2"></div>
                    <span class="text-gray-600">Fully Booked</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-gray-100 border border-gray-200 rounded mr-2"></div>
                    <span class="text-gray-600">Past Date</span>
                </div>
            </div>
            <div x-show="selectedDate && availableHours.length > 0" class="mt-8 border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Select Time</h3>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                    <template x-for="hour in availableHours" :key="hour.time">
                        <button @click="selectTime(hour)"
                                :disabled="!hour.available"
                                :class="{
                                    'bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100': hour.available && selectedTime !== hour.time,
                                    'bg-blue-500 border-blue-500 text-white': selectedTime === hour.time,
                                    'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed': !hour.available
                                }"
                                class="relative px-3 py-2 text-sm font-medium border rounded-lg transition-colors">
                            <span x-text="hour.time"></span>
                            <template x-if="hour.indicator">
                                <div class="absolute -top-1 -right-1 px-1 text-xs bg-red-500 text-white rounded">
                                    <span x-text="hour.indicator"></span>
                                </div>
                            </template>
                        </button>
                    </template>
                </div>
            </div>
            <div x-show="selectedDate && availableHours.length === 0" class="mt-8 border-t pt-6">
                <div class="text-center py-8">
                    <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">No available time slots for this date</p>
                    <p class="text-sm text-gray-500 mt-2">Please select another date</p>
                </div>
            </div>
            <div x-show="selectedDate && selectedTime" class="mt-8 bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2">Booking Summary</h4>
                <div class="space-y-2 text-sm text-gray-700">
                    <p><span class="font-medium">Service:</span> {{ $menu->name }}</p>
                    <p><span class="font-medium">Duration:</span> {{ $menu->required_time }} minutes</p>
                    <p x-show="selectedDate"><span class="font-medium">Date:</span> <span x-text="formatSelectedDate()"></span></p>
                    <p x-show="selectedTime"><span class="font-medium">Time:</span> <span x-text="selectedTime"></span></p>
                </div>
            </div>
            <div class="flex justify-between mt-8">
                <a href="{{ route('home') }}" 
                   class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Back to Services
                </a>
                <button x-show="selectedDate && selectedTime" 
                        @click="proceedToNextStep()"
                        class="px-8 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                    Proceed with Booking
                </button>
            </div>
        </div>
    </div>
    <script>
        function bookingCalendar() {
            return {
                menuId: {{ $menu->id }},
                currentMonth: '{{ $currentMonth->format("Y-m") }}',
                currentMonthDisplay: '{{ $currentMonth->format("F Y") }}',
                calendarDays: @json($calendarData['days']),
                selectedDate: null,
                selectedTime: null,
                availableHours: [],
                loading: false,
                selectDate(day) {
                    // Hilangkan pengecekan 'blocked' status
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
                        const response = await fetch(`{{ route('booking.calendar-data') }}?month=${month}&menu_id=${this.menuId}`);
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
                    const current = new Date(this.currentMonth + '-01');
                    current.setMonth(current.getMonth() - 1);
                    this.currentMonth = current.getFullYear() + '-' + String(current.getMonth() + 1).padStart(2, '0');
                    await this.loadCalendarData(this.currentMonth);
                    this.selectedDate = null;
                    this.selectedTime = null;
                    this.availableHours = [];
                },
                async nextMonth() {
                    const current = new Date(this.currentMonth + '-01');
                    current.setMonth(current.getMonth() + 1);
                    this.currentMonth = current.getFullYear() + '-' + String(current.getMonth() + 1).padStart(2, '0');
                    await this.loadCalendarData(this.currentMonth);
                    this.selectedDate = null;
                    this.selectedTime = null;
                    this.availableHours = [];
                },
                formatSelectedDate() {
                    if (!this.selectedDate) return '';
                    const date = new Date(this.selectedDate);
                    return date.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                },
                getTooltipText(day) {
                    switch (day.bookingStatus) {
                        case 'full':
                            return 'Fully booked';
                        case 'past':
                            return 'Past date - unavailable';
                        default:
                            return '';
                    }
                },
                proceedToNextStep() {
                    if (!this.selectedDate || !this.selectedTime) {
                        alert('Please select both date and time');
                        return;
                    }
                    // Store booking data in sessionStorage for next step
                    const bookingData = {
                        menuId: this.menuId,
                        date: this.selectedDate,
                        time: this.selectedTime,
                        datetime: this.selectedDate + ' ' + this.selectedTime
                    };
                    sessionStorage.setItem('bookingData', JSON.stringify(bookingData));
                    // Redirect to second step
                    window.location.href = '{{ route("booking.second-step") }}';
                }
            }
        }
    </script>
    <style>
        .fade-enter-active, .fade-leave-active {
            transition: opacity 0.3s;
        }
        .fade-enter, .fade-leave-to {
            opacity: 0;
        }
    </style>
</x-layouts.app>