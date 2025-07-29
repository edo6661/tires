<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 mb-6 transform transition-all duration-300 hover:shadow-lg">
            <h1 class="text-title-lg font-bold text-main-text mb-2">{{ __('second-step.title') }}</h1>
            <p class="text-body-md text-main-text/70">{{ __('second-step.subtitle') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg" x-data="secondStepHandler()">
            @auth
                <div class="text-center py-8">
                    <div class="mb-6 transform transition-all duration-500 ">
                        <i class="fas fa-user-check text-4xl text-brand mb-4 animate-pulse"></i>
                        <h2 class="text-title-md font-semibold text-main-text mb-2">{{ __('second-step.welcome_back', ['name' => auth()->user()->full_name]) }}</h2>
                        <p class="text-body-md text-main-text/70">{{ __('second-step.logged_in_as') }}</p>
                    </div>
                    <div class="bg-sub rounded-lg p-4 mb-6 transform transition-all duration-300 hover:bg-sub/80">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-info-circle text-brand mr-2"></i>
                            <p class="text-body-md text-brand">{{ __('second-step.member_info_used') }}</p>
                        </div>
                    </div>
                    <button @click="proceedToThirdStep()"
                            class="px-8 py-2 bg-brand hover:bg-link-hover text-white rounded-lg font-medium transition-all duration-300 w-full transform  shadow-md hover:shadow-lg text-button-lg">
                        {{ __('second-step.continue_to_confirmation') }}
                    </button>
                </div>
            @else
                <div class="space-y-8">
                    <div class="border-2 border-brand/30 rounded-lg p-6 bg-sub transform transition-all duration-300 hover:border-brand/50 hover:bg-sub/80">
                        <div class="text-center mb-6">
                            <i class="fas fa-users text-3xl text-brand mb-3 transform transition-all duration-300 "></i>
                            <h2 class="text-title-md font-semibold text-main-text mb-2">{{ __('second-step.members_title') }}</h2>
                            <p class="text-body-md text-main-text/70 mb-4">{{ __('second-step.members_subtitle') }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 mb-4 transform transition-all duration-200 hover:shadow-sm">
                            <h3 class="font-medium text-main-text mb-2 text-heading-md">{{ __('second-step.benefits_title') }}</h3>
                            <ul class="text-body-md text-main-text/70 space-y-1">
                                <li class="flex items-center transition-all duration-200 hover:text-main-text">
                                    <i class="fas fa-check text-brand mr-2"></i>
                                    {{ __('second-step.benefits.item1') }}
                                </li>
                                <li class="flex items-center transition-all duration-200 hover:text-main-text">
                                    <i class="fas fa-check text-brand mr-2"></i>
                                    {{ __('second-step.benefits.item2') }}
                                </li>
                                <li class="flex items-center transition-all duration-200 hover:text-main-text">
                                    <i class="fas fa-check text-brand mr-2"></i>
                                    {{ __('second-step.benefits.item3') }}
                                </li>
                            </ul>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('login') . '?redirect=' . urlencode(route('booking.second-step')) }}"
                               class="inline-block px-8 py-3 bg-brand hover:bg-link-hover text-white rounded-lg font-medium transition-all duration-300 transform  shadow-md hover:shadow-lg text-button-lg">
                                {{ __('second-step.login_button') }}
                            </a>
                        </div>
                    </div>
                    <div class="border-2 border-disabled/50 rounded-lg p-6 transform transition-all duration-300 hover:border-disabled hover:shadow-sm">
                        <div class="text-center mb-6">
                            <i class="fas fa-user-plus text-3xl text-main-text/70 mb-3 transform transition-all duration-300  hover:text-main-text"></i>
                            <h2 class="text-title-md font-semibold text-main-text mb-2">{{ __('second-step.non_members_title') }}</h2>
                            <p class="text-body-md text-main-text/70">{{ __('second-step.non_members_subtitle') }}</p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-disabled/20 rounded-lg p-4 transform transition-all duration-300 hover:bg-disabled/30 hover:shadow-sm">
                                <h3 class="font-medium text-main-text mb-2 text-heading-md">{{ __('second-step.guest_title') }}</h3>
                                <p class="text-body-md text-main-text/70 mb-4">{{ __('second-step.guest_subtitle') }}</p>
                                <button @click="proceedAsGuest()"
                                        class="w-full px-6 py-2 bg-secondary-button hover:bg-secondary-button/80 text-main-text rounded-lg font-medium transition-all duration-300 transform  shadow-sm hover:shadow-md text-button-md">
                                    {{ __('second-step.guest_button') }}
                                </button>
                            </div>
                            <div class="bg-main-button/10 rounded-lg p-4 transform transition-all duration-300 hover:bg-main-button/20 hover:shadow-sm">
                                <h3 class="font-medium text-main-text mb-2 text-heading-md">{{ __('second-step.register_title') }}</h3>
                                <p class="text-body-md text-main-text/70 mb-4">{{ __('second-step.register_subtitle') }}</p>
                                <a href="{{ route('register') . '?redirect=' . urlencode(route('booking.second-step')) }}"
                                   class="block w-full px-6 py-2 bg-main-button hover:bg-btn-main-hover text-white rounded-lg font-medium text-center transition-all duration-300 transform  shadow-md hover:shadow-lg text-button-md">
                                    {{ __('second-step.register_button') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-main-button/10 border-l-4 border-main-button p-4 transform transition-all duration-300 hover:bg-main-button/20">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-main-button transform transition-all duration-300 "></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-body-md text-main-text/80">
                                    <strong class="text-main-text">{{ __('second-step.final_note_title') }}</strong> {{ __('second-step.final_note_body') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
    <script>
        function secondStepHandler() {
            return {
                translations: {
                    alertNotFound: @json(__('second-step.js.booking_info_not_found')),
                },
                proceedToThirdStep() {
                    const bookingData = sessionStorage.getItem('bookingData');
                    if (!bookingData) {
                        alert(this.translations.alertNotFound);
                        window.location.href = '{{ route("home") }}';
                        return;
                    }
                    window.location.href = '{{ route("booking.third-step") }}';
                },
                proceedAsGuest() {
                    sessionStorage.setItem('bookingAsGuest', 'true');
                    window.location.href = '{{ route("booking.third-step") }}';
                }
            }
        }
    </script>
</x-layouts.app>