<x-layouts.app>
    {{-- Teruskan terjemahan JavaScript ke Alpine.js menggunakan @json --}}
    <div class="container space-y-6" x-data='dashboardData(@json(__('admin/dashboard.javascript')))'>
        <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 transform transition-all duration-500 ">
            <div class="space-y-3">
                @foreach ($announcements as $announcement)
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-sub/30 border border-sub hover:bg-sub/50 transition-all duration-300 transform" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">
                        <i class="fa-solid fa-circle-exclamation text-main-button flex-shrink-0 mt-1 animate-pulse"></i>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-body-md text-brand">[{{ $announcement->title }}]</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-body-md text-main-text/70">{{ $announcement->created_at->format('Y-m-d H:i') }}</span>
                                    <i class="fa-solid fa-xmark cursor-pointer text-body-md hover:text-main-button transition-all duration-200 hover:scale-100 transform" 
                                       @click="deactivateAnnouncement({{ $announcement->id }})" 
                                       title="{{ __('admin/dashboard.announcement.close_tooltip') }}"></i>
                                </div>
                            </div>
                            <p class="text-body-md text-main-text line-clamp-2 break-words">
                                {{ $announcement->content }}
                            </p>
                        </div>
                    </div>                                          
                @endforeach
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 transform transition-all duration-500 ">
                    <h2 class="text-heading-lg font-semibold text-brand mb-4 border-b border-sub pb-2">{{ __('admin/dashboard.todo.title') }}</h2>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 rounded-lg bg-sub/20 hover:bg-sub/40 transition-all duration-300 group cursor-pointer"
                            @click="window.location.href='{{ route('admin.reservation.calendar',[
                                'tab' => 'list'
                            ]) }}';"
                        >
                            <span class="text-body-md font-medium text-main-text group-hover:text-brand transition-colors duration-300">{{ __('admin/dashboard.todo.today_reservations') }}</span>
                            <div class="space-x-2 flex items-center">
                                <span class="text-title-lg font-bold text-brand transform group-hover:scale-100 transition-transform duration-300">{{ $todayReservations->count() }}</span>
                                <span class="text-body-md text-main-text/70">{{ __('admin/dashboard.todo.cases_unit') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-sub/20 hover:bg-sub/40 transition-all duration-300 group cursor-pointer"
                            @click="window.location.href='{{ route('admin.contact.index') }}';"
                        >
                            <span class="text-body-md font-medium text-main-text group-hover:text-brand transition-colors duration-300">{{ __('admin/dashboard.todo.contacts') }}</span>
                            <div class="space-x-2 flex items-center">
                                <span class="text-title-lg font-bold text-brand transform group-hover:scale-100 transition-transform duration-300">{{ $pendingContactsCount }}</span>
                                <span class="text-body-md text-main-text/70">{{ __('admin/dashboard.todo.cases_unit') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 transform transition-all duration-500 ">
                    <div class="flex items-center justify-between mb-4 border-b border-sub pb-2">
                        <h2 class="text-heading-lg font-semibold text-brand">{{ __('admin/dashboard.contact.title') }}</h2>
                        <span class="text-body-md text-main-text/70"></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-body-md">
                            <thead class="bg-sub/40">
                                <tr>
                                    <th class="px-4 py-3 text-left text-brand font-semibold rounded-tl-lg">{{ __('admin/dashboard.contact.received_at') }}</th>
                                    <th class="px-4 py-3 text-left text-brand font-semibold">{{ __('admin/dashboard.contact.customer_name') }}</th>
                                    <th class="px-4 py-3 text-left text-brand font-semibold rounded-tr-lg">{{ __('admin/dashboard.contact.subject') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-disabled/30">
                                @forelse($pendingContacts as $contact)
                                    <tr class="hover:bg-sub/20 transition-all duration-300 transform">
                                        <td class="px-4 py-3 text-main-text">{{ $contact->created_at->format('m/d H:i') }}</td>
                                        <td class="px-4 py-3 text-main-text font-medium">{{ $contact->user->name ?? 'Customer' }}</td>
                                        <td class="px-4 py-3 text-main-text/80">{{ $contact->subject ?? 'Message from customer' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-main-text/70">
                                            {{ __('admin/dashboard.contact.no_pending') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('admin.contact.index') }}" class="text-link hover:text-link-hover text-body-md transition-colors duration-300 hover:underline">{{ __('admin/dashboard.contact.see_more') }}</a>
                    </div>
                </div>
            </div>
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 transform transition-all duration-500 ">
                    <div class="flex items-center justify-between mb-4 border-b border-sub pb-2">
                        <h2 class="text-heading-lg font-semibold text-brand">{{ __('admin/dashboard.reservation.title') }}</h2>
                        <span class="text-body-md text-main-text/70"></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-body-md">
                            <thead class="bg-sub/40">
                                <tr>
                                    <th class="px-4 py-3 text-left text-brand font-semibold rounded-tl-lg">{{ __('admin/dashboard.reservation.time') }}</th>
                                    <th class="px-4 py-3 text-left text-brand font-semibold">{{ __('admin/dashboard.reservation.service') }}</th>
                                    <th class="px-4 py-3 text-left text-brand font-semibold rounded-tr-lg">{{ __('admin/dashboard.reservation.customer_name') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-disabled/30">
                                @forelse($todayReservations as $reservation)
                                    <tr class="cursor-pointer hover:bg-sub/20 transition-all duration-300 transform"
                                        @click="window.location.href='{{ route('admin.reservation.show', $reservation->id) }}';" 
                                    >
                                        <td class="px-4 py-3 text-main-text">{{ $reservation->reservation_datetime->format('m/d H:i') }}</td>
                                        <td class="px-4 py-3 text-main-text/80">{{ $reservation->menu->name }}</td>
                                        <td class="px-4 py-3 text-main-text font-medium">
                                            {{ $reservation->getFullName() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-8 text-center text-main-text/70">
                                            {{ __('admin/dashboard.reservation.no_reservations_today') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 transform transition-all duration-500 ">
                    <div class="flex items-center justify-between mb-4 border-b border-sub pb-2">
                        <h2 class="text-heading-lg font-semibold text-brand">
                            {{ __('admin/dashboard.status.title', ['date' => Carbon\Carbon::now()->format('F Y')]) }}
                        </h2>
                        <span class="text-body-md text-main-text/70">></span>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-sub/30 rounded-lg border border-sub/50 hover:bg-sub/50 hover:border-brand/30 transition-all duration-300 hover:scale-[1.02] transform group cursor-pointer"
                            @click="window.location.href='{{ route('admin.reservation.calendar') }}';"
                        >
                            <div class="flex items-center gap-2">
                                <span class="text-body-md font-medium text-main-text group-hover:text-brand transition-colors duration-300">{{ __('admin/dashboard.status.reservations') }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-title-lg font-bold text-brand transform group-hover:scale-100 transition-transform duration-300">{{ $monthlyReservations->count() }}</div>
                                <div class="text-body-md text-main-text/70">{{ __('admin/dashboard.status.cases_unit') }}</div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-sub/30 rounded-lg border border-sub/50 hover:bg-sub/50 hover:border-brand/30 transition-all duration-300 hover:scale-[1.02] transform group cursor-pointer"
                            @click="window.location.href='{{ route('admin.customer.index') }}';"
                        >
                            <div class="flex items-center gap-2">
                                <span class="text-body-md font-medium text-main-text group-hover:text-brand transition-colors duration-300">{{ __('admin/dashboard.status.new_customers') }}</span>
                            </div>
                            <div class="text-right">
                                <div class="text-title-lg font-bold text-brand transform group-hover:scale-100 transition-transform duration-300">{{ $newCustomersThisMonth }}</div>
                                <div class="text-body-md text-main-text/70">{{ __('admin/dashboard.status.cases_unit') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function dashboardData(translations) {
            return {
                translations: translations, 
                async deactivateAnnouncement(announcementId) {
                    if (!confirm(this.translations.confirm_close_announcement)) {
                        return;
                    }
                    try {
                        const formData = new FormData();
                        formData.append('ids', JSON.stringify([announcementId]));
                        formData.append('status', false); 
                        const response = await fetch('{{ route('admin.announcement.bulkToggleStatus') }}', {
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
                            alert(result.message || this.translations.deactivation_error);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert(this.translations.deactivation_error);
                    }
                }
            }
        }
    </script>
</x-layouts.app>