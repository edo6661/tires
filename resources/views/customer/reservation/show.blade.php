<x-layouts.app>
    <div class="bg-gradient-to-br from-gray-50 to-sub py-8">
        <div class="container px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-6">
                    <a href="{{ route('customer.reservation.index') }}" 
                       class="inline-flex items-center gap-2 text-main-text hover:text-link transition-colors duration-200 font-medium group">
                        <div class="w-8 h-8 rounded-full bg-sub group-hover:bg-brand group-hover:text-white flex items-center justify-center transition-all duration-300 transform group-">
                            <i class="fas fa-arrow-left text-sm"></i>
                        </div>
                        Back to Reservations
                    </a>
                </div>
                
                <div class="bg-white rounded-2xl shadow-lg border border-disabled/50 p-8 transform hover:shadow-xl transition-all duration-300">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                        <div>
                            <h1 class="text-title-lg font-bold text-main-text mb-3">Reservation Details</h1>
                            <div class="flex items-center gap-4">
                                <span class="px-4 py-2 bg-sub rounded-full text-body-md font-medium text-brand">
                                    #{{ $reservation->reservation_number }}
                                </span>
                                <span class="text-body-md text-main-text/60">
                                    Created {{ $reservation->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-6 py-3 rounded-full text-button-md font-bold shadow-lg transform  transition-all duration-300
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
                <div class="xl:col-span-3 space-y-8">
                    <div class="bg-white rounded-2xl shadow-lg border border-disabled/50 overflow-hidden transform hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="bg-gradient-to-r from-brand to-link p-6">
                            <h2 class="text-heading-lg font-bold text-white flex items-center gap-3">
                                <i class="fas fa-concierge-bell"></i>
                                Service Information
                            </h2>
                        </div>
                        <div class="p-8">
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-body-md font-semibold text-brand mb-2">Service Name</label>
                                    <p class="text-title-md font-bold text-main-text">{{ $reservation->menu->name }}</p>
                                </div>
                                
                                @if($reservation->menu->description)
                                    <div>
                                        <label class="block text-body-md font-semibold text-brand mb-2">Description</label>
                                        <div class="bg-sub rounded-lg p-4 transform hover:scale-[1.02] transition-transform duration-300">
                                            <p class="text-main-text leading-relaxed text-body-md">{{ $reservation->menu->description }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-sub rounded-lg p-4 transform  transition-all duration-300 hover:shadow-md">
                                        <label class="block text-body-md font-semibold text-brand mb-1">Duration</label>
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-clock text-link"></i>
                                            <p class="text-heading-md font-bold text-main-text">{{ $reservation->menu->required_time }} minutes</p>
                                        </div>
                                    </div>
                                    
                                    @if($reservation->amount)
                                        <div class="bg-sub rounded-lg p-4 transform  transition-all duration-300 hover:shadow-md">
                                            <label class="block text-body-md font-semibold text-brand mb-1">Price</label>
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-yen-sign text-link"></i>
                                                <p class="text-heading-md font-bold text-main-text">{{ number_format($reservation->amount) }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg border border-disabled/50 overflow-hidden transform hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="bg-gradient-to-r from-main-button to-btn-main-hover p-6">
                            <h2 class="text-heading-lg font-bold text-white flex items-center gap-3">
                                <i class="fas fa-calendar-check"></i>
                                Reservation Details
                            </h2>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                <div class="bg-sub rounded-xl p-6 text-center transform  transition-all duration-300 hover:shadow-lg">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-md">
                                        <i class="fas fa-calendar-alt text-brand text-xl"></i>
                                    </div>
                                    <label class="block text-body-md font-semibold text-brand mb-1">Date</label>
                                    <p class="text-heading-md font-bold text-main-text">{{ $reservation->reservation_datetime->format('M d, Y') }}</p>
                                    <p class="text-body-md text-main-text/60 mt-1">{{ $reservation->reservation_datetime->format('l') }}</p>
                                </div>
                                
                                <div class="bg-sub rounded-xl p-6 text-center transform  transition-all duration-300 hover:shadow-lg">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-md">
                                        <i class="fas fa-clock text-brand text-xl"></i>
                                    </div>
                                    <label class="block text-body-md font-semibold text-brand mb-1">Time</label>
                                    <p class="text-heading-md font-bold text-main-text">{{ $reservation->reservation_datetime->format('H:i') }}</p>
                                    <p class="text-body-md text-main-text/60 mt-1">{{ $reservation->reservation_datetime->format('A') }}</p>
                                </div>
                                
                                <div class="bg-sub rounded-xl p-6 text-center transform  transition-all duration-300 hover:shadow-lg">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-3 shadow-md">
                                        <i class="fas fa-users text-brand text-xl"></i>
                                    </div>
                                    <label class="block text-body-md font-semibold text-brand mb-1">Party Size</label>
                                    <p class="text-heading-md font-bold text-main-text">{{ $reservation->number_of_people }}</p>
                                    <p class="text-body-md text-main-text/60 mt-1">{{ $reservation->number_of_people == 1 ? 'person' : 'people' }}</p>
                                </div>
                            </div>

                            @if($reservation->notes)
                                <div class="bg-main-button/10 border-l-4 border-main-button p-6 rounded-r-xl transform hover:scale-[1.02] transition-transform duration-300">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 w-10 h-10 bg-main-button/20 rounded-full flex items-center justify-center">
                                            <i class="fas fa-sticky-note text-main-button"></i>
                                        </div>
                                        <div>
                                            <label class="block text-body-md font-semibold text-brand mb-2">Special Notes</label>
                                            <p class="text-main-text leading-relaxed text-body-md">{{ $reservation->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg border border-disabled/50 overflow-hidden transform hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="bg-gradient-to-r from-link to-link-hover p-6">
                            <h2 class="text-heading-lg font-bold text-white flex items-center gap-3">
                                <i class="fas fa-user-circle"></i>
                                Customer Information
                            </h2>
                        </div>
                        <div class="p-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-body-md font-semibold text-brand mb-2">Full Name</label>
                                        <div class="bg-sub rounded-lg p-4 transform hover:scale-[1.02] transition-transform duration-300">
                                            <p class="font-semibold text-main-text text-body-lg">{{ $reservation->getFullName() }}</p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-body-md font-semibold text-brand mb-2">Kana Name</label>
                                        <div class="bg-sub rounded-lg p-4 transform hover:scale-[1.02] transition-transform duration-300">
                                            <p class="font-semibold text-main-text text-body-lg">{{ $reservation->getFullNameKana() }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-6">
                                    <div>
                                        <label class="block text-body-md font-semibold text-brand mb-2">Email Address</label>
                                        <div class="bg-sub rounded-lg p-4 transform hover:scale-[1.02] transition-transform duration-300">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-envelope text-main-text/60"></i>
                                                <p class="font-semibold text-main-text text-body-lg">{{ $reservation->getEmail() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-body-md font-semibold text-brand mb-2">Phone Number</label>
                                        <div class="bg-sub rounded-lg p-4 transform hover:scale-[1.02] transition-transform duration-300">
                                            <div class="flex items-center gap-3">
                                                <i class="fas fa-phone text-main-text/60"></i>
                                                <p class="font-semibold text-main-text text-body-lg">{{ $reservation->getPhoneNumber() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($reservation->questionnaire)
                        <div class="bg-white rounded-2xl shadow-lg border border-disabled/50 overflow-hidden transform hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="bg-gradient-to-r from-brand to-main-button p-6">
                                <h2 class="text-heading-lg font-bold text-white flex items-center gap-3">
                                    <i class="fas fa-clipboard-list"></i>
                                    Questionnaire
                                </h2>
                            </div>
                            <div class="p-8">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-6 transform hover:scale-[1.02] transition-transform duration-300">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-green-800 text-body-lg">Questionnaire Completed</p>
                                            <p class="text-body-md text-green-600">All required information has been provided.</p>
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