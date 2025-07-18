<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-plus-circle mr-3 text-blue-600"></i>
                    Create New Reservation
                </h1>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.reservation.store') }}" x-data="reservationForm()">
                    @csrf
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
                                    <input type="radio" name="customer_type" value="existing" x-model="customerType" class="mr-2">
                                    <span>Registered Customer</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="customer_type" value="guest" x-model="customerType" class="mr-2">
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
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="customerType === 'guest'" class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="full_name_kana" class="block text-sm font-medium text-gray-700 mb-2">Full Name (Kana) *</label>
                                    <input type="text" name="full_name_kana" id="full_name_kana" value="{{ old('full_name_kana') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
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
                                            {{ old('menu_id') == $menu->id ? 'selected' : '' }}>
                                        {{ $menu->name }} ({{ number_format($menu->price, 0, ',', '.') }} yen - {{ $menu->required_time }} minutes)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="reservation_datetime" class="block text-sm font-medium text-gray-700 mb-2">Reservation Date & Time *</label>
                            <input type="datetime-local" name="reservation_datetime" id="reservation_datetime" 
                                   x-model="reservationDateTime" @change="checkAvailability()"
                                   value="{{ old('reservation_datetime') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="number_of_people" class="block text-sm font-medium text-gray-700 mb-2">Number of People *</label>
                            <input type="number" name="number_of_people" id="number_of_people" min="1" value="{{ old('number_of_people', 1) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Total Amount *</label>
                            <input type="number" name="amount" id="amount" step="0.01" value="{{ old('amount') }}"
                                   x-model="amount" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
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
                                :disabled="!isFormValid || availabilityStatus !== 'available'"
                                :class="(!isFormValid || availabilityStatus !== 'available') ? 'opacity-50 cursor-not-allowed' : ''"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            Save Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
       function reservationForm() {
            return {
                customerType: '{{ old('customer_type', 'existing') }}',
                selectedMenu: '{{ old('menu_id') }}',
                reservationDateTime: '{{ old('reservation_datetime') }}',
                amount: '{{ old('amount') }}',
                availabilityStatus: null,
                availabilityMessage: '',
                isFormValid: false,
                init() {
                    this.updateAmount();
                    this.validateForm();
                    // Watch for changes to trigger validation
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
                    // Set up event listeners for guest form fields
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
                        // User select for existing customer
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
                    // Debug log
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
                                reservation_datetime: this.reservationDateTime
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
                        // Validate form after checking availability
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
                }
            }
        }
        // Add CSRF token to head if not exists
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
        // Auto-update amount when menu changes
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