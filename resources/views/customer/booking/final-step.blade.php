<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 mb-6 transform transition-all duration-300 hover:shadow-lg" x-data="finalStepHandler()">
            <div class="text-center py-8">
                <div class="mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4 transform transition-all duration-500 hover:scale-110 animate-pulse">
                        <i class="fas fa-check text-2xl text-green-600 transform transition-all duration-300"></i>
                    </div>
                    <h1 class="text-title-lg font-bold text-main-text mb-2 transform transition-all duration-300 hover:text-brand">Booking Confirmed!</h1>
                    <p class="text-body-md text-main-text/70">Your reservation has been successfully submitted</p>
                </div>

                <div x-show="loading" class="mb-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                    <div class="flex justify-center items-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand"></div>
                        <span class="ml-3 text-main-text/70 text-body-md">Processing your reservation...</span>
                    </div>
                </div>

                <div x-show="!loading && bookingSuccess" class="space-y-6" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 transform transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-receipt text-green-600 mr-2 transform transition-all duration-300 hover:scale-110"></i>
                            <div>
                                <p class="text-body-md text-green-700">Reservation Number</p>
                                <p class="text-heading-lg font-bold text-green-800" x-text="reservationNumber"></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-sub rounded-lg p-6 text-left transform transition-all duration-300 hover:shadow-md">
                        <h2 class="text-heading-lg font-semibold text-main-text mb-4 text-center">Reservation Details</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-3 transform transition-all duration-300 hover:translate-x-1">
                                <h3 class="font-medium text-main-text border-b border-disabled pb-2 text-heading-md">Service Information</h3>
                                <div class="space-y-2 text-body-md">
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white/50 p-1 rounded">
                                        <span class="text-main-text/70">Service:</span>
                                        <span class="font-medium text-main-text" x-text="finalBookingData.serviceName"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white/50 p-1 rounded">
                                        <span class="text-main-text/70">Date:</span>
                                        <span class="font-medium text-main-text" x-text="formatBookingDate()"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white/50 p-1 rounded">
                                        <span class="text-main-text/70">Time:</span>
                                        <span class="font-medium text-main-text" x-text="finalBookingData.time"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white/50 p-1 rounded">
                                        <span class="text-main-text/70">Duration:</span>
                                        <span class="font-medium text-main-text" x-text="finalBookingData.duration + ' minutes'"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3 transform transition-all duration-300 hover:translate-x-1">
                                <h3 class="font-medium text-main-text border-b border-disabled pb-2 text-heading-md">Customer Information</h3>
                                <div class="space-y-2 text-body-md">
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white/50 p-1 rounded">
                                        <span class="text-main-text/70">Name:</span>
                                        <span class="font-medium text-main-text" x-text="getCustomerName()"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white/50 p-1 rounded">
                                        <span class="text-main-text/70">Email:</span>
                                        <span class="font-medium text-main-text" x-text="getCustomerEmail()"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white/50 p-1 rounded">
                                        <span class="text-main-text/70">Phone:</span>
                                        <span class="font-medium text-main-text" x-text="getCustomerPhone()"></span>
                                    </div>
                                    <div class="flex justify-between transition-all duration-200 hover:bg-white/50 p-1 rounded">
                                        <span class="text-main-text/70">Status:</span>
                                        <span class="px-2 py-1 bg-brand/10 text-brand text-body-md rounded-full transform transition-all duration-300 hover:scale-105">Confirmed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-brand/5 border-l-4 border-brand p-4 text-left transform transition-all duration-300 hover:bg-brand/10 hover:translate-x-1">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-brand transform transition-all duration-300 hover:scale-110"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-heading-md font-medium text-brand">What's Next?</h3>
                                <div class="mt-2 text-body-md text-main-text/80">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li class="transition-all duration-200 hover:text-main-text hover:translate-x-1">A confirmation email has been sent to your email address</li>
                                        <li class="transition-all duration-200 hover:text-main-text hover:translate-x-1">Please arrive 5 minutes before your scheduled time</li>
                                        <li class="transition-all duration-200 hover:text-main-text hover:translate-x-1">Bring a valid ID for verification</li>
                                        <li class="transition-all duration-200 hover:text-main-text hover:translate-x-1">Contact us if you need to make any changes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-disabled/20 rounded-lg p-4 text-left transform transition-all duration-300 hover:bg-disabled/30 hover:shadow-sm">
                        <h3 class="font-medium text-main-text mb-2 text-heading-md">Need Help?</h3>
                        <div class="text-body-md text-main-text/70 space-y-1">
                            <p class="transition-all duration-200 hover:text-main-text hover:translate-x-1">
                                <i class="fas fa-phone mr-2 text-brand"></i>Phone: +81-3-1234-5678
                            </p>
                            <p class="transition-all duration-200 hover:text-main-text hover:translate-x-1">
                                <i class="fas fa-envelope mr-2 text-brand"></i>Email: support@reserva.com
                            </p>
                            <p class="transition-all duration-200 hover:text-main-text hover:translate-x-1">
                                <i class="fas fa-clock mr-2 text-brand"></i>Business Hours: 9:00 AM - 6:00 PM (Mon-Fri)
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6">
                        
                        @auth
                        <a href="{{ route('customer.reservation.index') }}" 
                           class="px-6 py-2 bg-brand hover:bg-link-hover text-white rounded-lg font-medium transition-all duration-300 text-center transform hover:scale-105 hover:shadow-lg text-button-md">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            View My Reservations
                        </a>
                        @endauth
                        <a href="{{ route('home') }}" 
                           class="px-6 py-2 bg-main-button hover:bg-btn-main-hover text-white rounded-lg font-medium transition-all duration-300 text-center transform hover:scale-105 hover:shadow-lg text-button-md">
                            <i class="fas fa-home mr-2"></i>
                            Back to Home
                        </a>
                    </div>
                </div>

                <div x-show="!loading && !bookingSuccess" class="space-y-6" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    {{-- <div class="bg-red-50 border border-red-200 rounded-lg p-4 transform transition-all duration-300 hover:shadow-md">
                        <div class="flex items-center justify-center text-red-700">
                            <i class="fas fa-exclamation-triangle mr-2 transform transition-all duration-300 hover:scale-110"></i>
                            <div>
                                <p class="font-medium text-heading-md">Booking Failed</p>
                                <p class="text-body-md mt-1" x-text="errorMessage"></p>
                            </div>
                        </div>
                    </div> --}}
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button @click="retryBooking()" 
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all duration-300 transform hover:scale-105 hover:shadow-lg text-button-md">
                            <i class="fas fa-redo mr-2"></i>
                            Try Again
                        </button>
                        <a href="{{ route('booking.third-step') }}" 
                           class="px-6 py-2 bg-secondary-button hover:bg-secondary-button/80 text-main-text rounded-lg font-medium transition-all duration-300 text-center border border-disabled transform hover:scale-105 hover:shadow-md text-button-md">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Go Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function finalStepHandler() {
            return {
                loading: true,
                bookingSuccess: false,
                errorMessage: '',
                reservationNumber: '',
                finalBookingData: {},

                init() {
                    const storedData = sessionStorage.getItem('finalBookingData');
                    if (!storedData) {
                        this.loading = false;
                        window.location.href = '{{ route("home") }}';
                        return;
                    }
                    this.finalBookingData = JSON.parse(storedData);
                    this.processBooking();
                },

                async processBooking() {
                    try {
                        const bookingPayload = {
                            menu_id: this.finalBookingData.menuId,
                            reservation_datetime: this.finalBookingData.datetime,
                            number_of_people: 1, 
                            amount: 0, 
                            status: 'confirmed',
                            notes: 'Booking via website',
                            @guest
                            full_name: this.finalBookingData.guestInfo?.full_name || '',
                            full_name_kana: this.finalBookingData.guestInfo?.full_name_kana || '',
                            email: this.finalBookingData.guestInfo?.email || '',
                            phone_number: this.finalBookingData.guestInfo?.phone_number || '',
                            @else
                            user_id: {{ auth()->id() ?? 'null' }},
                            @endguest
                        };

                        const response = await fetch('{{ route("booking.create-reservation") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(bookingPayload)
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            this.bookingSuccess = true;
                            this.reservationNumber = result.reservation_number || this.generateReservationNumber();
                            sessionStorage.removeItem('bookingData');
                            sessionStorage.removeItem('guestInfo');
                            sessionStorage.removeItem('finalBookingData');
                        } else {
                            throw new Error(result.message || 'Failed to create booking');
                        }
                    } catch (error) {
                        console.error('Booking failed:', error);
                        this.bookingSuccess = false;
                        this.errorMessage = error.message || 'An error occurred while processing your booking. Please try again.';
                    } finally {
                        this.loading = false;
                    }
                },

                generateReservationNumber() {
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                    return `RSV${year}${month}${day}${random}`;
                },

                formatBookingDate() {
                    if (!this.finalBookingData.date) return '';
                    const date = new Date(this.finalBookingData.date);
                    return date.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                },

                getCustomerName() {
                    @auth
                    return '{{ auth()->user()->full_name ?? '' }}';
                    @else
                    return this.finalBookingData.guestInfo?.full_name || '';
                    @endauth
                },

                getCustomerEmail() {
                    @auth
                    return '{{ auth()->user()->email ?? '' }}';
                    @else
                    return this.finalBookingData.guestInfo?.email || '';
                    @endauth
                },

                getCustomerPhone() {
                    @auth
                    return '{{ auth()->user()->phone_number ?? '' }}';
                    @else
                    return this.finalBookingData.guestInfo?.phone_number || '';
                    @endauth
                },

                printConfirmation() {
                    window.print();
                },

                async retryBooking() {
                    this.loading = true;
                    this.bookingSuccess = false;
                    this.errorMessage = '';
                    await this.processBooking();
                }
            }
        }
    </script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
            }
            .bg-sub, .bg-green-50, .bg-brand\/5 {
                background: white !important;
                border: 1px solid #e5e7eb !important;
            }
        }
    </style>
</x-layouts.app>