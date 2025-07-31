<x-layouts.app>
    <div class="container">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-edit mr-3 text-blue-600"></i>
                    {{ __('admin/reservation/edit.title', ['id' => $reservation->id]) }}
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
                                    <h3 class="text-sm font-medium text-red-800">{{ __('admin/reservation/edit.error_occurred') }}</h3>
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
                            <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('admin/reservation/edit.customer_type_label') }}</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="customer_type" value="existing"
                                           x-model="customerType"
                                           {{ $reservation->user_id ? 'checked' : '' }}
                                           class="mr-2">
                                    <span>{{ __('admin/reservation/edit.registered_customer') }}</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="customer_type" value="guest"
                                           x-model="customerType"
                                           {{ !$reservation->user_id ? 'checked' : '' }}
                                           class="mr-2">
                                    <span>{{ __('admin/reservation/edit.guest_customer') }}</span>
                                </label>
                            </div>
                        </div>
                        <div x-show="customerType === 'existing'" class="md:col-span-2">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.select_customer_label') }}</label>
                            <select name="user_id" id="user_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">{{ __('admin/reservation/edit.select_customer_option') }}</option>
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
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.full_name_label') }}</label>
                                    <input type="text" name="full_name" id="full_name"
                                           value="{{ old('full_name', $reservation->full_name) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="full_name_kana" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.full_name_kana_label') }}</label>
                                    <input type="text" name="full_name_kana" id="full_name_kana"
                                           value="{{ old('full_name_kana', $reservation->full_name_kana) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.email_label') }}</label>
                                    <input type="email" name="email" id="email"
                                           value="{{ old('email', $reservation->email) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.phone_number_label') }}</label>
                                    <input type="text" name="phone_number" id="phone_number"
                                           value="{{ old('phone_number', $reservation->phone_number) }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="menu_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.menu_label') }}</label>
                            <select name="menu_id" id="menu_id" x-model="selectedMenu" @change="checkAvailability()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">{{ __('admin/reservation/edit.select_menu_option') }}</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}" data-price="{{ $menu->price }}" data-duration="{{ $menu->required_time }}"
                                            {{ old('menu_id', $reservation->menu_id) == $menu->id ? 'selected' : '' }}>
                                        {{ $menu->name }} ({{ number_format($menu->price, 0, ',', '.') }} {{ __('admin/reservation/edit.yen') }} - {{ $menu->required_time }} {{ __('admin/reservation/edit.minutes') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="reservation_datetime_display" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.reservation_datetime_label') }}</label>
                            <input type="hidden" name="reservation_datetime" id="reservation_datetime"
                                   x-model="reservationDateTime" @change="checkAvailability()"
                                   value="{{ old('reservation_datetime', $reservation->reservation_datetime->format('Y-m-d\TH:i')) }}">
                            <button type="button" @click="openCalendarModal()"
                                    class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 border border-blue-300 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                {{ __('admin/reservation/edit.select_from_calendar_button') }}
                            </button>
                        </div>
                        <div>
                            <label for="number_of_people" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.number_of_people_label') }}</label>
                            <input type="number" name="number_of_people" id="number_of_people" min="1"
                                   value="{{ old('number_of_people', $reservation->number_of_people) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.total_amount_label') }}</label>
                            <input type="number" name="amount" id="amount" step="0.01"
                                   value="{{ old('amount', $reservation->amount) }}"
                                   x-model="amount" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.status_label') }}</label>
                            <select name="status" id="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>{{ __('admin/reservation/edit.status_pending') }}</option>
                                <option value="confirmed" {{ old('status', $reservation->status) == 'confirmed' ? 'selected' : '' }}>{{ __('admin/reservation/edit.status_confirmed') }}</option>
                                <option value="completed" {{ old('status', $reservation->status) == 'completed' ? 'selected' : '' }}>{{ __('admin/reservation/edit.status_completed') }}</option>
                                <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>{{ __('admin/reservation/edit.status_cancelled') }}</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.notes_label') }}</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $reservation->notes) }}</textarea>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.reservation.calendar') }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('admin/reservation/edit.cancel_button') }}
                        </a>
                        <button type="button" @click="checkAvailability()"
                                class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            <i class="fas fa-check mr-2"></i>
                            {{ __('admin/reservation/edit.check_availability_button') }}
                        </button>
                        <button type="submit"
                                :disabled="!isFormValid || (availabilityStatus !== 'available' && availabilityStatus !== null)"
                                :class="(!isFormValid || (availabilityStatus !== 'available' && availabilityStatus !== null)) ? 'opacity-50 cursor-not-allowed' : ''"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            {{ __('admin/reservation/edit.update_reservation_button') }}
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
                        <div @click.away="closeCalendarModal()"
                             x-show="showCalendarModal"
                             x-transition:enter="transition ease-out duration-300 transform"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200 transform"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[80vh] overflow-y-auto">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('admin/reservation/edit.modal_title') }}</h3>
                                <button @click="closeCalendarModal()" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="p-6">
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('admin/reservation/edit.select_date_label') }}</label>
                                    <div class="grid grid-cols-7 gap-1 text-center text-sm">
                                        <div class="font-medium text-gray-500 py-2">{{ __('admin/reservation/edit.days_of_week.sun') }}</div>
                                        <div class="font-medium text-gray-500 py-2">{{ __('admin/reservation/edit.days_of_week.mon') }}</div>
                                        <div class="font-medium text-gray-500 py-2">{{ __('admin/reservation/edit.days_of_week.tue') }}</div>
                                        <div class="font-medium text-gray-500 py-2">{{ __('admin/reservation/edit.days_of_week.wed') }}</div>
                                        <div class="font-medium text-gray-500 py-2">{{ __('admin/reservation/edit.days_of_week.thu') }}</div>
                                        <div class="font-medium text-gray-500 py-2">{{ __('admin/reservation/edit.days_of_week.fri') }}</div>
                                        <div class="font-medium text-gray-500 py-2">{{ __('admin/reservation/edit.days_of_week.sat') }}</div>
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
                                                 <div x-show="day.isFullyBlocked && !day.isPast" class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">×</div>
                                                 <div x-show="day.isMixed && !day.isFullyBlocked && !day.isPast" class="absolute top-0 right-0 -mt-0.5 -mr-0.5 w-3 h-3 bg-gradient-to-r from-orange-400 to-purple-500 rounded-full border border-white"></div>
                                                 <div x-show="day.hasBlockedTimes && !day.hasReservationBlocked && !day.isMixed && !day.isFullyBlocked && !day.isPast" class="absolute top-0 right-0 -mt-0.5 -mr-0.5 w-3 h-3 bg-orange-400 rounded-full"></div>
                                                 <div x-show="day.hasReservationBlocked && !day.hasBlockedTimes && !day.isMixed && !day.isFullyBlocked && !day.isPast" class="absolute top-0 right-0 -mt-0.5 -mr-0.5 w-3 h-3 bg-purple-500 rounded-full"></div>
                                            </div>
                                        </template>
                                    </div>
                                    <div class="flex justify-between items-center mt-4">
                                        <button type="button" @click="previousMonth()"
                                                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-chevron-left mr-0.5"></i>
                                            {{ __('admin/reservation/edit.previous_button') }}
                                        </button>
                                        <h4 class="text-lg font-medium text-gray-900" x-text="currentMonthYear"></h4>
                                        <button type="button" @click="nextMonth()"
                                                class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800">
                                            {{ __('admin/reservation/edit.next_button') }}
                                            <i class="fas fa-chevron-right ml-1"></i>
                                        </button>
                                    </div>
                                </div>
                                <div x-show="selectedDate && !isSelectedDateFullyBlocked" class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">{{ __('admin/reservation/edit.select_time_label') }}</label>
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
                                                <div x-show="time.disabled && time.blockedBy === 'blocked_period'" class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-orange-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">B</div>
                                                <div x-show="time.disabled && time.blockedBy === 'existing_reservation'" class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-purple-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">R</div>
                                                <div x-show="time.disabled && (!time.blockedBy || time.blockedBy === 'unknown')" class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">×</div>
                                                <div x-show="time.disabled && time.blockedBy === 'past_time'" class="absolute top-0 right-0 -mt-0.5 -mr-0.5 bg-gray-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-xs font-bold">×</div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                                <div x-show="selectedDate && isSelectedDateFullyBlocked" class="mb-6">
                                    <div class="bg-red-50 border border-red-200 rounded-md p-4 text-center">
                                        <i class="fas fa-exclamation-triangle text-red-500 mb-2"></i>
                                        <p class="text-sm text-red-700">
                                            {{ __('admin/reservation/edit.date_fully_blocked_message') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mb-4 p-3 bg-gray-50 rounded-md">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('admin/reservation/edit.legend_title') }}</h4>
                                    <div class="flex flex-wrap gap-4 text-xs">
                                        <div class="flex items-center">
                                            <div class="w-4 h-4 bg-red-500 rounded-full mr-2 flex items-center justify-center text-white text-xs font-bold">×</div>
                                            <span class="text-gray-600">{{ __('admin/reservation/edit.legend_fully_blocked') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-orange-400 rounded-full mr-2"></div>
                                            <span class="text-gray-600">{{ __('admin/reservation/edit.legend_blocked_period') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
                                            <span class="text-gray-600">{{ __('admin/reservation/edit.legend_has_reservation') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-gradient-to-r from-orange-400 to-purple-500 rounded-full mr-2"></div>
                                            <span class="text-gray-600">{{ __('admin/reservation/edit.legend_mixed') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-blue-500 rounded-md mr-2"></div>
                                            <span class="text-gray-600">{{ __('admin/reservation/edit.legend_available') }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-3 h-3 bg-gray-300 rounded-md mr-2"></div>
                                            <span class="text-gray-600">{{ __('admin/reservation/edit.legend_past_date') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                                <button type="button" @click="closeCalendarModal()"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                    {{ __('admin/reservation/edit.modal_cancel_button') }}
                                </button>
                                <button type="button" @click="confirmSelection()"
                                        :disabled="!selectedDate || !selectedTime || isSelectedDateFullyBlocked"
                                        :class="(!selectedDate || !selectedTime || isSelectedDateFullyBlocked) ? 'opacity-50 cursor-not-allowed' : ''"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                    {{ __('admin/reservation/edit.modal_confirm_button') }}
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
                    
                    this.$watch('customerType', () => { this.validateForm(); });
                    this.$watch('selectedMenu', () => { this.updateAmount(); this.validateForm(); });
                    this.$watch('reservationDateTime', () => { this.validateForm(); });
                    
                    setTimeout(() => {
                        const guestFields = ['full_name', 'full_name_kana', 'email', 'phone_number'];
                        guestFields.forEach(fieldId => {
                            const field = document.getElementById(fieldId);
                            if (field) {
                                field.addEventListener('input', () => this.validateForm());
                            }
                        });
                        const userSelect = document.getElementById('user_id');
                        if (userSelect) {
                            userSelect.addEventListener('change', () => this.validateForm());
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
                },
                
                async checkAvailability() {
                    if (!this.selectedMenu || !this.reservationDateTime) {
                        this.availabilityMessage = '{{ __('admin/reservation/edit.js_select_menu_datetime_first') }}';
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
                                exclude_reservation_id: this.originalReservationId
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.available) {
                            this.availabilityStatus = 'available';
                            this.availabilityMessage = '{{ __('admin/reservation/edit.js_timeslot_available') }}';
                        } else {
                            this.availabilityStatus = 'unavailable';
                            this.availabilityMessage = '{{ __('admin/reservation/edit.js_timeslot_unavailable') }}';
                        }
                        
                        this.validateForm();
                    } catch (error) {
                        console.error('Error checking availability:', error);
                        this.availabilityStatus = 'error';
                        this.availabilityMessage = '{{ __('admin/reservation/edit.js_availability_error') }}';
                    }
                },
                
                submitForm() {
                    this.validateForm();
                    if (this.isFormValid && this.availabilityStatus === 'available') {
                        document.querySelector('form').submit();
                    }
                },
                
                get currentMonthYear() {
                    const months = @json(__('admin/reservation/edit.months'));
                    const monthName = months[this.currentMonth];
                    if (document.documentElement.lang === 'ja') {
                         return `${this.currentYear}年 ${monthName}`;
                    }
                    return `${monthName} ${this.currentYear}`;
                },
                
                openCalendarModal() {
                    if (!this.selectedMenu) {
                        alert('{{ __('admin/reservation/edit.js_select_menu_first_alert') }}');
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
                
                closeCalendarModal() { this.showCalendarModal = false; this.selectedDate = null; this.selectedTime = null; },
                
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
                        
                        const dateStr = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
                        const isCurrentMonth = date.getMonth() === this.currentMonth;
                        const isPast = date < today;
                        const dateAvailability = this.availabilityData.find(item => item.date === dateStr);
                        const isFullyBlocked = dateAvailability ? dateAvailability.is_blocked : false;
                        
                        let hasAvailableTime = false, hasBlockedTimes = false, hasReservationBlocked = false;
                        
                        if (dateAvailability && dateAvailability.available_hours) {
                             hasAvailableTime = dateAvailability.available_hours.some(hour => hour.available);
                             hasBlockedTimes = dateAvailability.has_blocked_periods || false;
                             hasReservationBlocked = dateAvailability.has_reservations || false;
                        }
                        
                        days.push({
                            date: dateStr, day: date.getDate(), otherMonth: !isCurrentMonth, isPast: isPast,
                            isFullyBlocked: isFullyBlocked, hasBlockedTimes: hasBlockedTimes && !isFullyBlocked,
                            hasReservationBlocked: hasReservationBlocked && !isFullyBlocked,
                            isMixed: dateAvailability ? dateAvailability.is_mixed : false,
                            hasAvailableTime: hasAvailableTime, disabled: isPast || isFullyBlocked
                        });
                    }
                    
                    this.calendarDays = days;
                },
                
                selectDate(dateStr) {
                    const dateAvailability = this.availabilityData.find(item => item.date === dateStr);
                    this.isSelectedDateFullyBlocked = dateAvailability ? dateAvailability.is_blocked : false;
                    
                    if (!this.isSelectedDateFullyBlocked) {
                        this.selectedDate = dateStr;
                        this.selectedTime = null;
                        this.generateAvailableTimes();
                    }
                },
                
                generateAvailableTimes() {
                    const times = [];
                    const dateAvailability = this.availabilityData.find(item => item.date === this.selectedDate);
                    const now = new Date();
                    if (dateAvailability && dateAvailability.available_hours) {
                        dateAvailability.available_hours.forEach(hourInfo => {
                            const timeStr = hourInfo.hour;
                            const slotDateTime = new Date(`${this.selectedDate}T${timeStr}`);
                            let isDisabled = !hourInfo.available;
                            let blockedBy = hourInfo.blocked_by || 'unknown';
                            if (slotDateTime < now) {
                                isDisabled = true;
                                blockedBy = 'past_time';
                            }
                            times.push({
                                value: timeStr, label: `${parseInt(timeStr.split(':')[0])}:00`,
                                disabled: isDisabled, blockedBy: blockedBy
                            });
                        });
                    }
                    this.availableTimes = times;
                },
                
                previousMonth() {
                    if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; } else { this.currentMonth--; }
                    this.loadBlockedDates().then(() => { this.generateCalendar(); });
                },
                
                nextMonth() {
                    if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; } else { this.currentMonth++; }
                    this.loadBlockedDates().then(() => { this.generateCalendar(); });
                },
                
                selectTime(timeStr) { this.selectedTime = timeStr; },
                
                confirmSelection() {
                    if (this.selectedDate && this.selectedTime && !this.isSelectedDateFullyBlocked) {
                        const dateTimeStr = `${this.selectedDate}T${this.selectedTime}`;
                        this.reservationDateTime = dateTimeStr;
                        const datetimeInput = document.getElementById('reservation_datetime');
                        if (datetimeInput) {
                            datetimeInput.value = dateTimeStr;
                            datetimeInput.dispatchEvent(new Event('change'));
                        }
                        this.closeCalendarModal();
                        this.checkAvailability();
                    } else {
                        alert(this.isSelectedDateFullyBlocked ? '{{ __('admin/reservation/edit.date_fully_blocked_message') }}' : '{{ __('admin/reservation/edit.js_select_date_time_alert') }}');
                    }
                },
                
                async loadBlockedDates() {
                    if (!this.selectedMenu) return;
                    
                    try {
                        const response = await fetch('{{ route('admin.reservation.availability') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                menu_id: this.selectedMenu,
                                start_date: `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-01`,
                                end_date: `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-31`,
                                exclude_reservation_id: this.originalReservationId
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (data.success && data.data) {
                            this.availabilityData = data.data;
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
    </script>
</x-layouts.app>