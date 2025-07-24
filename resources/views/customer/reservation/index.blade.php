<x-layouts.app>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">My Reservations</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Manage and view all your reservations in one place</p>
            </div>
            @if($reservations->count() > 0)
                <!-- Stats Bar -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-900">{{ $reservations->count() }}</div>
                                <div class="text-sm text-gray-500">Total Reservations</div>
                            </div>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">
                                    {{ $reservations->where('status.value', 'pending')->count() }}
                                </div>
                                <div class="text-sm text-gray-500">Pending</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    {{ $reservations->where('status.value', 'confirmed')->count() }}
                                </div>
                                <div class="text-sm text-gray-500">Confirmed</div>
                            </div>
                        </div>
                        <div class="hidden sm:block">
                            <a href="{{ route('home') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                <i class="fas fa-plus mr-2"></i>
                                New Reservation
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Reservations List -->
                <div class="space-y-6">
                    @foreach($reservations as $reservation)
                        <article class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer" 
                                 onclick="window.location.href='{{ route('customer.reservation.show', $reservation->id) }}'">
                            <div class="p-8">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                    <div class="flex-1">
                                        <!-- Header -->
                                        <div class="flex items-start justify-between mb-6">
                                            <div>
                                                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                                                    {{ $reservation->menu->name }}
                                                </h3>
                                                <div class="flex items-center gap-3 text-sm text-gray-500">
                                                    <span class="px-3 py-1 bg-gray-100 rounded-full font-medium">
                                                        #{{ $reservation->reservation_number }}
                                                    </span>
                                                    <span class="flex items-center gap-1">
                                                        <i class="fas fa-clock text-xs"></i>
                                                        {{ $reservation->menu->required_time }} minutes
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="px-4 py-2 rounded-full text-sm font-semibold shadow-md
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
                                        <!-- Details Grid -->
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                                            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-calendar-alt text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-500">Date</div>
                                                    <div class="font-semibold text-gray-900">{{ $reservation->reservation_datetime->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-clock text-green-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-500">Time</div>
                                                    <div class="font-semibold text-gray-900">{{ $reservation->reservation_datetime->format('H:i') }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-lg">
                                                <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-users text-purple-600"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-500">People</div>
                                                    <div class="font-semibold text-gray-900">{{ $reservation->number_of_people }} people</div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Notes -->
                                        @if($reservation->notes)
                                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-sticky-note text-blue-400 mt-1"></i>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm font-medium text-blue-800 mb-1">Notes:</p>
                                                        <p class="text-sm text-blue-700">{{ $reservation->notes }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <!-- Actions -->
                                    @if($reservation->status->value === 'pending')
                                        <div class="flex flex-col sm:flex-row gap-3 lg:min-w-[120px]">
                                            <button type="button" 
                                                    class="inline-flex items-center justify-center px-6 py-3 bg-white border-2 border-red-200 rounded-lg text-sm font-semibold text-red-700 hover:bg-red-50 hover:border-red-300 transition-all duration-200 shadow-sm hover:shadow-md"
                                                    onclick="event.stopPropagation(); cancelReservation({{ $reservation->id }})">
                                                <i class="fas fa-times mr-2"></i>
                                                Cancel
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                <!-- Mobile New Reservation Button -->
                <div class="sm:hidden fixed bottom-6 right-6">
                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center justify-center w-14 h-14 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-110">
                        <i class="fas fa-plus text-xl"></i>
                    </a>
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div class="mx-auto h-32 w-32 mb-8">
                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar-times text-5xl text-gray-400"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">No Reservations Yet</h3>
                        <p class="text-gray-600 mb-8 text-lg leading-relaxed">
                            You haven't made any reservations yet. Start by creating your first reservation and enjoy our services.
                        </p>
                        <a href="{{ route('home') }}" 
                           class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-plus mr-3"></i>
                            Create First Reservation
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Cancel Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" id="modalContent">
                <div class="p-8">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Cancel Reservation</h3>
                        <p class="text-gray-600 mb-8 leading-relaxed">
                            Are you sure you want to cancel this reservation? This action cannot be undone and may result in cancellation fees.
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <button type="button" 
                                onclick="closeCancelModal()"
                                class="flex-1 px-6 py-3 border-2 border-gray-200 rounded-xl text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-300 transition-all duration-200">
                            Keep Reservation
                        </button>
                        <button type="button" 
                                onclick="confirmCancel()"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 rounded-xl text-sm font-semibold text-white hover:from-red-700 hover:to-red-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                            Yes, Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let reservationToCancel = null;
        
        function cancelReservation(reservationId) {
            reservationToCancel = reservationId;
            const modal = document.getElementById('cancelModal');
            const modalContent = document.getElementById('modalContent');
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
        }
        
        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                reservationToCancel = null;
            }, 300);
        }
        
        function confirmCancel() {
            if (reservationToCancel) {
                // Show a nice success message instead of alert
                const modal = document.getElementById('cancelModal');
                modal.innerHTML = `
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center">
                            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-check text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Feature Coming Soon</h3>
                            <p class="text-gray-600 mb-6">Reservation cancellation feature will be implemented soon.</p>
                            <button onclick="closeCancelModal()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200">
                                Got it
                            </button>
                        </div>
                    </div>
                `;
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        }
        
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });
    </script>
</x-layouts.app>