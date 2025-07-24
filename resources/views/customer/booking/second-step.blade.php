<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h1 class="text-xl font-bold text-gray-900 mb-2">Booking Information</h1>
            <p class="text-sm text-gray-600">Please choose how you would like to proceed with your reservation</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" x-data="secondStepHandler()">
            @auth
                <div class="text-center py-8">
                    <div class="mb-6">
                        <i class="fas fa-user-check text-4xl text-green-500 mb-4"></i>
                        <h2 class="text-2xl font-semibold text-gray-900 mb-2">Welcome back, {{ auth()->user()->full_name }}!</h2>
                        <p class="text-gray-600">You are logged in as a RESERVA member</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-info-circle text-green-600 mr-2"></i>
                            <p class="text-sm text-green-700">Your member information will be used for this reservation</p>
                        </div>
                    </div>
                    <button @click="proceedToThirdStep()" 
                            class="px-8 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors w-full">
                        Continue to Confirmation
                    </button>
                </div>
            @else
                <div class="space-y-8">
                    <div class="border-2 border-blue-200 rounded-lg p-6 bg-blue-50">
                        <div class="text-center mb-6">
                            <i class="fas fa-users text-3xl text-blue-600 mb-3"></i>
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">RESERVA Members</h2>
                            <p class="text-gray-600 mb-4">Already have a RESERVA account? Login to continue</p>
                        </div>
                        <div class="bg-white rounded-lg p-4 mb-4">
                            <h3 class="font-medium text-gray-900 mb-2">Member Benefits:</h3>
                            <ul class="text-sm text-gray-600 space-y-1">
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Save time with pre-filled information
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    View your reservation history
                                </li>
                                <li class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    Easy reservation management and cancellation
                                </li>
                            </ul>
                        </div>
                        <div class="text-center">
                            <a href="{{ route('login') . '?redirect=' . urlencode(route('booking.second-step')) }}" 
                               class="inline-block px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                RESERVA Member Login
                            </a>
                        </div>
                    </div>
                    <div class="border-2 border-gray-200 rounded-lg p-6">
                        <div class="text-center mb-6">
                            <i class="fas fa-user-plus text-3xl text-gray-600 mb-3"></i>
                            <h2 class="text-xl font-semibold text-gray-900 mb-2">Non-RESERVA Members</h2>
                            <p class="text-gray-600">Continue as guest or create a new account</p>
                        </div>
                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="font-medium text-gray-900 mb-2">Continue as Guest</h3>
                                <p class="text-sm text-gray-600 mb-4">Enter your contact information directly for this reservation</p>
                                <button @click="proceedAsGuest()" 
                                        class="w-full px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors">
                                    Enter Contact Information
                                </button>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <h3 class="font-medium text-gray-900 mb-2">Create New Account</h3>
                                <p class="text-sm text-gray-600 mb-4">Register now to enjoy member benefits for future bookings</p>
                                <a href="{{ route('register') . '?redirect=' . urlencode(route('booking.second-step')) }}" 
                                   class="block w-full px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-center transition-colors">
                                    New Member Registration
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Note:</strong> By logging in as a member, you can easily save time by entering your details the next time you make a reservation. 
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