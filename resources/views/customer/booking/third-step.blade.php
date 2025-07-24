<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h1 class="text-xl font-bold text-gray-900 mb-2">
                @auth
                    Booking Confirmation
                @else
                    Contact Information & Booking Confirmation
                @endauth
            </h1>
            <p class="text-sm text-gray-600">
                @auth
                    Please review your reservation details before confirmation
                @else 
                    Please provide your contact information and review your reservation details
                @endauth
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="thirdStepHandler()">
            @guest
                <!-- Guest Form Section -->
                <div x-show="!showConfirmation" class="space-y-6">
                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
                        <form @submit.prevent="validateAndProceed()" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="full_name" 
                                           x-model="guestInfo.full_name"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Enter your full name"
                                           required>
                                    <span x-show="errors.full_name" class="text-sm text-red-600" x-text="errors.full_name"></span>
                                </div>
                                <div>
                                    <label for="full_name_kana" class="block text-sm font-medium text-gray-700 mb-2">
                                        Full Name (Kana) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="full_name_kana" 
                                           x-model="guestInfo.full_name_kana"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Enter your full name in Kana"
                                           required>
                                    <span x-show="errors.full_name_kana" class="text-sm text-red-600" x-text="errors.full_name_kana"></span>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           id="email" 
                                           x-model="guestInfo.email"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Enter your email address"
                                           required>
                                    <span x-show="errors.email" class="text-sm text-red-600" x-text="errors.email"></span>
                                </div>
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" 
                                           id="phone_number" 
                                           x-model="guestInfo.phone_number"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="Enter your phone number"
                                           required>
                                    <span x-show="errors.phone_number" class="text-sm text-red-600" x-text="errors.phone_number"></span>
                                </div>
                            </div>
                            <div class="flex justify-between pt-4">
                                <button type="button" 
                                        @click="goBackToSecondStep()"
                                        class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                                    <i class="fas fa-arrow-left mr-2"></i>Back
                                </button>
                                <button type="submit" 
                                        class="px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    Continue to Confirmation
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endguest
            <!-- Confirmation Section -->
            <div x-show="@auth true @else showConfirmation @endauth" class="space-y-6">
                <!-- Booking Summary -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Reservation Summary</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Service Details -->
                        <div class="space-y-3">
                            <h3 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Service Details</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Service:</span>
                                    <span class="font-medium" x-text="bookingData.serviceName"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Duration:</span>
                                    <span class="font-medium" x-text="bookingData.duration + ' minutes'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium" x-text="formatBookingDate()"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time:</span>
                                    <span class="font-medium" x-text="bookingData.time"></span>
                                </div>
                            </div>
                        </div>
                        <!-- Customer Details -->
                        <div class="space-y-3">
                            <h3 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Customer Information</h3>
                            <div class="space-y-2 text-sm">
                                @auth
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Name:</span>
                                        <span class="font-medium">{{ auth()->user()->full_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Name (Kana):</span>
                                        <span class="font-medium">{{ auth()->user()->full_name_kana }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="font-medium">{{ auth()->user()->email }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="font-medium">{{ auth()->user()->phone_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Member Status:</span>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">RESERVA Member</span>
                                    </div>
                                @else
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Name:</span>
                                        <span class="font-medium" x-text="guestInfo.full_name"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Name (Kana):</span>
                                        <span class="font-medium" x-text="guestInfo.full_name_kana"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="font-medium" x-text="guestInfo.email"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="font-medium" x-text="guestInfo.phone_number"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Member Status:</span>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">Guest</span>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Important Notes -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Important Notes</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Please arrive 5 minutes before your scheduled time</li>
                                    <li>Cancellation is not allowed after confirmation</li>
                                    <li>Changes to reservation must be made at least 24 hours in advance</li>
                                    <li>Please bring a valid ID for verification</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Terms and Conditions -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" 
                               id="agree_terms" 
                               x-model="agreedToTerms"
                               class="mt-1 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="agree_terms" class="text-sm text-gray-700 cursor-pointer">
                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-800 underline">Terms and Conditions</a> 
                            and <a href="#" class="text-blue-600 hover:text-blue-800 underline">Privacy Policy</a>
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <span x-show="errors.terms" class="text-sm text-red-600 mt-1 block" x-text="errors.terms"></span>
                </div>
                <!-- Action Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <button type="button" 
                            @click="goBack()"
                            class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        @guest
                            <span x-text="showConfirmation ? 'Edit Information' : 'Back'"></span>
                        @else
                            Back
                        @endguest
                    </button>
                    <button type="button" 
                            @click="completeBooking()"
                            :disabled="!agreedToTerms"
                            :class="agreedToTerms ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                            class="px-8 py-2 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-check mr-2"></i>
                        Complete Booking
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function thirdStepHandler() {
            return {
                @guest
                showConfirmation: false,
                guestInfo: {
                    full_name: '',
                    full_name_kana: '',
                    email: '',
                    phone_number: ''
                },
                @else
                showConfirmation: true,
                @endguest
                bookingData: {},
                agreedToTerms: false,
                errors: {},
                loading: false,
                init() {
                    const storedBookingData = sessionStorage.getItem('bookingData');
                    if (!storedBookingData) {
                        alert('Booking information not found. Please start over.');
                        window.location.href = '{{ route("home") }}';
                        return;
                    }
                    this.bookingData = JSON.parse(storedBookingData);
                    this.loadMenuInfo();
                },
                async loadMenuInfo() {
                    this.bookingData.serviceName = 'Sample Service'; 
                    this.bookingData.duration = '60'; 
                },
                @guest
                validateAndProceed() {
                    this.errors = {};
                    let isValid = true;
                    if (!this.guestInfo.full_name.trim()) {
                        this.errors.full_name = 'Full name is required';
                        isValid = false;
                    }
                    if (!this.guestInfo.full_name_kana.trim()) {
                        this.errors.full_name_kana = 'Full name (Kana) is required';
                        isValid = false;
                    }
                    if (!this.guestInfo.email.trim()) {
                        this.errors.email = 'Email is required';
                        isValid = false;
                    } else if (!this.isValidEmail(this.guestInfo.email)) {
                        this.errors.email = 'Please enter a valid email address';
                        isValid = false;
                    }
                    if (!this.guestInfo.phone_number.trim()) {
                        this.errors.phone_number = 'Phone number is required';
                        isValid = false;
                    }
                    if (isValid) {
                        sessionStorage.setItem('guestInfo', JSON.stringify(this.guestInfo));
                        this.showConfirmation = true;
                    }
                },
                isValidEmail(email) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return emailRegex.test(email);
                },
                @endguest
                formatBookingDate() {
                    if (!this.bookingData.date) return '';
                    const date = new Date(this.bookingData.date);
                    return date.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                },
                goBack() {
                    @guest
                    if (this.showConfirmation) {
                        this.showConfirmation = false;
                    } else {
                        this.goBackToSecondStep();
                    }
                    @else
                    this.goBackToSecondStep();
                    @endguest
                },
                goBackToSecondStep() {
                    window.location.href = '{{ route("booking.second-step") }}';
                },
                completeBooking() {
                    this.errors = {};
                    if (!this.agreedToTerms) {
                        this.errors.terms = 'You must agree to the terms and conditions';
                        return;
                    }
                    if (this.loading) return;
                    this.loading = true;
                    const finalBookingData = {
                        ...this.bookingData,
                        @guest
                        guestInfo: this.guestInfo,
                        @endauth
                        agreedToTerms: this.agreedToTerms
                    };
                    sessionStorage.setItem('finalBookingData', JSON.stringify(finalBookingData));
                    window.location.href = '{{ route("booking.final-step") }}';
                }
            }
        }
    </script>
</x-layouts.app>