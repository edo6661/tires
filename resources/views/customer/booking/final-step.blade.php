<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 mb-6" x-data="finalStepHandler()">
            <div class="text-center py-8">
                <div x-show="loading || bookingSuccess" class="mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check text-2xl text-green-600"></i>
                    </div>
                    <h1 class="text-title-lg font-bold text-main-text mb-2">{{ __('final-step.success_title') }}</h1>
                    <p class="text-body-md text-main-text/70">{{ __('final-step.success_subtitle') }}</p>
                </div>

                <div x-show="loading" class="mb-6" x-transition>
                    <div class="flex justify-center items-center">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand"></div>
                        <span class="ml-3 text-main-text/70 text-body-md">{{ __('final-step.processing') }}</span>
                    </div>
                </div>

                <div x-show="!loading && bookingSuccess" class="space-y-6" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-receipt text-green-600 mr-2"></i>
                            <div>
                                <p class="text-body-md text-green-700">{{ __('final-step.reservation_number') }}</p>
                                <p class="text-heading-lg font-bold text-green-800" x-text="reservationNumber"></p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-sub rounded-lg p-6 text-left">
                        <h2 class="text-heading-lg font-semibold text-main-text mb-4 text-center">{{ __('final-step.details_title') }}</h2>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-3">
                                <h3 class="font-medium text-main-text border-b border-disabled pb-2 text-heading-md">{{ __('final-step.service_info_title') }}</h3>
                                <div class="space-y-2 text-body-md">
                                    <div class="flex justify-between p-1"><span class="text-main-text/70">{{ __('final-step.labels.service') }}</span><span class="font-medium text-main-text text-right" x-text="finalBookingData.serviceName"></span></div>
                                    <div class="flex justify-between p-1"><span class="text-main-text/70">{{ __('final-step.labels.date') }}</span><span class="font-medium text-main-text" x-text="formatBookingDate()"></span></div>
                                    <div class="flex justify-between p-1"><span class="text-main-text/70">{{ __('final-step.labels.time') }}</span><span class="font-medium text-main-text" x-text="finalBookingData.time"></span></div>
                                    <div class="flex justify-between p-1"><span class="text-main-text/70">{{ __('final-step.labels.duration') }}</span><span class="font-medium text-main-text" x-text="finalBookingData.duration + ' ' + translations.duration_unit"></span></div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <h3 class="font-medium text-main-text border-b border-disabled pb-2 text-heading-md">{{ __('final-step.customer_info_title') }}</h3>
                                <div class="space-y-2 text-body-md">
                                    <div class="flex justify-between p-1"><span class="text-main-text/70">{{ __('final-step.labels.name') }}</span><span class="font-medium text-main-text text-right" x-text="getCustomerName()"></span></div>
                                    <div class="flex justify-between p-1"><span class="text-main-text/70">{{ __('final-step.labels.email') }}</span><span class="font-medium text-main-text text-right" x-text="getCustomerEmail()"></span></div>
                                    <div class="flex justify-between p-1"><span class="text-main-text/70">{{ __('final-step.labels.phone') }}</span><span class="font-medium text-main-text" x-text="getCustomerPhone()"></span></div>
                                    <div class="flex justify-between p-1"><span class="text-main-text/70">{{ __('final-step.labels.status') }}</span><span class="px-2 py-1 bg-brand/10 text-brand text-body-md rounded-full">{{ __('final-step.booking_status_confirmed') }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-brand/5 border-l-4 border-brand p-4 text-left">
                        <div class="flex">
                            <div class="flex-shrink-0"><i class="fas fa-info-circle text-brand"></i></div>
                            <div class="ml-3">
                                <h3 class="text-heading-md font-medium text-brand">{{ __("final-step.whats_next_title") }}</h3>
                                <div class="mt-2 text-body-md text-main-text/80">
                                    <ul class="list-disc list-inside space-y-1">
                                        <li>{{ __("final-step.whats_next_items.email_sent") }}</li>
                                        <li>{{ __("final-step.whats_next_items.arrive_early") }}</li>
                                        <li>{{ __("final-step.whats_next_items.bring_id") }}</li>
                                        <li>{{ __("final-step.whats_next_items.contact_for_changes") }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center pt-6">
                        @auth
                        <a href="{{ route('customer.reservation.index') }}" class="px-6 py-2 bg-brand hover:bg-link-hover text-white rounded-lg font-medium transition-all duration-300 text-center">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ __("final-step.action_view_reservations") }}
                        </a>
                        @endauth
                        <a href="{{ route('home') }}" class="px-6 py-2 bg-main-button hover:bg-btn-main-hover text-white rounded-lg font-medium transition-all duration-300 text-center">
                            <i class="fas fa-home mr-2"></i>
                            {{ __("final-step.action_back_home") }}
                        </a>
                    </div>
                </div>

                <div x-show="!loading && !bookingSuccess" class="space-y-6" x-transition>
                     <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                         <div class="flex items-center justify-center text-red-700">
                             <i class="fas fa-exclamation-triangle mr-2"></i>
                             <div>
                                 <p class="font-medium text-heading-md">{{ __('final-step.failure_title') }}</p>
                                 <p class="text-body-md mt-1" x-text="errorMessage"></p>
                             </div>
                         </div>
                     </div>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button @click="retryBooking()" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
                            <i class="fas fa-redo mr-2"></i>
                            {{ __('final-step.failure_try_again') }}
                        </button>
                        <a href="{{ route('booking.third-step') }}" class="px-6 py-2 bg-secondary-button hover:bg-secondary-button/80 text-main-text rounded-lg font-medium text-center border border-disabled">
                            <i class="fas fa-arrow-left mr-2"></i>
                            {{ __('final-step.failure_go_back') }}
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
                translations: {
                    error_generic: @json(__('final-step.js.error_generic')),
                    date_locale: @json(__('final-step.js.date_locale')),
                    duration_unit: @json(__('final-step.duration_unit')),
                },

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
                            sessionStorage.clear(); // Hapus semua data sesi booking
                        } else {
                            throw new Error(result.message || this.translations.error_generic);
                        }
                    } catch (error) {
                        console.error('Booking failed:', error);
                        this.bookingSuccess = false;
                        this.errorMessage = error.message || this.translations.error_generic;
                    } finally {
                        this.loading = false;
                    }
                },
                
                generateReservationNumber() {
                    // Fallback jika API tidak mengembalikan nomor reservasi
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
                    return `RSV${year}${month}${day}${random}`;
                },

                formatBookingDate() {
                    if (!this.finalBookingData.date) return '';
                    const date = new Date(this.finalBookingData.date + 'T00:00:00');
                    return date.toLocaleDateString(this.translations.date_locale, {
                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                    });
                },

                getCustomerName() {
                    @auth return '{{ auth()->user()->full_name ?? '' }}'; @else return this.finalBookingData.guestInfo?.full_name || ''; @endauth
                },
                getCustomerEmail() {
                     @auth return '{{ auth()->user()->email ?? '' }}'; @else return this.finalBookingData.guestInfo?.email || ''; @endauth
                },
                getCustomerPhone() {
                     @auth return '{{ auth()->user()->phone_number ?? '' }}'; @else return this.finalBookingData.guestInfo?.phone_number || ''; @endauth
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
</x-layouts.app>