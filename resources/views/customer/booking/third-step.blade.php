<x-layouts.app>
    <div class="container">
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 mb-6 transform transition-all duration-300 hover:shadow-lg">
            <h1 class="text-title-lg font-bold text-main-text mb-2">
                @auth
                    {{ __('third-step.title_auth') }}
                @else
                    {{ __('third-step.title_guest') }}
                @endauth
            </h1>
            <p class="text-body-md text-main-text/70">
                @auth
                    {{ __('third-step.subtitle_auth') }}
                @else
                    {{ __('third-step.subtitle_guest') }}
                @endauth
            </p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg" x-data="thirdStepHandler()">
            @guest
                <div x-show="!showConfirmation" class="space-y-6" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                    <div class="border-b border-disabled/50 pb-6">
                        <h2 class="text-heading-lg font-semibold text-main-text mb-4 transform transition-all duration-300 hover:text-brand">
                            <i class="fas fa-address-card text-brand mr-2 transform transition-all duration-300 "></i>
                            {{ __('third-step.form_title') }}
                        </h2>
                        <form @submit.prevent="validateAndProceed()" class="space-y-4">
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label for="full_name" class="block text-body-md font-medium text-main-text mb-2">{{ __('third-step.labels.full_name') }} <span class="text-red-500">*</span></label>
                                    <input type="text" id="full_name" x-model="guestInfo.full_name" class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md" placeholder="{{ __('third-step.placeholders.full_name') }}" required>
                                    <span x-show="errors.full_name" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.full_name"></span>
                                </div>
                                <div>
                                    <label for="full_name_kana" class="block text-body-md font-medium text-main-text mb-2">{{ __('third-step.labels.full_name_kana') }} <span class="text-red-500">*</span></label>
                                    <input type="text" id="full_name_kana" x-model="guestInfo.full_name_kana" class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md" placeholder="{{ __('third-step.placeholders.full_name_kana') }}" required>
                                    <span x-show="errors.full_name_kana" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.full_name_kana"></span>
                                </div>
                            </div>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-body-md font-medium text-main-text mb-2">{{ __('third-step.labels.email') }} <span class="text-red-500">*</span></label>
                                    <input type="email" id="email" x-model="guestInfo.email" class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md" placeholder="{{ __('third-step.placeholders.email') }}" required>
                                    <span x-show="errors.email" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.email"></span>
                                </div>
                                <div>
                                    <label for="phone_number" class="block text-body-md font-medium text-main-text mb-2">{{ __('third-step.labels.phone_number') }} <span class="text-red-500">*</span></label>
                                    <input type="tel" id="phone_number" x-model="guestInfo.phone_number" class="w-full px-3 py-2 border border-disabled rounded-lg focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-300 hover:border-brand/50 text-body-md" placeholder="{{ __('third-step.placeholders.phone_number') }}" required>
                                    <span x-show="errors.phone_number" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.phone_number"></span>
                                </div>
                            </div>
                            <div class="flex justify-between pt-4">
                                <button type="button" @click="goBackToSecondStep()" class="px-6 py-2 text-main-text/70 hover:text-main-text transition-all duration-300 transform text-button-md">
                                    <i class="fas fa-arrow-left mr-2"></i>{{ __('third-step.form_button_back') }}
                                </button>
                                <button type="submit" class="px-8 py-2 bg-brand hover:bg-link-hover text-white rounded-lg font-medium transition-all duration-300 transform shadow-md hover:shadow-lg text-button-lg">
                                    {{ __('third-step.form_button_continue') }}
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
                        <i class="fas fa-clipboard-list text-brand mr-2"></i>
                        {{ __('third-step.summary_title') }}
                    </h2>
                    <div class="grid md:grid-cols-2 gap-x-6 gap-y-4">
                        <div class="space-y-3">
                            <h3 class="font-medium text-main-text border-b border-disabled/50 pb-2 text-heading-md">{{ __('third-step.service_details_title') }}</h3>
                            <div class="space-y-2 text-body-md">
                                <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.service') }}</span><span class="font-medium text-main-text text-right" x-text="bookingData.serviceName"></span></div>
                                <div class="flex justify-between hover:bg-white rounded px-2 py-1">
                                    <span class="text-main-text/70">{{ __('third-step.details.duration') }}</span>
                                    <span class="font-medium text-main-text" x-text="bookingData.duration + ' {{ __('third-step.duration_unit') }}'"></span>
                                </div>
                                <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.date') }}</span><span class="font-medium text-main-text" x-text="formatBookingDate()"></span></div>
                                <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.time') }}</span><span class="font-medium text-main-text" x-text="bookingData.time"></span></div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <h3 class="font-medium text-main-text border-b border-disabled/50 pb-2 text-heading-md">{{ __('third-step.customer_info_title') }}</h3>
                            <div class="space-y-2 text-body-md">
                                @auth
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.name') }}</span><span class="font-medium text-main-text">{{ auth()->user()->full_name }}</span></div>
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.name_kana') }}</span><span class="font-medium text-main-text">{{ auth()->user()->full_name_kana }}</span></div>
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.email') }}</span><span class="font-medium text-main-text">{{ auth()->user()->email }}</span></div>
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.phone') }}</span><span class="font-medium text-main-text">{{ auth()->user()->phone_number }}</span></div>
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.status') }}</span><span class="px-2 py-1 bg-brand/10 text-brand text-body-md rounded-full">{{ __('third-step.member_status.member') }}</span></div>
                                @else
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.name') }}</span><span class="font-medium text-main-text" x-text="guestInfo.full_name"></span></div>
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.name_kana') }}</span><span class="font-medium text-main-text" x-text="guestInfo.full_name_kana"></span></div>
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.email') }}</span><span class="font-medium text-main-text" x-text="guestInfo.email"></span></div>
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.phone') }}</span><span class="font-medium text-main-text" x-text="guestInfo.phone_number"></span></div>
                                    <div class="flex justify-between hover:bg-white rounded px-2 py-1"><span class="text-main-text/70">{{ __('third-step.details.status') }}</span><span class="px-2 py-1 bg-disabled/20 text-main-text text-body-md rounded-full">{{ __('third-step.member_status.guest') }}</span></div>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-main-button/10 border-l-4 border-main-button p-4">
                    <div class="flex">
                        <div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-main-button"></i></div>
                        <div class="ml-3">
                            <h3 class="text-body-md font-medium text-main-text">{{ __('third-step.important_notes_title') }}</h3>
                            <div class="mt-2 text-body-md text-main-text/80">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>{{ __('third-step.notes.item1') }}</li>
                                    <li>{{ __('third-step.notes.item2') }}</li>
                                    <li>{{ __('third-step.notes.item3') }}</li>
                                    <li>{{ __('third-step.notes.item4') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-disabled/50 pt-6">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="agree_terms" x-model="agreedToTerms" class="mt-1 h-4 w-4 text-brand border-disabled rounded focus:ring-brand">
                        <label for="agree_terms" class="text-body-md text-main-text cursor-pointer">
                            @if(app()->getLocale() == 'ja')
                                <a href="#" class="text-link hover:text-link-hover underline">{{ __('third-step.terms_and_conditions') }}</a>
                                {{ __('third-step.terms_and') }}
                                <a href="#" class="text-link hover:text-link-hover underline">{{ __('third-step.terms_privacy_policy') }}</a>
                                {{ __('third-step.terms_agree') }}
                            @else
                                {{ __('third-step.terms_agree') }}
                                <a href="#" class="text-link hover:text-link-hover underline">{{ __('third-step.terms_and_conditions') }}</a>
                                {{ __('third-step.terms_and') }}
                                <a href="#" class="text-link hover:text-link-hover underline">{{ __('third-step.terms_privacy_policy') }}</a>
                            @endif
                            <span class="text-red-500">*</span>
                        </label>
                    </div>
                    <span x-show="errors.terms" class="text-body-md text-red-500 mt-1 block animate-pulse" x-text="errors.terms"></span>
                </div>
                <div class="flex justify-between pt-6 border-t border-disabled/50">
                    <button type="button" @click="goBack()" class="px-6 py-2 text-main-text/70 hover:text-main-text transition-all duration-300 transform text-button-md">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span x-text="backButtonText"></span>
                    </button>
                    <button type="button" @click="completeBooking()" :disabled="!agreedToTerms || loading" :class="(agreedToTerms && !loading) ? 'bg-brand hover:bg-link-hover shadow-md hover:shadow-lg' : 'bg-disabled cursor-not-allowed'" class="px-8 py-2 text-white rounded-lg font-medium transition-all duration-300 transform text-button-lg">
                        <i class="fas fa-check mr-2"></i>
                        {{ __('third-step.action_complete_booking') }}
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
                guestInfo: { full_name: '', full_name_kana: '', email: '', phone_number: '' },
                @else
                showConfirmation: true,
                @endguest
                bookingData: {}, 
                agreedToTerms: false,
                errors: {},
                loading: false,
                translations: @json(__('third-step.js')),
                backButtonText: '{{ Auth::guest() ? __("third-step.form_button_back") : __("third-step.form_button_back") }}',
                init() {
                    const storedBookingData = sessionStorage.getItem('bookingData');
                    if (!storedBookingData) {
                        alert(this.translations.booking_info_not_found);
                        window.location.href = '{{ route("home") }}';
                        return;
                    }
                    this.bookingData = JSON.parse(storedBookingData);
                    this.bookingData.serviceName = this.translations.loading || '...';
                    this.bookingData.duration = '...';
                    this.loadMenuInfo();
                    @guest
                    this.$watch('showConfirmation', (value) => {
                        this.backButtonText = value ? '{{ __("third-step.action_back_guest_edit") }}' : '{{ __("third-step.form_button_back") }}';
                    });
                    @endguest
                },
                async loadMenuInfo() {
                    if (!this.bookingData.menuId) {
                        console.error('Menu ID not found in session.');
                        this.bookingData.serviceName = this.translations.error_service_not_found;
                        this.bookingData.duration = this.translations.not_applicable;
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
                        this.bookingData.serviceName = this.translations.error_loading_service;
                        this.bookingData.duration = this.translations.not_applicable;
                    }
                },
                @guest
                validateAndProceed() {
                    this.errors = {};
                    let isValid = true;
                    if (!this.guestInfo.full_name.trim()) { this.errors.full_name = this.translations.validation.full_name_required; isValid = false; }
                    if (!this.guestInfo.full_name_kana.trim()) { this.errors.full_name_kana = this.translations.validation.full_name_kana_required; isValid = false; }
                    if (!this.guestInfo.email.trim()) { this.errors.email = this.translations.validation.email_required; isValid = false; }
                    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.guestInfo.email)) { this.errors.email = this.translations.validation.email_invalid; isValid = false; }
                    if (!this.guestInfo.phone_number.trim()) { this.errors.phone_number = this.translations.validation.phone_required; isValid = false; }
                    if (isValid) {
                        sessionStorage.setItem('guestInfo', JSON.stringify(this.guestInfo));
                        this.showConfirmation = true;
                    }
                },
                @endguest
                formatBookingDate() {
                    if (!this.bookingData.date) return '';
                    const date = new Date(this.bookingData.date + 'T00:00:00');
                    return date.toLocaleDateString(this.translations.date_locale, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
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