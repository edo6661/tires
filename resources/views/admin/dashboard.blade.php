<x-layouts.app>
    <div class="max-w-7xl mx-auto space-y-6" x-data="dashboardData()">
       <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="space-y-3">
                @foreach ($announcements as $announcement)
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-exclamation text-red-500 flex-shrink-0 mt-1"></i>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-sm">[{{ $announcement->title }}]</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-500">{{ $announcement->created_at->format('Y-m-d H:i') }}</span>
                                    <i class="fa-solid fa-xmark cursor-pointer text-sm hover:text-red-600 transition-colors" 
                                       @click="deactivateAnnouncement({{ $announcement->id }})" 
                                       title="Tutup pengumuman"></i>
                                </div>
                            </div>
                            <p class="text-sm text-gray-700 line-clamp-2 break-words">
                                {{ $announcement->content }}
                            </p>
                        </div>
                    </div>                                
                @endforeach
            </div>
        </div>
        <!-- Rest of your dashboard content remains the same -->
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
                        <span class=" text-sm"></span>
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
                        <span class=" text-sm"></span>
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
                                        <td class="px-4 py-2 text-gray-900">
                                            {{ $reservation->getFullName() }}
                                        </td>
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
    <script>
        function dashboardData() {
            return {
                async deactivateAnnouncement(announcementId) {
                    if (!confirm('Are you sure you want to close this announcement?')) {
                        return;
                    }
                    try {
                        const formData = new FormData();
                        formData.append('ids', JSON.stringify([announcementId]));
                        formData.append('status', false); 
                        const response = await fetch('/admin/announcement/bulk-toggle-status', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        });
                        const result = await response.json();
                        if (result.success) {
                            window.location.reload();
                        } else {
                            alert(result.message || 'An error occurred while deactivating the announcement');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('An error occurred while deactivating the announcement');
                    }
                }
            }
        }
    </script>
</x-layouts.app>