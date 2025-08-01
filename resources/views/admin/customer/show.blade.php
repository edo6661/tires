<x-layouts.app>
    <div class="container space-y-6" x-data="customerShow()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.customer.index') }}"
                   class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('admin/customer/show.header.title') }}</h1>
                    <p class="text-gray-600 mt-1">{{ __('admin/customer/show.header.subtitle') }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                @if($customerDetail['customer']['is_registered'])
                    {{-- <button @click="sendMessage()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-envelope mr-2"></i>{{ __('admin/customer/show.header.send_message_button') }}
                    </button> --}}
                @endif
                {{-- <button @click="exportData()"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>{{ __('admin/customer/show.header.export_button') }}
                </button> --}}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin/customer/show.stats.total_reservations') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $customerDetail['stats']['reservation_count'] }}</p>
                    </div>
                </div>
            </div>
            {{-- <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-yen-sign text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin/customer/show.stats.total_amount') }}</p>
                        <p class="text-2xl font-bold text-gray-900">¥{{ number_format($customerDetail['stats']['total_amount'], 0) }}</p>
                    </div>
                </div>
            </div> --}}
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <i class="fas fa-warehouse text-purple-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">{{ __('admin/customer/show.stats.tire_storage') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $customerDetail['stats']['tire_storage_count'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 p-6 sticky top-6">
                    <div class="text-center mb-6">
                        <div class="h-24 w-24 mx-auto rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center mb-4">
                            <span class="text-white font-bold text-2xl">
                                {{ substr($customerDetail['customer']['full_name'], 0, 2) }}
                            </span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $customerDetail['customer']['full_name'] }}</h2>
                        @if($customerDetail['customer']['full_name_kana'])
                            <p class="text-gray-600">{{ $customerDetail['customer']['full_name_kana'] }}</p>
                        @endif
                        <div class="flex justify-center gap-2 mt-3">
                            @if($customerDetail['customer']['is_registered'])
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-user-check mr-1"></i>{{ __('admin/customer/show.sidebar.status_registered') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-user mr-1"></i>{{ __('admin/customer/show.sidebar.status_guest') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.sidebar.email') }}</label>
                            <div class="flex items-center text-gray-900">
                                <i class="fas fa-envelope text-gray-400 mr-2 w-4"></i>
                                <span>{{ $customerDetail['customer']['email'] ?: 'N/A' }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.sidebar.phone') }}</label>
                            <div class="flex items-center text-gray-900">
                                <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                                <span>{{ $customerDetail['customer']['phone_number'] ?: 'N/A' }}</span>
                            </div>
                        </div>
                        
                        @if($customerDetail['customer']['is_registered'])
                            @if($customerDetail['customer']['company_name'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.sidebar.company') }}</label>
                                    <div class="flex items-center text-gray-900">
                                        <i class="fas fa-building text-gray-400 mr-2 w-4"></i>
                                        <span>{{ $customerDetail['customer']['company_name'] }}</span>
                                    </div>
                                </div>
                            @endif
                            @if($customerDetail['customer']['department'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.sidebar.department') }}</label>
                                    <div class="flex items-center text-gray-900">
                                        <i class="fas fa-users text-gray-400 mr-2 w-4"></i>
                                        <span>{{ $customerDetail['customer']['department'] }}</span>
                                    </div>
                                </div>
                            @endif
                            @if($customerDetail['customer']['date_of_birth'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.sidebar.dob') }}</label>
                                    <div class="flex items-center text-gray-900">
                                        <i class="fas fa-birthday-cake text-gray-400 mr-2 w-4"></i>
                                        <span>{{ \Carbon\Carbon::parse($customerDetail['customer']['date_of_birth'])->format('d M Y') }}</span>
                                    </div>
                                </div>
                            @endif
                            @if($customerDetail['customer']['gender'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.sidebar.gender') }}</label>
                                    <div class="flex items-center text-gray-900">
                                        <i class="fas fa-user text-gray-400 mr-2 w-4"></i>
                                        <span>{{ ucfirst($customerDetail['customer']['gender']) }}</span>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mt-4">
                                <div class="flex">
                                    <i class="fas fa-info-circle text-yellow-600 mr-2 mt-0.5"></i>
                                    <div class="text-sm text-yellow-800">
                                        <p class="font-medium">{{ __('admin/customer/show.sidebar.guest_info.title') }}</p>
                                        <p>{{ __('admin/customer/show.sidebar.guest_info.body') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <nav class="space-y-2">
                            <a href="#" @click.prevent="activeTab = 'info'"
                               :class="activeTab === 'info' ? 'bg-blue-50 text-blue-700 border-blue-300' : 'text-gray-600 hover:bg-gray-50'"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg border transition-colors">
                                <i class="fas fa-user mr-3"></i>
                                {{ __('admin/customer/show.tabs.customer_info') }}
                            </a>
                            <a href="#" @click.prevent="activeTab = 'reservations'"
                               :class="activeTab === 'reservations' ? 'bg-blue-50 text-blue-700 border-blue-300' : 'text-gray-600 hover:bg-gray-50'"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-lg border transition-colors">
                                <i class="fas fa-calendar-alt mr-3"></i>
                                {{ __('admin/customer/show.tabs.reservation_history') }}
                                <span class="ml-auto bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $customerDetail['stats']['reservation_count'] }}</span>
                            </a>
                            @if($customerDetail['customer']['is_registered'])
                                <a href="#" @click.prevent="activeTab = 'tirestorage'"
                                   :class="activeTab === 'tirestorage' ? 'bg-blue-50 text-blue-700 border-blue-300' : 'text-gray-600 hover:bg-gray-50'"
                                   class="flex items-center px-3 py-2 text-sm font-medium rounded-lg border transition-colors">
                                    <i class="fas fa-warehouse mr-3"></i>
                                    {{ __('admin/customer/show.tabs.tire_storage') }}
                                    <span class="ml-auto bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">{{ $customerDetail['stats']['tire_storage_count'] }}</span>
                                </a>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div x-show="activeTab === 'info'" class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">{{ __('admin/customer/show.main_content.customer_info.title') }}</h3>
                    
                    @if($customerDetail['customer']['is_registered'])
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.full_name') }}</label>
                                    <p class="text-gray-900">{{ $customerDetail['customer']['full_name'] ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.full_name_kana') }}</label>
                                    <p class="text-gray-900">{{ $customerDetail['customer']['full_name_kana'] ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.email') }}</label>
                                    <p class="text-gray-900">{{ $customerDetail['customer']['email'] ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.phone_number') }}</label>
                                    <p class="text-gray-900">{{ $customerDetail['customer']['phone_number'] ?: 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.company_name') }}</label>
                                    <p class="text-gray-900">{{ $customerDetail['customer']['company_name'] ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.department') }}</label>
                                    <p class="text-gray-900">{{ $customerDetail['customer']['department'] ?: 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.dob') }}</label>
                                    <p class="text-gray-900">
                                        {{ $customerDetail['customer']['date_of_birth'] ? \Carbon\Carbon::parse($customerDetail['customer']['date_of_birth'])->format('d M Y') : 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.gender') }}</label>
                                    <p class="text-gray-900">{{ $customerDetail['customer']['gender'] ? ucfirst($customerDetail['customer']['gender']) : 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        
                        @if($customerDetail['customer']['company_address'] || $customerDetail['customer']['home_address'])
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="text-md font-medium text-gray-900 mb-4">{{ __('admin/customer/show.main_content.customer_info.addresses_title') }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @if($customerDetail['customer']['company_address'])
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.company_address') }}</label>
                                            <p class="text-gray-900">{{ $customerDetail['customer']['company_address'] }}</p>
                                        </div>
                                    @endif
                                    @if($customerDetail['customer']['home_address'])
                                        <div>
                                            <label class="block text-sm font-medium text-gray-500 mb-1">{{ __('admin/customer/show.main_content.customer_info.home_address') }}</label>
                                            <p class="text-gray-900">{{ $customerDetail['customer']['home_address'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="bg-gray-50 rounded-lg p-6">
                            <div class="text-center">
                                <i class="fas fa-user-slash text-gray-400 text-4xl mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">{{ __('admin/customer/show.main_content.customer_info.guest.title') }}</h4>
                                <p class="text-gray-600 mb-4">{{ __('admin/customer/show.main_content.customer_info.guest.body') }}</p>
                                
                                <div class="max-w-md mx-auto space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <span class="text-sm font-medium text-gray-500">{{ __('admin/customer/show.main_content.customer_info.guest.name_label') }}</span>
                                        <span class="text-sm text-gray-900">{{ $customerDetail['customer']['full_name'] }}</span>
                                    </div>
                                    @if($customerDetail['customer']['full_name_kana'])
                                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                            <span class="text-sm font-medium text-gray-500">{{ __('admin/customer/show.main_content.customer_info.guest.name_kana_label') }}</span>
                                            <span class="text-sm text-gray-900">{{ $customerDetail['customer']['full_name_kana'] }}</span>
                                        </div>
                                    @endif
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <span class="text-sm font-medium text-gray-500">{{ __('admin/customer/show.main_content.customer_info.guest.email_label') }}</span>
                                        <span class="text-sm text-gray-900">{{ $customerDetail['customer']['email'] ?: 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-sm font-medium text-gray-500">{{ __('admin/customer/show.main_content.customer_info.guest.phone_label') }}</span>
                                        <span class="text-sm text-gray-900">{{ $customerDetail['customer']['phone_number'] ?: 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'reservations'" class="bg-white rounded-lg border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('admin/customer/show.main_content.reservation_history.title') }}</h3>
                        <span class="text-sm text-gray-500">{{ __('admin/customer/show.main_content.reservation_history.count_text', ['count' => $customerDetail['reservation_history']->count()]) }}</span>
                    </div>
                    
                    @if($customerDetail['reservation_history']->count() > 0)
                        <div class="space-y-4">
                            @foreach($customerDetail['reservation_history'] as $reservation)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <span class="font-medium text-gray-900">#{{ $reservation->reservation_number }}</span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                    @if($reservation->status->value === 'confirmed') bg-green-100 text-green-800
                                                    @elseif($reservation->status->value === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($reservation->status->value === 'completed') bg-blue-100 text-blue-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ $reservation->status->label() }}
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                <div>
                                                    <span class="text-gray-500">{{ __('admin/customer/show.main_content.reservation_history.date_time') }}</span>
                                                    <span class="text-gray-900 ml-1">{{ $reservation->reservation_datetime->format('d M Y, H:i') }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">{{ __('admin/customer/show.main_content.reservation_history.people') }}</span>
                                                    <span class="text-gray-900 ml-1">{{ $reservation->number_of_people }}</span>
                                                </div>
                                                @if($reservation->menu)
                                                    <div>
                                                        <span class="text-gray-500">{{ __('admin/customer/show.main_content.reservation_history.menu') }}</span>
                                                        <span class="text-gray-900 ml-1">{{ $reservation->menu->name }}</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="text-gray-500">{{ __('admin/customer/show.main_content.reservation_history.amount') }}</span>
                                                    <span class="text-gray-900 ml-1 font-medium">¥{{ number_format($reservation->amount, 0) }}</span>
                                                </div>
                                            </div>
                                            @if($reservation->notes)
                                                <div class="mt-2 text-sm">
                                                    <span class="text-gray-500">{{ __('admin/customer/show.main_content.reservation_history.notes') }}</span>
                                                    <span class="text-gray-900 ml-1">{{ $reservation->notes }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('admin.reservation.show', $reservation->id) }}"
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                {{ __('admin/customer/show.main_content.reservation_history.view_details_link') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">{{ __('admin/customer/show.main_content.reservation_history.no_records') }}</p>
                        </div>
                    @endif
                </div>

                @if($customerDetail['customer']['is_registered'])
                    <div x-show="activeTab === 'tirestorage'" class="bg-white rounded-lg border border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('admin/customer/show.main_content.tire_storage.title') }}</h3>
                            <span class="text-sm text-gray-500">{{ __('admin/customer/show.main_content.tire_storage.count_text', ['count' => $customerDetail['tire_storage']->count()]) }}</span>
                        </div>
                        
                        @if($customerDetail['tire_storage']->count() > 0)
                            <div class="space-y-4">
                                @foreach($customerDetail['tire_storage'] as $tire)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <h4 class="font-medium text-gray-900">{{ $tire->tire_brand }} - {{ $tire->tire_size }}</h4>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                        @if($tire->status->value === 'active') bg-green-100 text-green-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ $tire->status->label() }}
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                                    <div>
                                                        <span class="text-gray-500">{{ __('admin/customer/show.main_content.tire_storage.start_date') }}</span>
                                                        <span class="text-gray-900 ml-1">{{ $tire->storage_start_date->format('d M Y') }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">{{ __('admin/customer/show.main_content.tire_storage.planned_end') }}</span>
                                                        <span class="text-gray-900 ml-1">{{ $tire->planned_end_date->format('d M Y') }}</span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-500">{{ __('admin/customer/show.main_content.tire_storage.storage_fee') }}</span>
                                                        <span class="text-gray-900 ml-1 font-medium">¥{{ number_format($tire->storage_fee, 0) }}</span>
                                                    </div>
                                                    @if($tire->status->value === 'active')
                                                        <div>
                                                            <span class="text-gray-500">{{ __('admin/customer/show.main_content.tire_storage.days_remaining') }}</span>
                                                            <span class="text-gray-900 ml-1">{{ __('admin/customer/show.main_content.tire_storage.days_remaining_text', ['days' => $tire->planned_end_date->diffInDays(now())]) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                @if($tire->notes)
                                                    <div class="mt-2 text-sm">
                                                        <span class="text-gray-500">{{ __('admin/customer/show.main_content.tire_storage.notes') }}</span>
                                                        <span class="text-gray-900 ml-1">{{ $tire->notes }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-warehouse text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">{{ __('admin/customer/show.main_content.tire_storage.no_records') }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

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
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('admin/customer/show.modal.title', ['name' => $customerDetail['customer']['full_name']]) }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/customer/show.modal.subject_label') }}</label>
                            <input type="text"
                                   x-model="messageSubject"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="{{ __('admin/customer/show.modal.subject_placeholder') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin/customer/show.modal.message_label') }}</label>
                            <textarea x-model="messageContent"
                                      rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="{{ __('admin/customer/show.modal.message_placeholder') }}"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex gap-2 justify-end">
                        <button @click="showMessageModal = false"
                                class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                            {{ __('admin/customer/show.modal.cancel_button') }}
                        </button>
                        <button @click="sendMessageConfirm()"
                                class="px-4 py-2 bg-blue-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-700">
                            {{ __('admin/customer/show.modal.send_button') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function customerShow() {
            return {
                activeTab: 'info',
                showMessageModal: false,
                messageSubject: '',
                messageContent: '',
                
                sendMessage() {
                    this.messageSubject = '';
                    this.messageContent = '';
                    this.showMessageModal = true;
                },
                
                async sendMessageConfirm() {
                    if (!this.messageSubject.trim() || !this.messageContent.trim()) {
                        alert("{{ __('admin/customer/show.js_alerts.fill_fields') }}");
                        return;
                    }
                    
                    try {
                        // NOTE: This is a placeholder for actual API call
                        // await fetch('/api/send-message', {
                        //     method: 'POST',
                        //     headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        //     body: JSON.stringify({
                        //         customer_id: '{{ $customerDetail['customer']['customer_id'] }}',
                        //         subject: this.messageSubject,
                        //         content: this.messageContent
                        //     })
                        // });
                        
                        alert("{{ __('admin/customer/show.js_alerts.send_success') }}");
                        this.showMessageModal = false;
                    } catch (error) {
                        console.error('Error:', error);
                        alert("{{ __('admin/customer/show.js_alerts.send_failed') }}");
                    }
                },
                
                exportData() {
                    // Placeholder for export functionality
                    // alert("{{ __('admin/customer/show.js_alerts.export_placeholder') }}");
                }
            }
        }
    </script>
</x-layouts.app>