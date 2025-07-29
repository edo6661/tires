<x-layouts.app>
    <div class="">
        <div class="container">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-bold text-brand mb-4">{{ __('customer/reservation.page_title') }}</h1>
                <p class="text-heading-lg text-main-text/70 max-w-2xl mx-auto">{{ __('customer/reservation.subtitle') }}</p>
            </div>
            @if($reservations->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-main-text">{{ $reservations->count() }}</div>
                                <div class="text-body-md text-main-text/60">{{ __('customer/reservation.total_reservations') }}</div>
                            </div>
                            <div class="h-8 w-px bg-gray-200"></div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-500">
                                    {{ $reservations->where('status.value', 'pending')->count() }}
                                </div>
                                <div class="text-body-md text-main-text/60">{{ __('customer/reservation.pending') }}</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-500">
                                    {{ $reservations->where('status.value', 'confirmed')->count() }}
                                </div>
                                <div class="text-body-md text-main-text/60">{{ __('customer/reservation.confirmed') }}</div>
                            </div>
                        </div>
                        <div class="hidden sm:block">
                            <a href="{{ route('home') }}"
                                class="inline-flex items-center px-6 py-3 bg-main-button text-white font-semibold rounded-lg hover:bg-btn-main-hover transition-colors duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-px">
                                <i class="fas fa-plus mr-2"></i>
                                {{ __('customer/reservation.new_reservation') }}
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-6">
                    @foreach($reservations as $reservation)
                        <article class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 cursor-pointer"
                                onclick="window.location.href='{{ route('customer.reservation.show', $reservation->id) }}'">
                            <div class="p-6 md:p-8">
                                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between mb-6">
                                            <div>
                                                <h3 class="text-title-md font-bold text-brand mb-2">
                                                    {{ $reservation->menu->name }}
                                                </h3>
                                                <div class="flex items-center gap-3 text-body-md text-main-text/60">
                                                    <span class="px-3 py-1 bg-sub rounded-full font-medium text-link">
                                                        #{{ $reservation->reservation_number }}
                                                    </span>
                                                    <span class="flex items-center gap-1.5">
                                                        <i class="fas fa-clock text-xs"></i>
                                                        {{ __('customer/reservation.required_time', ['time' => $reservation->menu->required_time]) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <span class="px-4 py-1.5 rounded-full text-sm font-semibold shadow-sm
                                                @if($reservation->status->value === 'pending')
                                                    bg-yellow-100 text-yellow-800
                                                @elseif($reservation->status->value === 'confirmed')
                                                    bg-green-100 text-green-800
                                                @elseif($reservation->status->value === 'completed')
                                                    bg-blue-100 text-blue-800
                                                @else
                                                    bg-red-100 text-red-800
                                                @endif
                                            ">
                                                {{ $reservation->status->label() }}
                                            </span>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                                            <div class="flex items-center gap-3 p-4 bg-sub rounded-lg">
                                                <div class="flex-shrink-0 w-10 h-10 bg-white rounded-full flex items-center justify-center border">
                                                    <i class="fas fa-calendar-alt text-link"></i>
                                                </div>
                                                <div>
                                                    <div class="text-body-md text-main-text/60">{{ __('customer/reservation.date') }}</div>
                                                    <div class="font-semibold text-main-text">{{ $reservation->reservation_datetime->format('d M Y') }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 p-4 bg-sub rounded-lg">
                                                <div class="flex-shrink-0 w-10 h-10 bg-white rounded-full flex items-center justify-center border">
                                                    <i class="fas fa-clock text-link"></i>
                                                </div>
                                                <div>
                                                    <div class="text-body-md text-main-text/60">{{ __('customer/reservation.time') }}</div>
                                                    <div class="font-semibold text-main-text">{{ $reservation->reservation_datetime->format('H:i') }}</div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3 p-4 bg-sub rounded-lg">
                                                <div class="flex-shrink-0 w-10 h-10 bg-white rounded-full flex items-center justify-center border">
                                                    <i class="fas fa-users text-link"></i>
                                                </div>
                                                <div>
                                                    <div class="text-body-md text-main-text/60">{{ __('customer/reservation.people_label') }}</div>
                                                    <div class="font-semibold text-main-text">{{ __('customer/reservation.people_count', ['count' => $reservation->number_of_people]) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($reservation->notes)
                                            <div class="bg-sub border-l-4 border-brand p-4 rounded-r-lg">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        <i class="fas fa-sticky-note text-link mt-1"></i>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm font-semibold text-brand mb-1">{{ __('customer/reservation.notes') }}</p>
                                                        <p class="text-body-md text-main-text/90">{{ $reservation->notes }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($reservation->status->value === 'pending')
                                        <div class="flex flex-col sm:flex-row gap-3 lg:min-w-[120px]">
                                            <button type="button"
                                                    class="inline-flex items-center justify-center px-6 py-3 bg-white border border-red-300 rounded-lg text-sm font-semibold text-red-600 hover:bg-red-50 hover:border-red-400 transition-colors duration-200 shadow-sm"
                                                    onclick="event.stopPropagation(); cancelReservation({{ $reservation->id }})">
                                                <i class="fas fa-times mr-2"></i>
                                                {{ __('customer/reservation.cancel_button') }}
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
                
                <div class="sm:hidden fixed bottom-6 right-6 z-10">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center justify-center w-14 h-14 bg-main-button text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform ">
                        <i class="fas fa-plus text-xl"></i>
                    </a>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="max-w-md mx-auto">
                        <div class="mx-auto h-32 w-32 mb-8">
                            <div class="w-full h-full bg-white border-4 border-sub rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar-times text-5xl text-gray-400"></i>
                            </div>
                        </div>
                        <h3 class="text-title-lg font-bold text-main-text mb-4">{{ __('customer/reservation.no_reservations_title') }}</h3>
                        <p class="text-body-lg text-main-text/70 mb-8 leading-relaxed">
                            {{ __('customer/reservation.no_reservations_body') }}
                        </p>
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center px-8 py-4 bg-main-button text-white font-semibold rounded-xl hover:bg-btn-main-hover transition-colors duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-px">
                            <i class="fas fa-plus mr-3"></i>
                            {{ __('customer/reservation.create_first_reservation') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 backdrop-blur-sm">
        <div class="flex items-center justify-center  p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" id="modalContent">
                <div class="p-8">
                    <div class="flex items-center justify-center mb-6">
                        <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-title-md font-bold text-main-text mb-3">{{ __('customer/reservation.modal_cancel_title') }}</h3>
                        <p class="text-main-text/70 mb-8 leading-relaxed">
                            {{ __('customer/reservation.modal_cancel_body') }}
                        </p>
                    </div>
                    <div class="flex gap-4">
                        <button type="button"
                                onclick="closeCancelModal()"
                                class="flex-1 px-6 py-3 border border-disabled rounded-xl text-sm font-semibold text-main-text bg-white hover:bg-gray-50 transition-colors duration-200">
                            {{ __('customer/reservation.modal_keep_button') }}
                        </button>
                        <button type="button"
                                onclick="confirmCancel()"
                                class="flex-1 px-6 py-3 bg-red-600 rounded-xl text-sm font-semibold text-white hover:bg-red-700 transition-colors duration-200 shadow-lg hover:shadow-xl">
                            {{ __('customer/reservation.modal_confirm_cancel_button') }}
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
            requestAnimationFrame(() => {
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            });
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
                const modal = document.getElementById('cancelModal');
                const title = "{{ __('customer/reservation.modal_feature_soon_title') }}";
                const body = "{{ __('customer/reservation.modal_feature_soon_body') }}";
                const button = "{{ __('customer/reservation.modal_got_it_button') }}";

                modal.innerHTML = `
                    <div class="flex items-center justify-center  p-4">
                        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center">
                            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-check text-green-600 text-2xl"></i>
                            </div>
                            <h3 class="text-title-md font-bold text-main-text mb-3">${title}</h3>
                            <p class="text-main-text/70 mb-6">${body}</p>
                            <button onclick="window.location.reload()" class="px-6 py-3 bg-main-button text-white font-semibold rounded-xl hover:bg-btn-main-hover transition-colors duration-200">
                                ${button}
                            </button>
                        </div>
                    </div>
                `;
            }
        }
        
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target.id === 'cancelModal') {
                closeCancelModal();
            }
        });
    </script>
</x-layouts.app>