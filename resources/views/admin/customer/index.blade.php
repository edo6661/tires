<x-layouts.app>
    <div class="container space-y-6" x-data="customerIndex()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin/customer/index.title') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('admin/customer/index.description') }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-user-plus text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin/customer/index.stats.first_time') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $customerTypeCounts['first_time'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-sync-alt text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin/customer/index.stats.repeat') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $customerTypeCounts['repeat'] }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin/customer/index.stats.dormant') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $customerTypeCounts['dormant'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text"
                               x-model="searchQuery"
                               @input.debounce.500ms="applyFilters()"
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="{{ __('admin/customer/index.filters.search_placeholder') }}">
                    </div>
                </div>
                <div class="lg:w-64">
                    <select x-model="customerType"
                            @change="applyFilters()"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">{{ __('admin/customer/index.filters.all_types') }}</option>
                        <option value="first_time">{{ __('admin/customer/index.filters.first_time') }}</option>
                        <option value="repeat">{{ __('admin/customer/index.filters.repeat') }}</option>
                        <option value="dormant">{{ __('admin/customer/index.filters.dormant') }}</option>
                    </select>
                </div>
                <button @click="resetFilters()"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    <i class="fas fa-times mr-2"></i>{{ __('admin/customer/index.filters.reset') }}
                </button>
            </div>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin/customer/index.table.header.customer') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin/customer/index.table.header.contact_info') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin/customer/index.table.header.status') }}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin/customer/index.table.header.reservations') }}
                            </th>
                            {{-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin/customer/index.table.header.total_amount') }}
                            </th> --}}
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin/customer/index.table.header.last_reservation') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('admin/customer/index.table.header.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($customers as $customer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">
                                                    {{ substr($customer->full_name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $customer->full_name }}
                                            </div>
                                            @if($customer->full_name_kana)
                                                <div class="text-sm text-gray-500">
                                                    {{ $customer->full_name_kana }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $customer->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $customer->phone_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col gap-1">
                                        @if($customer->is_registered)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-user-check mr-1"></i>
                                                {{ __('admin/customer/index.table.status_badge.registered') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <i class="fas fa-user mr-1"></i>
                                                {{ __('admin/customer/index.table.status_badge.guest') }}
                                            </span>
                                        @endif
                                        @php
                                            $customerTypeKey = '';
                                            $customerTypeBadge = '';
                                            if($customer->reservation_count == 1) {
                                                $customerTypeKey = 'admin/customer/index.table.type_badge.first_time';
                                                $customerTypeBadge = 'bg-blue-100 text-blue-800';
                                            } elseif($customer->reservation_count >= 3) {
                                                $customerTypeKey = 'admin/customer/index.table.type_badge.repeat';
                                                $customerTypeBadge = 'bg-green-100 text-green-800';
                                            } elseif(\Carbon\Carbon::parse($customer->latest_reservation)->diffInMonths(now()) >= 3) {
                                                $customerTypeKey = 'admin/customer/index.table.type_badge.dormant';
                                                $customerTypeBadge = 'bg-yellow-100 text-yellow-800';
                                            }
                                        @endphp
                                        @if($customerTypeKey)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $customerTypeBadge }}">
                                                {{ __($customerTypeKey) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-check text-gray-400 mr-2"></i>
                                        {{ __('admin/customer/index.table.reservations_count', ['count' => $customer->reservation_count]) }}
                                    </div>
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-yen-sign text-gray-400 mr-2"></i>
                                        ¥{{ number_format($customer->total_amount, 0) }}
                                    </div>
                                </td> --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($customer->latest_reservation)->format('d M Y') }}
                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($customer->latest_reservation)->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($customer->is_registered)
                                            <a href="{{ route('admin.customer.show', $customer->user_id ?? $customer->customer_id) }}"
                                               class="text-blue-600 hover:text-blue-900 transition-colors"
                                               title="{{ __('admin/customer/index.table.actions_tooltip.view_details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            {{-- <button @click="sendMessage('{{ $customer->customer_id }}')"
                                                    class="text-green-600 hover:text-green-900 transition-colors"
                                                    title="{{ __('admin/customer/index.table.actions_tooltip.send_message') }}">
                                                <i class="fas fa-envelope"></i>
                                            </button> --}}
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-users text-gray-300 text-4xl mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('admin/customer/index.table.empty.title') }}</h3>
                                        <p class="text-gray-500">{{ __('admin/customer/index.table.empty.description') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($customers->hasPages())
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if($customers->previousPageUrl())
                                <a href="{{ $customers->previousPageUrl() }}"
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    {{ __('admin/customer/index.pagination.previous') }}
                                </a>
                            @endif
                            @if($customers->nextPageUrl())
                                <a href="{{ $customers->nextPageUrl() }}"
                                   class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    {{ __('admin/customer/index.pagination.next') }}
                                </a>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                {{ $customers->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- Modal -->
        <div x-show="showMessageModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border max-w-md shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="translations.modal_title"></h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" x-text="translations.modal_subject"></label>
                            <input type="text"
                                   x-model="messageSubject"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   :placeholder="translations.modal_subject_placeholder">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" x-text="translations.modal_message"></label>
                            <textarea x-model="messageContent"
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      :placeholder="translations.modal_message_placeholder"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2 justify-end">
                        <button @click="showMessageModal = false"
                                class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300"
                                x-text="translations.modal_cancel">
                        </button>
                        <button @click="sendMessageConfirm()"
                                class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700"
                                x-text="translations.modal_send">
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function customerIndex() {
            return {
                searchQuery: '{{ request('search') }}',
                customerType: '{{ request('customer_type') }}',
                showMessageModal: false,
                selectedCustomerId: null,
                messageSubject: '',
                messageContent: '',
                translations: {
                    validation_error: "{{ __('admin/customer/index.alerts.validation_error') }}",
                    send_success: "{{ __('admin/customer/index.alerts.send_success') }}",
                    send_error: "{{ __('admin/customer/index.alerts.send_error') }}",
                    modal_title: "{{ __('admin/customer/index.modal.title') }}",
                    modal_subject: "{{ __('admin/customer/index.modal.subject') }}",
                    modal_subject_placeholder: "{{ __('admin/customer/index.modal.subject_placeholder') }}",
                    modal_message: "{{ __('admin/customer/index.modal.message') }}",
                    modal_message_placeholder: "{{ __('admin/customer/index.modal.message_placeholder') }}",
                    modal_cancel: "{{ __('admin/customer/index.modal.cancel') }}",
                    modal_send: "{{ __('admin/customer/index.modal.send') }}"
                },
                applyFilters() {
                    const params = new URLSearchParams();
                    if (this.searchQuery.trim()) {
                        params.append('search', this.searchQuery.trim());
                    }
                    if (this.customerType) {
                        params.append('customer_type', this.customerType);
                    }
                    const url = params.toString() ?
                        `{{ route('admin.customer.index') }}?${params.toString()}` :
                        '{{ route('admin.customer.index') }}';
                    window.location.href = url;
                },
                resetFilters() {
                    this.searchQuery = '';
                    this.customerType = '';
                    window.location.href = '{{ route('admin.customer.index') }}';
                },
                sendMessage(customerId) {
                    this.selectedCustomerId = customerId;
                    this.messageSubject = '';
                    this.messageContent = '';
                    this.showMessageModal = true;
                },
                async sendMessageConfirm() {
                    if (!this.messageSubject.trim() || !this.messageContent.trim()) {
                        alert(this.translations.validation_error);
                        return;
                    }
                    try {
                        // NOTE: This is a placeholder for actual API call
                        // await fetch('/api/send-message', {
                        //     method: 'POST',
                        //     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        //     body: JSON.stringify({
                        //         customer_id: this.selectedCustomerId,
                        //         subject: this.messageSubject,
                        //         content: this.messageContent
                        //     })
                        // });
                        
                        alert(this.translations.send_success);
                        this.showMessageModal = false;
                    } catch (error) {
                        console.error('Error:', error);
                        alert(this.translations.send_error);
                    }
                }
            }
        }
    </script>
</x-layouts.app>