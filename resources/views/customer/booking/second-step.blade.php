<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 mb-6 transform transition-all duration-300 hover:shadow-lg">
            <h1 class="text-title-lg font-bold text-main-text mb-2">Booking Information</h1>
            <p class="text-body-md text-main-text/70">Please choose how you would like to proceed with your reservation</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-disabled/50 p-6 transform transition-all duration-300 hover:shadow-lg" x-data="secondStepHandler()">
            @auth
                <div class="text-center py-8">
                    <div class="mb-6 transform transition-all duration-500 ">
                        <i class="fas fa-user-check text-4xl text-brand mb-4 animate-pulse"></i>
                        <h2 class="text-title-md font-semibold text-main-text mb-2">Welcome back, {{ auth()->user()->full_name }}!</h2>
                        <p class="text-body-md text-main-text/70">You are logged in as a RESERVA member</p>
                    </div>
                    <div class="bg-sub rounded-lg p-4 mb-6 transform transition-all duration-300 hover:bg-sub/80">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-info-circle text-brand mr-2"></i>
                            <p class="text-body-md text-brand">Your member information will be used for this reservation</p>
                        </div>
                    </div>
                    <button @click="proceedToThirdStep()" 
                            class="px-8 py-2 bg-brand hover:bg-link-hover text-white rounded-lg font-medium transition-all duration-300 w-full transform  shadow-md hover:shadow-lg text-button-lg">
                        Continue to Confirmation
                    </button>
                </div>
            @else
                <div class="space-y-8">
                    <div class="border-2 border-brand/30 rounded-lg p-6 bg-sub transform transition-all duration-300 hover:border-brand/50 hover:bg-sub/80">
                        <div class="text-center mb-6">
                            <i class="fas fa-users text-3xl text-brand mb-3 transform transition-all duration-300 "></i>
                            <h2 class="text-title-md font-semibold text-main-text mb-2">RESERVA Members</h2>
                            <p class="text-body-md text-main-text/70 mb-4">Already have a RESERVA account? Login to continue</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 mb-4 transform transition-all duration-200 hover:shadow-sm">
                            <h3 class="font-medium text-main-text mb-2 text-heading-md">Member Benefits:</h3>
                            <ul class="text-body-md text-main-text/70 space-y-1">
                                <li class="flex items-center transition-all duration-200 hover:text-main-text">
                                    <i class="fas fa-check text-brand mr-2"></i>
                                    Save time with pre-filled information
                                </li>
                                <li class="flex items-center transition-all duration-200 hover:text-main-text">
                                    <i class="fas fa-check text-brand mr-2"></i>
                                    View your reservation history
                                </li>
                                <li class="flex items-center transition-all duration-200 hover:text-main-text">
                                    <i class="fas fa-check text-brand mr-2"></i>
                                    Easy reservation management and cancellation
                                </li>
                            </ul>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('login') . '?redirect=' . urlencode(route('booking.second-step')) }}" 
                               class="inline-block px-8 py-3 bg-brand hover:bg-link-hover text-white rounded-lg font-medium transition-all duration-300 transform  shadow-md hover:shadow-lg text-button-lg">
                                RESERVA Member Login
                            </a>
                        </div>
                    </div>
                    <div class="border-2 border-disabled/50 rounded-lg p-6 transform transition-all duration-300 hover:border-disabled hover:shadow-sm">
                        <div class="text-center mb-6">
                            <i class="fas fa-user-plus text-3xl text-main-text/70 mb-3 transform transition-all duration-300  hover:text-main-text"></i>
                            <h2 class="text-title-md font-semibold text-main-text mb-2">Non-RESERVA Members</h2>
                            <p class="text-body-md text-main-text/70">Continue as guest or create a new account</p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-disabled/20 rounded-lg p-4 transform transition-all duration-300 hover:bg-disabled/30 hover:shadow-sm">
                                <h3 class="font-medium text-main-text mb-2 text-heading-md">Continue as Guest</h3>
                                <p class="text-body-md text-main-text/70 mb-4">Enter your contact information directly for this reservation</p>
                                <button @click="proceedAsGuest()" 
                                        class="w-full px-6 py-2 bg-secondary-button hover:bg-secondary-button/80 text-main-text rounded-lg font-medium transition-all duration-300 transform  shadow-sm hover:shadow-md text-button-md">
                                    Enter Contact Information
                                </button>
                            </div>
                            <div class="bg-main-button/10 rounded-lg p-4 transform transition-all duration-300 hover:bg-main-button/20 hover:shadow-sm">
                                <h3 class="font-medium text-main-text mb-2 text-heading-md">Create New Account</h3>
                                <p class="text-body-md text-main-text/70 mb-4">Register now to enjoy member benefits for future bookings</p>
                                <a href="{{ route('register') . '?redirect=' . urlencode(route('booking.second-step')) }}" 
                                   class="block w-full px-6 py-2 bg-main-button hover:bg-btn-main-hover text-white rounded-lg font-medium text-center transition-all duration-300 transform  shadow-md hover:shadow-lg text-button-md">
                                    New Member Registration
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
                                    <strong class="text-main-text">Note:</strong> By logging in as a member, you can easily save time by entering your details the next time you make a reservation. 
                                    You can also check your reservation history and cancel your reservation.
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
                proceedToThirdStep() {
                    const bookingData = sessionStorage.getItem('bookingData');
                    if (!bookingData) {
                        alert('Booking information not found. Please start over.');
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