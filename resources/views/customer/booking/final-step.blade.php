<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6" x-data="finalStepHandler()">
            <div class="text-center py-8">
                <div class="mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check text-2xl text-green-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Booking Confirmed!</h1>
                    <p class="text-gray-600">Your reservation has been successfully submitted</p>
                </div>
                <div x-show="loading" class="mb-6">
                    <div class="flex justify-center items-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        <span class="ml-3 text-gray-600">Processing your reservation...</span>
                    </div>
                </div>
                <div x-show="!loading && bookingSuccess" class="space-y-6">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-receipt text-green-600 mr-2"></i>
                            <div>
                                <p class="text-sm text-green-700">Reservation Number</p>
                                <p class="text-lg font-bold text-green-800" x-text="reservationNumber"></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-6 text-left">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 text-center">Reservation Details</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h3 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Service Information</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Service:</span>
                                        <span class="font-medium" x-text="finalBookingData.serviceName"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Date:</span>
                                        <span class="font-medium" x-text="formatBookingDate()"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Time:</span>
                                        <span class="font-medium" x-text="finalBookingData.time"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Duration:</span>
                                        <span class="font-medium" x-text="finalBookingData.duration + ' minutes'"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <h3 class="font-medium text-gray-900 border-b border-gray-200 pb-2">Customer Information</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Name:</span>
                                        <span class="font-medium" x-text="getCustomerName()"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Email:</span>
                                        <span class="font-medium" x-text="getCustomerEmail()"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="font-medium" x-text="getCustomerPhone()"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Confirmed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 text-left">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">What's Next?</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>A confirmation email has been sent to your email address</li>
                                        <li>Please arrive 5 minutes before your scheduled time</li>
                                        <li>Bring a valid ID for verification</li>
                                        <li>Contact us if you need to make any changes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-4 text-left">
                        <h3 class="font-medium text-gray-900 mb-2">Need Help?</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><i class="fas fa-phone mr-2"></i>Phone: +81-3-1234-5678</p>
                            <p><i class="fas fa-envelope mr-2"></i>Email: support@reserva.com</p>
                            <p><i class="fas fa-clock mr-2"></i>Business Hours: 9:00 AM - 6:00 PM (Mon-Fri)</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6">
                        <button @click="printConfirmation()" 
                                class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors border border-gray-300">
                            <i class="fas fa-print mr-2"></i>
                            Print Confirmation
                        </button>
                        @auth
                        <a href="{{ route('customer.reservation.index') }}" 
                           class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors text-center">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            View My Reservations
                        </a>
                        @endauth
                        <a href="{{ route('home') }}" 
                           class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors text-center">
                            <i class="fas fa-home mr-2"></i>
                            Back to Home
                        </a>
                    </div>
                </div>
                <div x-show="!loading && !bookingSuccess" class="space-y-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center justify-center text-red-700">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <div>
                                <p class="font-medium">Booking Failed</p>
                                <p class="text-sm mt-1" x-text="errorMessage"></p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button @click="retryBooking()" 
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-redo mr-2"></i>
                            Try Again
                        </button>
                        <a href="{{ route('booking.third-step') }}" 
                           class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors text-center border border-gray-300">
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
                        this.errorMessage = 'Booking data not found. Please start over.';
                        setTimeout(() => {
                            window.location.href = '{{ route("home") }}';
                        }, 3000);
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
            .bg-gray-50, .bg-green-50, .bg-blue-50 {
                background: white !important;
                border: 1px solid #e5e7eb !important;
            }
        }
    </style>
</x-layouts.app>