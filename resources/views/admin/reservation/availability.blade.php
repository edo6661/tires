<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Availability Check</h1>
                    <p class="text-gray-600 mt-1">Check time availability for reservations</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6" x-data="availabilityData()">
            <div class="space-y-6">
                <div>
                    <label for="reservation_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Reservation Date
                    </label>
                    <input 
                        type="date" 
                        id="reservation_date" 
                        x-model="selectedDate"
                        @change="loadAvailability"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>
                <div class="flex items-center justify-center space-x-4 py-4">
                    <button 
                        @click="previousDay"
                        type="button"
                        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous Day
                    </button>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900" x-text="formatDisplayDate(selectedDate)"></div>
                        <div class="text-sm text-gray-600" x-text="getDayName(selectedDate)"></div>
                    </div>
                    <button 
                        @click="nextDay"
                        type="button"
                        class="flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200"
                    >
                        Next Day
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <div class="text-lg font-semibold text-gray-900" x-text="currentDate"></div>
                    </div>
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                        <div class="text-lg font-semibold text-gray-900" x-text="currentYear"></div>
                    </div>
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                        <div class="text-lg font-semibold text-gray-900" x-text="currentMonth"></div>
                    </div>
                    <div class="text-center">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Time</label>
                        <div class="text-lg font-semibold text-gray-900" x-text="getCurrentTime()"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Menu</label>
                    <select 
                        x-model="selectedMenuId"
                        @change="loadAvailability"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">-- Select Menu --</option>
                        @foreach($menus ?? [] as $menu)
                            <option value="{{ $menu->id }}">{{ $menu->name }} ({{ $menu->required_time }} minutes)</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="loading" class="flex items-center justify-center py-8">
                    <div class="flex items-center space-x-2">
                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-gray-600">Loading availability data...</span>
                    </div>
                </div>
                <div x-show="!loading && selectedMenuId && availabilityData.length > 0" class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900">Time Availability</h3>
                    <div class="grid gap-4">
                        <template x-for="dayData in availabilityData" :key="dayData.date">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-900" x-text="formatDisplayDate(dayData.date)"></h4>
                                    <span class="text-sm text-gray-500" x-text="getDayName(dayData.date)"></span>
                                </div>
                                <div class="space-y-4">
                                    <div class="flex flex-wrap gap-2 text-sm">
                                        <div x-show="getAvailableSlotsCount(dayData) > 0" 
                                             class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span x-text="getAvailableSlotsCount(dayData)"></span> &nbsp;available slots
                                        </div>
                                        <div x-show="getReservedSlotsCount(dayData) > 0" 
                                             class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span x-text="getReservedSlotsCount(dayData)"></span> reserved slots
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                                        <template x-for="hourSlot in dayData.available_hours" :key="hourSlot.hour">
                                            <button 
                                                type="button"
                                                @click="selectTimeSlot(dayData.date, hourSlot.hour, hourSlot.available)"
                                                :class="{
                                                    'bg-green-100 text-green-800 hover:bg-green-200 border-green-300 cursor-pointer transform hover:scale-105': hourSlot.available,
                                                    'bg-yellow-100 text-yellow-800 cursor-not-allowed border-yellow-300': !hourSlot.available && hourSlot.blocked_by === 'existing_reservation',
                                                    'ring-2 ring-blue-500 ring-opacity-50': selectedTimeSlot === `${dayData.date} ${hourSlot.hour}`
                                                }"
                                                :disabled="!hourSlot.available"
                                                class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 border-2 relative"
                                            >
                                                <div class="font-semibold" x-text="hourSlot.hour"></div>
                                                <div x-show="!hourSlot.available" class="text-xs mt-1">
                                                    <span x-show="hourSlot.blocked_by === 'existing_reservation'" 
                                                          class="inline-flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Reserved
                                                    </span>
                                                </div>
                                                <div x-show="hourSlot.available" class="text-xs mt-1 text-green-600">
                                                    Available
                                                </div>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-white border-2 border-gray-200 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-6 h-6 bg-green-100 border-2 border-green-300 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Available</div>
                                <div class="text-sm text-gray-600">Can make reservation</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-6 h-6 bg-yellow-100 border-2 border-yellow-300 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">Reserved</div>
                                <div class="text-sm text-gray-600">Already has reservation</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div x-show="selectedTimeSlot" class="p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <h4 class="font-medium text-blue-900 mb-2">Selected Time</h4>
                    <p class="text-blue-800 mb-3" x-text="getSelectedTimeInfo()"></p>
                    <div class="flex space-x-3">
                        <button 
                            @click="proceedWithReservation"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                        >
                            Proceed with Reservation
                        </button>
                        <button 
                            @click="clearSelection"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors duration-200"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
                <div x-show="!loading && selectedMenuId && availabilityData.length === 0" class="text-center py-8">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-2 text-lg font-medium">No availability data</p>
                        <p class="text-sm">for the selected menu on this date</p>
                    </div>
                </div>
                <div x-show="alertMessage" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform translate-y-2"
                     class="fixed top-4 right-4 max-w-sm z-50">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span x-text="alertMessage"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function availabilityData() {
            return {
                selectedDate: new Date().toISOString().split('T')[0],
                selectedMenuId: '',
                loading: false,
                availabilityData: [],
                selectedTimeSlot: '',
                alertMessage: '',
                get currentDate() {
                    return new Date(this.selectedDate).getDate();
                },
                get currentYear() {
                    return new Date(this.selectedDate).getFullYear();
                },
                get currentMonth() {
                    const months = [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    return months[new Date(this.selectedDate).getMonth()];
                },
                getCurrentTime() {
                    const now = new Date();
                    return String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                },
                formatDisplayDate(date) {
                    return new Date(date).toLocaleDateString('en-US', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                },
                getDayName(date) {
                    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    return days[new Date(date).getDay()];
                },
                // Helper functions to count slots
                getAvailableSlotsCount(dayData) {
                    return dayData.available_hours.filter(slot => slot.available).length;
                },
                getReservedSlotsCount(dayData) {
                    return dayData.available_hours.filter(slot => !slot.available && slot.blocked_by === 'existing_reservation').length;
                },
                showAlert(message) {
                    this.alertMessage = message;
                    setTimeout(() => {
                        this.alertMessage = '';
                    }, 3000);
                },
                previousDay() {
                    const date = new Date(this.selectedDate);
                    date.setDate(date.getDate() - 1);
                    this.selectedDate = date.toISOString().split('T')[0];
                    this.clearSelection();
                    this.loadAvailability();
                },
                nextDay() {
                    const date = new Date(this.selectedDate);
                    date.setDate(date.getDate() + 1);
                    this.selectedDate = date.toISOString().split('T')[0];
                    this.clearSelection();
                    this.loadAvailability();
                },
                async loadAvailability() {
                    if (!this.selectedMenuId) {
                        this.availabilityData = [];
                        return;
                    }
                    this.loading = true;
                    this.clearSelection();
                    try {
                        const response = await fetch('{{ route('admin.reservation.availability') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                menu_id: this.selectedMenuId,
                                start_date: this.selectedDate,
                                end_date: this.selectedDate
                            })
                        });
                        const result = await response.json();
                        if (response.ok && result.success) {
                            this.availabilityData = result.data;
                        } else {
                            console.error('Error:', result.error || result.message);
                            this.availabilityData = [];
                            this.showAlert('Failed to load availability data');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.availabilityData = [];
                        this.showAlert('An error occurred while loading data');
                    } finally {
                        this.loading = false;
                    }
                },
                selectTimeSlot(date, hour, isAvailable) {
                    if (!isAvailable) {
                        this.showAlert('This time slot is not available for reservation');
                        return;
                    }
                    this.selectedTimeSlot = `${date} ${hour}`;
                },
                clearSelection() {
                    this.selectedTimeSlot = '';
                },
                getSelectedTimeInfo() {
                    if (!this.selectedTimeSlot) return '';
                    const [date, hour] = this.selectedTimeSlot.split(' ');
                    return `${this.formatDisplayDate(date)} at ${hour}`;
                },
                proceedWithReservation() {
                    if (!this.selectedTimeSlot) {
                        this.showAlert('Please select a time slot first');
                        return;
                    }
                    const [date, hour] = this.selectedTimeSlot.split(' ');
                    const datetime = `${date} ${hour}:00`;
                    // Redirect to create reservation page with parameters
                    const url = new URL('{{ route('admin.reservation.create') }}');
                    url.searchParams.set('menu_id', this.selectedMenuId);
                    url.searchParams.set('reservation_datetime', datetime);
                    window.location.href = url.toString();
                },
                init() {
                    // Load initial data if menu is selected
                    if (this.selectedMenuId) {
                        this.loadAvailability();
                    }
                }
            }
        }
    </script>
</x-layouts.app>