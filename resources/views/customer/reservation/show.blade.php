<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-6">
                    <a href="{{ route('customer.reservation.index') }}" 
                       class="inline-flex items-center gap-2 text-gray-600 hover:text-blue-600 transition-colors duration-200 font-medium group">
                        <div class="w-8 h-8 rounded-full bg-gray-100 group-hover:bg-blue-100 flex items-center justify-center transition-colors duration-200">
                            <i class="fas fa-arrow-left text-sm"></i>
                        </div>
                        Back to Reservations
                    </a>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-3">Reservation Details</h1>
                            <div class="flex items-center gap-4">
                                <span class="px-4 py-2 bg-gray-100 rounded-full text-sm font-medium text-gray-700">
                                    #{{ $reservation->reservation_number }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Created {{ $reservation->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-6 py-3 rounded-full text-sm font-bold shadow-lg
                                @if($reservation->status->value === 'pending')
                                    bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 border border-yellow-300
                                @elseif($reservation->status->value === 'confirmed')
                                    bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300
                                @elseif($reservation->status->value === 'completed')
                                    bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300
                                @else
                                    bg-gradient-to-r from-red-100 to-red-200 text-red-800 border border-red-300
                                @endif
                            ">
                                {{ $reservation->status->label() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1">
                <!-- Main Content -->
                <div class="xl:col-span-3 space-y-8">
                    <!-- Service Information -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6">
                            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-concierge-bell"></i>
                                Service Information
                            </h2>
                        </div>
                        <div class="p-8">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Service Name</label>
                                    <p class="text-xl font-bold text-gray-900">{{ $reservation->menu->name }}</p>
                                </div>
                                
                                @if($reservation->menu->description)
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="text-gray-700 leading-relaxed">{{ $reservation->menu->description }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-blue-50 rounded-lg p-4">
                                        <label class="block text-sm font-semibold text-blue-700 mb-1">Duration</label>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-clock text-blue-600"></i>
                                            <p class="text-lg font-bold text-blue-900">{{ $reservation->menu->required_time }} minutes</p>
                                        </div>
                                    </div>
                                    
                                    @if($reservation->amount)
                                        <div class="bg-green-50 rounded-lg p-4">
                                            <label class="block text-sm font-semibold text-green-700 mb-1">Price</label>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-yen-sign text-green-600"></i>
                                                <p class="text-lg font-bold text-green-900">{{ number_format($reservation->amount) }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reservation Details -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-600 to-teal-600 p-6">
                            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-calendar-check"></i>
                                Reservation Details
                            </h2>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="bg-blue-50 rounded-xl p-6 text-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                                    </div>
                                    <label class="block text-sm font-semibold text-blue-700 mb-1">Date</label>
                                    <p class="text-lg font-bold text-blue-900">{{ $reservation->reservation_datetime->format('M d, Y') }}</p>
                                    <p class="text-xs text-blue-600 mt-1">{{ $reservation->reservation_datetime->format('l') }}</p>
                                </div>
                                
                                <div class="bg-green-50 rounded-xl p-6 text-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-clock text-green-600 text-xl"></i>
                                    </div>
                                    <label class="block text-sm font-semibold text-green-700 mb-1">Time</label>
                                    <p class="text-lg font-bold text-green-900">{{ $reservation->reservation_datetime->format('H:i') }}</p>
                                    <p class="text-xs text-green-600 mt-1">{{ $reservation->reservation_datetime->format('A') }}</p>
                                </div>
                                
                                <div class="bg-purple-50 rounded-xl p-6 text-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-users text-purple-600 text-xl"></i>
                                    </div>
                                    <label class="block text-sm font-semibold text-purple-700 mb-1">Party Size</label>
                                    <p class="text-lg font-bold text-purple-900">{{ $reservation->number_of_people }}</p>
                                    <p class="text-xs text-purple-600 mt-1">{{ $reservation->number_of_people == 1 ? 'person' : 'people' }}</p>
                                </div>
                            </div>

                            @if($reservation->notes)
                                <div class="bg-amber-50 border-l-4 border-amber-400 p-6 rounded-r-xl">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-sticky-note text-amber-600"></i>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-amber-800 mb-2">Special Notes</label>
                                            <p class="text-amber-700 leading-relaxed">{{ $reservation->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6">
                            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                <i class="fas fa-user-circle"></i>
                                Customer Information
                            </h2>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="font-semibold text-gray-900">{{ $reservation->getFullName() }}</p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kana Name</label>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <p class="font-semibold text-gray-900">{{ $reservation->getFullNameKana() }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-envelope text-gray-400"></i>
                                                <p class="font-semibold text-gray-900">{{ $reservation->getEmail() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-phone text-gray-400"></i>
                                                <p class="font-semibold text-gray-900">{{ $reservation->getPhoneNumber() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($reservation->questionnaire)
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-6">
                                <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                    <i class="fas fa-clipboard-list"></i>
                                    Questionnaire
                                </h2>
                            </div>
                            <div class="p-8">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-green-800">Questionnaire Completed</p>
                                            <p class="text-sm text-green-600">All required information has been provided.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>