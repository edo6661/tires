<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6">
        @if($customersUntilLimit <= 10)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-800">
                            Only {{ $customersUntilLimit }} customers left until the upper limit. Please upgrade your plan.
                        </p>
                    </div>
                </div>
            </div>
        @endif
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">To Do</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium ">Today's Reservations</span>
                            <div class="space-x-2">
                                <span class="text-xl font-bold ">{{ $todayReservations->count() }}</span>
                                <span class="text-sm text-gray-500">Cases</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">Contacts</span>
                            <div class="space-x-2">
                                <span class="text-xl font-bold">{{ $pendingContactsCount }}</span>
                                <span class="text-sm text-gray-500">Cases</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Contact</h2>
                        <span class=" text-sm">></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-600">Received At</th>
                                    <th class="px-4 py-2 text-left text-gray-600">Customer Name</th>
                                    <th class="px-4 py-2 text-left text-gray-600">Subject</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($pendingContacts as $contact)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-900">{{ $contact->created_at->format('m/d H:i') }}</td>
                                        <td class="px-4 py-2 text-gray-900">{{ $contact->user->name ?? 'Customer' }}</td>
                                        <td class="px-4 py-2 text-gray-700">{{ $contact->subject ?? 'Message from customer' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                            No pending contacts
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="#" class=" hover:text-blue-800 text-sm">See more</a>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Today's Reservations</h2>
                        <span class=" text-sm">></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-600">Time</th>
                                    <th class="px-4 py-2 text-left text-gray-600">Service</th>
                                    <th class="px-4 py-2 text-left text-gray-600">Customer Name</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($todayReservations as $reservation)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-900">{{ $reservation->reservation_datetime->format('m/d H:i') }}</td>
                                        <td class="px-4 py-2 text-gray-700">{{ $reservation->menu->name ?? 'Service' }}</td>
                                        <td class="px-4 py-2 text-gray-900">{{ $reservation->user->name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                            No reservations today
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Reservations/Customer Status ({{ Carbon\Carbon::now()->format('F Y') }})
                        </h2>
                        <span class=" text-sm">></span>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium">Reservations</span>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold ">{{ $monthlyReservations->count() }}</div>
                                <div class="text-xs text-gray-500">Cases</div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 px-4">
                            {{ $onlineReservationsThisMonth }} Online reservations
                        </div>
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium">New Customers</span>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold ">{{ $newCustomersThisMonth }}</div>
                                <div class="text-xs text-gray-500">Cases</div>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 px-4">
                            {{ $newCustomersThisMonth }} Online registrations
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
