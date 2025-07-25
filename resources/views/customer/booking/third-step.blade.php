<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 mb-6 transform transition-all duration-300 hover:shadow-lg">
            <h1 class="text-title-lg font-bold text-main-text mb-2">
                @auth
                    Booking Confirmation
                @else
                    Contact Information & Booking Confirmation
                @endauth
            </h1>
            <p class="text-body-md text-main-text/70">
                @auth
                    Please review your reservation details before confirmation
                @else 
                    Please provide your contact information and review your reservation details
                @endauth
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg" x-data="thirdStepHandler()">
            @guest
                <div x-show="!showConfirmation" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="border-b border-disabled/50 pb-6">
                        <h2 class="text-heading-lg font-semibold text-main-text mb-4 transform transition-all duration-300 hover:text-brand">
                            <i class="fas fa-address-card text-brand mr-2 transform transition-all duration-300 hover:scale-110"></i>
                            Contact Information
                        </h2>
                        <form @submit.prevent="validateAndProceed()" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="transform transition-all duration-200 hover:scale-105">
                                    <label for="full_name" class="block text-body-md font-medium text-main-text mb-2">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="full_name" 
                                           x-model="guestInfo.full_name"
                                           class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md"
                                           placeholder="Enter your full name"
                                           required>
                                    <span x-show="errors.full_name" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.full_name"></span>
                                </div>
                                <div class="transform transition-all duration-200 hover:scale-105">
                                    <label for="full_name_kana" class="block text-body-md font-medium text-main-text mb-2">
                                        Full Name (Kana) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           id="full_name_kana" 
                                           x-model="guestInfo.full_name_kana"
                                           class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md"
                                           placeholder="Enter your full name in Kana"
                                           required>
                                    <span x-show="errors.full_name_kana" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.full_name_kana"></span>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="transform transition-all duration-200 hover:scale-105">
                                    <label for="email" class="block text-body-md font-medium text-main-text mb-2">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" 
                                           id="email" 
                                           x-model="guestInfo.email"
                                           class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md"
                                           placeholder="Enter your email address"
                                           required>
                                    <span x-show="errors.email" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.email"></span>
                                </div>
                                <div class="transform transition-all duration-200 hover:scale-105">
                                    <label for="phone_number" class="block text-body-md font-medium text-main-text mb-2">
                                        Phone Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" 
                                           id="phone_number" 
                                           x-model="guestInfo.phone_number"
                                           class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md"
                                           placeholder="Enter your phone number"
                                           required>
                                    <span x-show="errors.phone_number" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.phone_number"></span>
                                </div>
                            </div>
                            <div class="flex justify-between pt-4">
                                <button type="button" 
                                        @click="goBackToSecondStep()"
                                        class="px-6 py-2 text-main-text/70 hover:text-main-text transition-all duration-300 transform hover:scale-105 text-button-md">
                                    <i class="fas fa-arrow-left mr-2"></i>Back
                                </button>
                                <button type="submit" 
                                        class="px-8 py-2 bg-brand hover:bg-link-hover text-white rounded-lg font-medium transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg text-button-lg">
                                    Continue to Confirmation
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endguest
            <div x-show="@auth true @else showConfirmation @endauth" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="bg-sub rounded-lg p-6 transform transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                    <h2 class="text-heading-lg font-semibold text-main-text mb-4 flex items-center">
                        <i class="fas fa-clipboard-list text-brand mr-2 transform transition-all duration-300 hover:scale-110"></i>
                        Reservation Summary
                    </h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="space-y-3 transform transition-all duration-200 hover:scale-105">
                            <h3 class="font-medium text-main-text border-b border-disabled/50 pb-2 text-heading-md">Service Details</h3>
                            <div class="space-y-2 text-body-md">
                                <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                    <span class="text-main-text/70">Service:</span>
                                    <span class="font-medium text-main-text" x-text="bookingData.serviceName"></span>
                                </div>
                                <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                    <span class="text-main-text/70">Duration:</span>
                                    <span class="font-medium text-main-text" x-text="bookingData.duration + ' minutes'"></span>
                                </div>
                                <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                    <span class="text-main-text/70">Date:</span>
                                    <span class="font-medium text-main-text" x-text="formatBookingDate()"></span>
                                </div>
                                <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                    <span class="text-main-text/70">Time:</span>
                                    <span class="font-medium text-main-text" x-text="bookingData.time"></span>
                                </div>
                            </div>
                        </div>
                        <div class="space-y-3 transform transition-all duration-200 hover:scale-105">
                            <h3 class="font-medium text-main-text border-b border-disabled/50 pb-2 text-heading-md">Customer Information</h3>
                            <div class="space-y-2 text-body-md">
                                @auth
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Name:</span>
                                        <span class="font-medium text-main-text">{{ auth()->user()->full_name }}</span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Name (Kana):</span>
                                        <span class="font-medium text-main-text">{{ auth()->user()->full_name_kana }}</span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Email:</span>
                                        <span class="font-medium text-main-text">{{ auth()->user()->email }}</span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Phone:</span>
                                        <span class="font-medium text-main-text">{{ auth()->user()->phone_number }}</span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Member Status:</span>
                                        <span class="px-2 py-1 bg-brand/10 text-brand text-body-md rounded-full transform transition-all duration-300 hover:scale-105">RESERVA Member</span>
                                    </div>
                                @else
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Name:</span>
                                        <span class="font-medium text-main-text" x-text="guestInfo.full_name"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Name (Kana):</span>
                                        <span class="font-medium text-main-text" x-text="guestInfo.full_name_kana"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Email:</span>
                                        <span class="font-medium text-main-text" x-text="guestInfo.email"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Phone:</span>
                                        <span class="font-medium text-main-text" x-text="guestInfo.phone_number"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white rounded px-2 py-1">
                                        <span class="text-main-text/70">Member Status:</span>
                                        <span class="px-2 py-1 bg-disabled/20 text-main-text text-body-md rounded-full transform transition-all duration-300 hover:scale-105">Guest</span>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-main-button/10 border-l-4 border-main-button p-4 transform transition-all duration-300 hover:bg-main-button/20 hover:shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-main-button transform transition-all duration-300 hover:scale-110"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-body-md font-medium text-main-text">Important Notes</h3>
                            <div class="mt-2 text-body-md text-main-text/80">
                                <ul class="list-disc list-inside space-y-1">
                                    <li class="transition-all duration-200 hover:text-main-text">Please arrive 5 minutes before your scheduled time</li>
                                    <li class="transition-all duration-200 hover:text-main-text">Cancellation is not allowed after confirmation</li>
                                    <li class="transition-all duration-200 hover:text-main-text">Changes to reservation must be made at least 24 hours in advance</li>
                                    <li class="transition-all duration-200 hover:text-main-text">Please bring a valid ID for verification</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-disabled/50 pt-6">
                    <div class="flex items-start space-x-3 transform transition-all duration-200 hover:scale-105">
                        <input type="checkbox" 
                               id="agree_terms" 
                               x-model="agreedToTerms"
                               class="mt-1 h-4 w-4 text-brand border-disabled rounded focus:ring-brand transition-all duration-300">
                        <label for="agree_terms" class="text-body-md text-main-text cursor-pointer">
                            I agree to the <a href="#" class="text-link hover:text-link-hover underline transition-colors duration-300">Terms and Conditions</a> 
                            and <a href="#" class="text-link hover:text-link-hover underline transition-colors duration-300">Privacy Policy</a>
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <span x-show="errors.terms" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.terms"></span>
                </div>
                <div class="flex justify-between pt-6 border-t border-disabled/50">
                    <button type="button" 
                            @click="goBack()"
                            class="px-6 py-2 text-main-text/70 hover:text-main-text transition-all duration-300 transform hover:scale-105 text-button-md">
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
                            :class="agreedToTerms ? 'bg-brand hover:bg-link-hover shadow-md hover:shadow-lg' : 'bg-disabled cursor-not-allowed'"
                            class="px-8 py-2 text-white rounded-lg font-medium transition-all duration-300 transform hover:scale-105 text-button-lg">
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
                    if (!this.bookingData.menuId) {
                        console.error('Menu ID not found in session.');
                        this.bookingData.serviceName = 'Error: Service not found';
                        this.bookingData.duration = 'N/A';
                        return;
                    }
                    try {
                        const url = `{{ route('booking.menu-details', ['menuId' => '__MENU_ID__']) }}`.replace('__MENU_ID__', this.bookingData.menuId);
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Failed to fetch menu details');
                        const data = await response.json();
                        if (data.success) {
                            this.bookingData.serviceName = data.menu.name;
                            this.bookingData.duration = data.menu.required_time;
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        console.error('Error fetching menu info:', error);
                        this.bookingData.serviceName = 'Error Loading Service';
                        this.bookingData.duration = 'N/A';
                    }
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