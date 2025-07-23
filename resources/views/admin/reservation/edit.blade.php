<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-edit mr-3 text-blue-600"></i>
                    Update Reservation #{{ $reservation->id }}
                </h1>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.reservation.update', $reservation->id) }}" x-data="reservationForm()">
                    @csrf
                    @method('PUT')
                    <div x-show="availabilityMessage" 
                         :class="availabilityStatus === 'available' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800'"
                         class="border rounded-md p-4 mb-6" 
                         style="display: none;">
                        <div class="flex items-center">
                            <i :class="availabilityStatus === 'available' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'" 
                               class="mr-2"></i>
                            <span x-text="availabilityMessage"></span>
                        </div>
                    </div>
                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800">An error occurred:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                            <div class="flex">
                                <i class="fas fa-check-circle text-green-400 mr-2"></i>
                                <p class="text-sm text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Customer Type</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="customer_type" value="existing" 
                                           x-model="customerType" 
                                           {{ $reservation->user_id ? 'checked' : '' }}
                                           class="mr-2">
                                    <span>Registered Customer</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="customer_type" value="guest" 
                                           x-model="customerType" 
                                           {{ !$reservation->user_id ? 'checked' : '' }}
                                           class="mr-2">
                                    <span>Guest Customer</span>
                                </label>
                            </div>
                        </div>
                        <div x-show="customerType === 'existing'" class="md:col-span-2">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Select Customer</label>
                            <select name="user_id" id="user_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Select Customer...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('user_id', $reservation->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="customerType === 'guest'" class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                    <input type="text" name="full_name" id="full_name" 
                                           value="{{ old('full_name', $reservation->full_name) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="full_name_kana" class="block text-sm font-medium text-gray-700 mb-2">Full Name (Kana) *</label>
                                    <input type="text" name="full_name_kana" id="full_name_kana" 
                                           value="{{ old('full_name_kana', $reservation->full_name_kana) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="email" id="email" 
                                           value="{{ old('email', $reservation->email) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                    <input type="text" name="phone_number" id="phone_number" 
                                           value="{{ old('phone_number', $reservation->phone_number) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="menu_id" class="block text-sm font-medium text-gray-700 mb-2">Menu *</label>
                            <select name="menu_id" id="menu_id" x-model="selectedMenu" @change="checkAvailability()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Menu...</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}" data-price="{{ $menu->price }}" data-duration="{{ $menu->required_time }}"
                                            {{ old('menu_id', $reservation->menu_id) == $menu->id ? 'selected' : '' }}>
                                        {{ $menu->name }} ({{ number_format($menu->price, 0, ',', '.') }} yen - {{ $menu->required_time }} minutes)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="reservation_datetime" class="block text-sm font-medium text-gray-700 mb-2">Reservation Date & Time *</label>
                            <input type="datetime-local" name="reservation_datetime" id="reservation_datetime" 
                                   x-model="reservationDateTime" @change="checkAvailability()"
                                   value="{{ old('reservation_datetime', $reservation->reservation_datetime->format('Y-m-d\TH:i')) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <button type="button" @click="openCalendarModal()"
                            class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-300 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Select from Calendar
                            </button>
                        </div>
                        <div>
                            <label for="number_of_people" class="block text-sm font-medium text-gray-700 mb-2">Number of People *</label>
                            <input type="number" name="number_of_people" id="number_of_people" min="1" 
                                   value="{{ old('number_of_people', $reservation->number_of_people) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Total Amount *</label>
                            <input type="number" name="amount" id="amount" step="0.01" 
                                   value="{{ old('amount', $reservation->amount) }}"
                                   x-model="amount" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ old('status', $reservation->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ old('status', $reservation->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $reservation->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.reservation.calendar') }}" 
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="button" @click="checkAvailability()" 
                                class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <i class="fas fa-check mr-2"></i>
                            Check Availability
                        </button>
                        <button type="submit" 
                                :disabled="!isFormValid || (availabilityStatus !== 'available' && availabilityStatus !== null)"
                                :class="(!isFormValid || (availabilityStatus !== 'available' && availabilityStatus !== null)) ? 'opacity-50 cursor-not-allowed' : ''"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            Update Reservation
                        </button>
                    </div>
                    <div x-show="showCalendarModal" 
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" 
                        style="display: none;">
                        <div x-show="showCalendarModal"
                            x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-200 transform"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[80vh] overflow-y-auto">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Select the date you want to book</h3>
                                <button @click="closeCalendarModal()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="p-6">
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Date</label>
                                    <div class="grid grid-cols-7 gap-1 text-center text-sm">
                                        <div class="font-medium text-gray-500 py-2">Sun</div>
                                        <div class="font-medium text-gray-500 py-2">Mon</div>
                                        <div class="font-medium text-gray-500 py-2">Tue</div>
                                        <div class="font-medium text-gray-500 py-2">Wed</div>
                                        <div class="font-medium text-gray-500 py-2">Thu</div>
                                        <div class="font-medium text-gray-500 py-2">Fri</div>
                                        <div class="font-medium text-gray-500 py-2">Sat</div>
                                        <template x-for="day in calendarDays" :key="day.date">
                                            <div class="relative">
                                                <button type="button"
                                                        @click="selectDate(day.date)"
                                                        :disabled="day.disabled || day.isPast || day.isFullyBlocked"
                                                        :class="{
                                                            'bg-blue-500 text-white border-blue-500': selectedDate === day.date && !day.disabled && !day.isPast && !day.isFullyBlocked,
                                                            'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200': day.disabled || day.isPast,
                                                            'bg-red-50 border-red-300 text-red-400 cursor-not-allowed': day.isFullyBlocked && !day.isPast,
                                                            'hover:bg-blue-100 border-blue-300': !day.disabled && !day.isPast && !day.isFullyBlocked && selectedDate !== day.date && day.hasAvailableTime,
                                                            'hover:bg-gray-50 border-gray-300': !day.disabled && !day.isPast && !day.isFullyBlocked && selectedDate !== day.date && !day.hasAvailableTime,
                                                            'text-gray-400': day.otherMonth,
                                                            'border-gray-200': !day.disabled && !day.isPast && !day.isFullyBlocked && selectedDate !== day.date
                                                        }"
                                                        class="h-10 w-10 rounded-md border flex items-center justify-center text-sm transition-colors relative">
                                                    <span x-text="day.day" :class="(day.isFullyBlocked || day.isPast) ? 'opacity-50' : ''"></span>
                                                </button>
                                                <div x-show="day.isFullyBlocked && !day.isPast" 
                                                    class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">
                                                    ×
                                                </div>
                                                <div x-show="day.isMixed && !day.isFullyBlocked && !day.isPast" 
                                                    class="absolute top-0 right-0 -mt-0.5 -mr-0.5 w-3 h-3 bg-gradient-to-r from-orange-400 to-purple-500 rounded-full border border-white"></div>
                                                <div x-show="day.hasBlockedTimes && !day.hasReservationBlocked && !day.isMixed && !day.isFullyBlocked && !day.isPast" 
                                                    class="absolute top-0 right-0 -mt-0.5 -mr-0.5 w-3 h-3 bg-orange-400 rounded-full"></div>
                                                <div x-show="day.hasReservationBlocked && !day.hasBlockedTimes && !day.isMixed && !day.isFullyBlocked && !day.isPast" 
                                                    class="absolute top-0 right-0 -mt-0.5 -mr-0.5 w-3 h-3 bg-purple-500 rounded-full"></div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex justify-between items-center mt-4">
                                        <button type="button" @click="previousMonth()" 
                                                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-chevron-left mr-0.5"></i>
                                            Previous
                                        </button>
                                        <h4 class="text-lg font-medium text-gray-900" x-text="currentMonthYear"></h4>
                                        <button type="button" @click="nextMonth()" 
                                                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">
                                            Next
                                            <i class="fas fa-chevron-right ml-1"></i>
                                        </button>
                                    </div>
                                </div>
                                <div x-show="selectedDate && !isSelectedDateFullyBlocked" class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Time</label>
                                    <div class="grid grid-cols-3 gap-2">
                                        <template x-for="time in availableTimes" :key="time.value">
                                            <div class="relative">
                                                <button type="button"
                                                        @click="selectTime(time.value)"
                                                        :disabled="time.disabled"
                                                        :class="{
                                                            'bg-blue-500 text-white border-blue-500': selectedTime === time.value && !time.disabled,
                                                            'bg-gray-100 text-gray-400 cursor-not-allowed border-gray-200': time.disabled && time.blockedBy === 'blocked_period',
                                                            'bg-purple-50 border-purple-300 text-purple-400 cursor-not-allowed': time.disabled && time.blockedBy === 'existing_reservation',
                                                            'bg-red-50 border-red-300 text-red-400 cursor-not-allowed': time.disabled && (!time.blockedBy || time.blockedBy === 'unknown'),
                                                            'hover:bg-blue-100 border-blue-300': !time.disabled && selectedTime !== time.value,
                                                            'border-gray-300': !time.disabled && selectedTime !== time.value
                                                        }"
                                                        class="px-3 py-2 text-sm border rounded-md transition-colors flex items-center justify-center w-full relative">
                                                    <span x-text="time.label" :class="time.disabled ? 'opacity-50' : ''"></span>
                                                </button>
                                                <div x-show="time.disabled && time.blockedBy === 'blocked_period'" 
                                                    class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-orange-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">
                                                    B
                                                </div>
                                                <div x-show="time.disabled && time.blockedBy === 'existing_reservation'" 
                                                    class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-purple-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">
                                                    R
                                                </div>
                                                <div x-show="time.disabled && (!time.blockedBy || time.blockedBy === 'unknown')" 
                                                    class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">
                                                    ×
                                                </div>
                                                <div x-show="time.disabled && time.blockedBy === 'past_time'" 
                                                    class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-gray-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">
                                                    ×
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div x-show="selectedDate && isSelectedDateFullyBlocked" class="mb-6">
                                    <div class="bg-red-50 border border-red-200 rounded-md p-4 text-center">
                                        <i class="fas fa-exclamation-triangle text-red-500 mb-2"></i>
                                        <p class="text-sm text-red-700">
                                            This date is fully blocked and not available for reservations.
                                        </p>
                                    </div>
                                </div>
                                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Legend:</h4>
                                    <div class="flex flex-wrap gap-4 text-xs">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 bg-red-500 rounded-full mr-2 flex items-center justify-center text-white text-xs font-bold">×</div>
                                            <span class="text-gray-600">Fully Blocked</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-orange-400 rounded-full mr-2"></div>
                                            <span class="text-gray-600">Blocked Period</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                                            <span class="text-gray-600">Has Reservation</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-gradient-to-r from-orange-400 to-purple-500 rounded-full mr-2"></div>
                                            <span class="text-gray-600">Mixed (Blocked + Reservation)</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-blue-500 rounded-md mr-2"></div>
                                            <span class="text-gray-600">Available</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-gray-300 rounded-md mr-2"></div>
                                            <span class="text-gray-600">Past Date</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                                <button type="button" @click="closeCalendarModal()" 
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="button" @click="confirmSelection()" 
                                        :disabled="!selectedDate || !selectedTime || isSelectedDateFullyBlocked"
                                        :class="(!selectedDate || !selectedTime || isSelectedDateFullyBlocked) ? 'opacity-50 cursor-not-allowed' : ''"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                    Confirm
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function reservationForm() {
            return {
                customerType: '{{ $reservation->user_id ? 'existing' : 'guest' }}',
                selectedMenu: '{{ old('menu_id', $reservation->menu_id) }}',
                reservationDateTime: '{{ old('reservation_datetime', $reservation->reservation_datetime->format('Y-m-d\TH:i')) }}',
                amount: '{{ old('amount', $reservation->amount) }}',
                originalReservationId: '{{ $reservation->id }}',
                availabilityStatus: null,
                availabilityMessage: '',
                isFormValid: false,
                showCalendarModal: false,
                selectedDate: null,
                selectedTime: null,
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                calendarDays: [],
                availableTimes: [],
                blockedDates: [],
                blockedTimes: {},
                availabilityData: [],
                isSelectedDateFullyBlocked: false,
                
                init() {
                    this.updateAmount();
                    this.validateForm();
                    
                    this.$watch('customerType', () => {
                        this.validateForm();
                    });
                    
                    this.$watch('selectedMenu', () => {
                        this.updateAmount();
                        this.validateForm();
                    });
                    
                    this.$watch('reservationDateTime', () => {
                        this.validateForm();
                    });
                    
                    // Setup event listeners untuk guest fields
                    setTimeout(() => {
                        const guestFields = ['full_name', 'full_name_kana', 'email', 'phone_number'];
                        guestFields.forEach(fieldId => {
                            const field = document.getElementById(fieldId);
                            if (field) {
                                field.addEventListener('input', () => {
                                    this.validateForm();
                                });
                            }
                        });
                        
                        const userSelect = document.getElementById('user_id');
                        if (userSelect) {
                            userSelect.addEventListener('change', () => {
                                this.validateForm();
                            });
                        }
                    }, 100);
                },
                
                updateAmount() {
                    if (this.selectedMenu) {
                        const menuSelect = document.getElementById('menu_id');
                        const selectedOption = menuSelect.options[menuSelect.selectedIndex];
                        if (selectedOption && selectedOption.dataset.price) {
                            this.amount = selectedOption.dataset.price;
                        }
                    }
                },
                
                validateForm() {
                    const menuSelected = this.selectedMenu && this.selectedMenu !== '';
                    const dateTimeSelected = this.reservationDateTime && this.reservationDateTime !== '';
                    let customerInfoValid = false;
                    
                    if (this.customerType === 'existing') {
                        const userSelect = document.getElementById('user_id');
                        customerInfoValid = userSelect && userSelect.value !== '';
                    } else {
                        const fullName = document.getElementById('full_name');
                        const fullNameKana = document.getElementById('full_name_kana');
                        const email = document.getElementById('email');
                        const phoneNumber = document.getElementById('phone_number');
                        
                        customerInfoValid = fullName && fullName.value.trim() !== '' &&
                                          fullNameKana && fullNameKana.value.trim() !== '' &&
                                          email && email.value.trim() !== '' &&
                                          phoneNumber && phoneNumber.value.trim() !== '';
                    }
                    
                    this.isFormValid = menuSelected && dateTimeSelected && customerInfoValid;
                    
                    console.log('Form validation:', {
                        menuSelected,
                        dateTimeSelected,
                        customerInfoValid,
                        customerType: this.customerType,
                        isFormValid: this.isFormValid,
                        availabilityStatus: this.availabilityStatus
                    });
                },
                
                async checkAvailability() {
                    if (!this.selectedMenu || !this.reservationDateTime) {
                        this.availabilityMessage = 'Please select menu and date/time first';
                        this.availabilityStatus = 'error';
                        return;
                    }
                    
                    try {
                        const response = await fetch('{{ route('admin.reservation.check-availability') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                menu_id: this.selectedMenu,
                                reservation_datetime: this.reservationDateTime,
                                exclude_reservation_id: this.originalReservationId // Exclude current reservation when updating
                            })
                        });
                        
                        const data = await response.json();
                        console.log('Availability check response:', data);
                        
                        if (data.available) {
                            this.availabilityStatus = 'available';
                            this.availabilityMessage = 'Time slot is available for reservation';
                        } else {
                            this.availabilityStatus = 'unavailable';
                            this.availabilityMessage = 'Time slot is not available - conflicts with blocked periods or other reservations';
                        }
                        
                        this.validateForm();
                    } catch (error) {
                        console.error('Error checking availability:', error);
                        this.availabilityStatus = 'error';
                        this.availabilityMessage = 'An error occurred while checking availability';
                    }
                },
                
                submitForm() {
                    this.validateForm();
                    if (this.isFormValid && this.availabilityStatus === 'available') {
                        document.querySelector('form').submit();
                    } else {
                        console.log('Form is invalid or time slot is not available:', {
                            isFormValid: this.isFormValid,
                            availabilityStatus: this.availabilityStatus
                        });
                    }
                },
                
                get currentMonthYear() {
                    const months = ['January', 'February', 'March', 'April', 'May', 'June',
                                  'July', 'August', 'September', 'October', 'November', 'December'];
                    return `${months[this.currentMonth]} ${this.currentYear}`;
                },
                
                openCalendarModal() {
                    if (!this.selectedMenu) {
                        alert('Please select a menu first');
                        return;
                    }
                    
                    this.showCalendarModal = true;
                    this.selectedDate = null;
                    this.selectedTime = null;
                    this.availableTimes = [];
                    
                    this.loadBlockedDates().then(() => {
                        this.generateCalendar();
                    }).catch(error => {
                        console.error('Error loading calendar data:', error);
                        this.generateCalendar();
                    });
                },
                
                closeCalendarModal() {
                    this.showCalendarModal = false;
                    this.selectedDate = null;
                    this.selectedTime = null;
                },
                
                generateCalendar() {
                    const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                    const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                    const startDate = new Date(firstDay);
                    startDate.setDate(startDate.getDate() - firstDay.getDay());
                    
                    const days = [];
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    for (let i = 0; i < 42; i++) {
                        const date = new Date(startDate);
                        date.setDate(startDate.getDate() + i);
                        
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        const dateStr = `${year}-${month}-${day}`;
                        
                        const isCurrentMonth = date.getMonth() === this.currentMonth;
                        const isPast = date < today;
                        
                        const dateAvailability = this.availabilityData.find(item => item.date === dateStr);
                        const isFullyBlocked = dateAvailability ? dateAvailability.is_blocked : false;
                        
                        let hasAvailableTime = false;
                        let hasBlockedTimes = false;
                        let hasReservationBlocked = false;
                        
                        if (dateAvailability && dateAvailability.available_hours) {
                            const availableCount = dateAvailability.available_hours.filter(hour => hour.available).length;
                            const blockedCount = dateAvailability.available_hours.filter(hour => !hour.available).length;
                            
                            hasAvailableTime = availableCount > 0;
                            hasBlockedTimes = dateAvailability.has_blocked_periods || false;
                            hasReservationBlocked = dateAvailability.has_reservations || false;
                        }
                        
                        days.push({
                            date: dateStr,
                            day: date.getDate(),
                            otherMonth: !isCurrentMonth,
                            isPast: isPast,
                            isFullyBlocked: isFullyBlocked,
                            hasBlockedTimes: hasBlockedTimes && !isFullyBlocked,
                            hasReservationBlocked: hasReservationBlocked && !isFullyBlocked,
                            isMixed: dateAvailability ? dateAvailability.is_mixed : false,
                            hasAvailableTime: hasAvailableTime,
                            disabled: isPast || isFullyBlocked
                        });
                    }
                    
                    this.calendarDays = days;
                    console.log('Calendar generated with days:', days.length);
                },
                
                selectDate(dateStr) {
                    const dateAvailability = this.availabilityData.find(item => item.date === dateStr);
                    this.isSelectedDateFullyBlocked = dateAvailability ? dateAvailability.is_blocked : false;
                    
                    if (!this.isSelectedDateFullyBlocked) {
                        this.selectedDate = dateStr;
                        this.selectedTime = null;
                        this.generateAvailableTimes();
                    }
                    
                    console.log('Selected date:', dateStr, 'Is blocked:', this.isSelectedDateFullyBlocked);
                },
                
                generateAvailableTimes() {
                    const times = [];
                    const dateAvailability = this.availabilityData.find(item => item.date === this.selectedDate);
                    const today = new Date();
                    const todayStr = today.toISOString().split('T')[0];
                    const currentHour = today.getHours();

                    if (dateAvailability && dateAvailability.available_hours) {
                        dateAvailability.available_hours.forEach(hourInfo => {
                            const hour = parseInt(hourInfo.hour.split(':')[0]);
                            const timeStr = hourInfo.hour;
                            const displayTime = `${hour}:00`;
                            
                            let isDisabled = !hourInfo.available;
                            let blockedBy = hourInfo.blocked_by || 'unknown';
                            
                            // Check if this is past time for today
                            if (this.selectedDate === todayStr && hour < currentHour) {
                                isDisabled = true;
                                blockedBy = 'past_time';
                            }
                            
                            times.push({
                                value: timeStr,
                                label: displayTime,
                                disabled: isDisabled,
                                blockedBy: blockedBy
                            });
                        });
                    }
                    
                    this.availableTimes = times;
                },
                
                previousMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                    
                    this.loadBlockedDates().then(() => {
                        this.generateCalendar();
                    });
                },
                
                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                    
                    this.loadBlockedDates().then(() => {
                        this.generateCalendar();
                    });
                },
                
                selectTime(timeStr) {
                    this.selectedTime = timeStr;
                    console.log('Selected time:', timeStr); 
                },
                
                confirmSelection() {
                    if (this.selectedDate && this.selectedTime && !this.isSelectedDateFullyBlocked) {
                        const dateTimeStr = `${this.selectedDate}T${this.selectedTime}`;
                        console.log('Confirming selection:', {
                            selectedDate: this.selectedDate,
                            selectedTime: this.selectedTime,
                            dateTimeStr: dateTimeStr
                        });
                        
                        this.reservationDateTime = dateTimeStr;
                        
                        // Update the datetime input field
                        const datetimeInput = document.getElementById('reservation_datetime');
                        if (datetimeInput) {
                            datetimeInput.value = dateTimeStr;
                            datetimeInput.dispatchEvent(new Event('change'));
                        }
                        
                        this.closeCalendarModal();
                        this.checkAvailability();
                    } else {
                        if (this.isSelectedDateFullyBlocked) {
                            alert('Selected date is fully blocked and not available for reservations.');
                        } else {
                            alert('Please select both date and time.');
                        }
                    }
                },
                
                async loadBlockedDates() {
                    if (!this.selectedMenu) {
                        console.log('No menu selected, cannot load blocked dates');
                        return;
                    }
                    
                    try {
                        const response = await fetch('{{ route('admin.reservation.availability') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                menu_id: this.selectedMenu,
                                start_date: `${this.currentYear}-${(this.currentMonth + 1).toString().padStart(2, '0')}-01`,
                                end_date: `${this.currentYear}-${(this.currentMonth + 1).toString().padStart(2, '0')}-31`,
                                exclude_reservation_id: this.originalReservationId // Exclude current reservation
                            })
                        });
                        
                        const data = await response.json();
                        console.log('Availability data received:', data);
                        
                        if (data.success && data.data) {
                            this.availabilityData = data.data;
                            this.blockedDates = [];
                            this.blockedTimes = {};
                            
                            data.data.forEach(dateInfo => {
                                if (dateInfo.is_blocked) {
                                    this.blockedDates.push(dateInfo.date);
                                }
                                
                                const unavailableHours = [];
                                if (dateInfo.available_hours) {
                                    dateInfo.available_hours.forEach(hourInfo => {
                                        if (!hourInfo.available) {
                                            const hour = parseInt(hourInfo.hour.split(':')[0]);
                                            unavailableHours.push(hour);
                                        }
                                    });
                                }
                                
                                if (unavailableHours.length > 0) {
                                    this.blockedTimes[dateInfo.date] = unavailableHours;
                                }
                            });
                            
                            console.log('Processed availability data:', {
                                availabilityData: this.availabilityData,
                                blockedDates: this.blockedDates,
                                blockedTimes: this.blockedTimes
                            });
                        } else {
                            console.error('Failed to load availability data:', data.message || 'Unknown error');
                            this.availabilityData = [];
                        }
                        
                        this.generateCalendar();
                    } catch (error) {
                        console.error('Error loading blocked dates:', error);
                        this.availabilityData = [];
                        this.generateCalendar();
                    }
                }
            }
        }

        // Ensure CSRF token is available
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }

        // Additional DOM ready event listeners
        document.addEventListener('DOMContentLoaded', function() {
            const menuSelect = document.getElementById('menu_id');
            if (menuSelect) {
                menuSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    if (selectedOption && selectedOption.dataset.price) {
                        document.getElementById('amount').value = selectedOption.dataset.price;
                    }
                });
            }
        });
    </script>
</x-layouts.app>