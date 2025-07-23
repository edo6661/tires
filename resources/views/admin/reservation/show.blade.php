<x-layouts.app>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-eye mr-3 text-blue-600"></i>
                        Reservation Details #{{ $reservation->id }}
                    </h1>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                            {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                               ($reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                               ($reservation->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                            {{ $reservation->status->label() }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-400 mr-2"></i>
                            <p class="text-sm text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-user mr-2 text-gray-600"></i>
                            Customer Information
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Customer Type</label>
                                <div class="flex items-center">
                                    <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-full">
                                        {{ $reservation->user_id ? 'Registered Customer' : 'Guest Customer' }}
                                    </span>
                                </div>
                            </div>
                            @if($reservation->user_id)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Registered Customer</label>
                                    <p class="text-gray-900 font-medium">{{ $reservation->user->full_name }}</p>
                                    <p class="text-gray-600 text-sm">{{ $reservation->user->email }}</p>
                                </div>
                            @else
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <p class="text-gray-900">{{ $reservation->full_name }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name (Kana)</label>
                                        <p class="text-gray-900">{{ $reservation->full_name_kana }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <p class="text-gray-900">{{ $reservation->email }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <p class="text-gray-900">{{ $reservation->phone_number }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-gray-600"></i>
                            Reservation Details
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Menu</label>
                                <p class="text-gray-900 font-medium">{{ $reservation->menu->name }}</p>
                                <p class="text-gray-600 text-sm">{{ number_format($reservation->menu->price, 0, ',', '.') }} yen - {{ $reservation->menu->required_time }} minutes</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Date & Time</label>
                                <p class="text-gray-900 font-medium">
                                    {{ $reservation->reservation_datetime->format('d M Y, H:i') }}
                                </p>
                                <p class="text-gray-600 text-sm">
                                    Estimated completion: {{ $reservation->reservation_datetime->addMinutes($reservation->menu->required_time)->format('H:i') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Number of People</label>
                                <p class="text-gray-900">{{ $reservation->number_of_people }} people</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Cost</label>
                                <p class="text-gray-900 font-bold text-lg">{{ number_format($reservation->amount, 0, ',', '.') }} yen</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-8 bg-gray-50 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-gray-600"></i>
                        Additional Information
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reservation Status</label>
                            <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full 
                                {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                   ($reservation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($reservation->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                <i class="fas fa-circle mr-2 text-xs"></i>
                                {{ $reservation->status->label() }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Created At</label>
                            <p class="text-gray-900">{{ $reservation->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        @if($reservation->updated_at != $reservation->created_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Updated</label>
                            <p class="text-gray-900">{{ $reservation->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                        @endif
                        @if($reservation->notes)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <div class="bg-white rounded-md p-3 border border-gray-200">
                                <p class="text-gray-900 whitespace-pre-wrap">{{ $reservation->notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.reservation.calendar') }}" 
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Calendar
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>