<x-layouts.app>
    <div class="container space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.announcement.index') }}"
                   class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition-colors duration-200">
                    <i class="fas fa-arrow-left"></i>
                    <span>{{ __('admin/announcement/show.header.back_to_list') }}</span>
                </a>
                <div class="h-6 w-px bg-gray-300"></div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin/announcement/show.header.title') }}</h1>
            </div>
            
        </div>
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    <button @click="show = false" class="text-green-700 hover:text-green-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" x-data="{ show: true }" x-show="show">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    <button @click="show = false" class="text-red-700 hover:text-red-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-full bg-{{ $announcement->is_active ? 'green' : 'red' }}-100">
                        <i class="fas fa-{{ $announcement->is_active ? 'check-circle' : 'times-circle' }} text-{{ $announcement->is_active ? 'green' : 'red' }}-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('admin/announcement/show.status_card.title') }}</h3>
                        <p class="text-sm text-gray-600">
                            {{ __('admin/announcement/show.status_card.description_prefix') }}
                            <span class="font-medium text-{{ $announcement->is_active ? 'green' : 'red' }}-600">
                                {{ $announcement->is_active ? __('admin/announcement/show.status_card.active') : __('admin/announcement/show.status_card.inactive') }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">{{ __('admin/announcement/show.status_card.created_at') }}</p>
                    <p class="text-sm font-medium text-gray-900">{{ $announcement->created_at->format('d M Y, H:i') }}</p>
                    @if($announcement->updated_at != $announcement->created_at)
                        <p class="text-xs text-gray-500 mt-1">{{ __('admin/announcement/show.status_card.updated_at_prefix') }} {{ $announcement->updated_at->format('d M Y, H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 px-6 py-4 border-b border-blue-200">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $announcement->title }}</h2>
                        <div class="flex items-center gap-4 text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar"></i>
                                <span>
                                    {{ __('admin/announcement/show.main_card.publish_date_label') }}
                                    {{ $announcement->published_at ? $announcement->published_at->format('d M Y, H:i') : __('admin/announcement/show.main_card.not_published_yet') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-eye"></i>
                                <span>ID: #{{ $announcement->id }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        @if($announcement->is_active)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                {{ __('admin/announcement/show.status_card.active') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                {{ __('admin/announcement/show.status_card.inactive') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="px-6 py-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('admin/announcement/show.main_card.content_title') }}</h3>
                        <div class="prose max-w-none">
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <div class="whitespace-pre-line text-gray-800 leading-relaxed">
                                    {{ $announcement->content }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-t pt-4 mt-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-3">{{ __('admin/announcement/show.main_card.additional_info_title') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-700 w-24">{{ __('admin/announcement/show.additional_info.id') }}</span>
                                    <span class="text-gray-600">{{ $announcement->id }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-700 w-24">{{ __('admin/announcement/show.additional_info.status') }}</span>
                                    <span class="text-{{ $announcement->is_active ? 'green' : 'red' }}-600 font-medium">
                                        {{ $announcement->is_active ? __('admin/announcement/show.status_card.active') : __('admin/announcement/show.status_card.inactive') }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-700 w-24">{{ __('admin/announcement/show.additional_info.published') }}</span>
                                    <span class="text-gray-600">
                                        {{ $announcement->published_at ? $announcement->published_at->format('d M Y, H:i') : __('admin/announcement/show.main_card.not_published_yet') }}
                                    </span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-700 w-24">{{ __('admin/announcement/show.additional_info.created') }}</span>
                                    <span class="text-gray-600">{{ $announcement->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-700 w-24">{{ __('admin/announcement/show.additional_info.updated') }}</span>
                                    <span class="text-gray-600">{{ $announcement->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-700 w-24">{{ __('admin/announcement/show.additional_info.characters_label') }}</span>
                                    <span class="text-gray-600">{{ strlen(strip_tags($announcement->content)) }} {{ __('admin/announcement/show.additional_info.characters_unit') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('admin/announcement/show.actions.title') }}</h3>
                    <p class="text-sm text-gray-600">{{ __('admin/announcement/show.actions.description') }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.announcement.edit', $announcement->id) }}"
                       class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-edit"></i>
                        {{ __('admin/announcement/show.actions.edit') }}
                    </a>
                    <button onclick="toggleStatus({{ $announcement->id }})"
                            class="px-4 py-2 bg-{{ $announcement->is_active ? 'red' : 'green' }}-600 text-white rounded-lg hover:bg-{{ $announcement->is_active ? 'red' : 'green' }}-700 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-{{ $announcement->is_active ? 'pause' : 'play' }}"></i>
                        {{ $announcement->is_active ? __('admin/announcement/show.actions.deactivate') : __('admin/announcement/show.actions.activate') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2">{{ __('admin/announcement/show.delete_modal.title') }}</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        {{ __('admin/announcement/show.delete_modal.text_line_1') }}
                        <br><strong>{{ __('admin/announcement/show.delete_modal.text_line_2') }}</strong>
                    </p>
                </div>
                <div class="items-center px-4 py-3 flex gap-2 justify-center">
                    <button onclick="hideDeleteModal()"
                            class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300 transition-colors duration-200">
                        {{ __('admin/announcement/show.delete_modal.cancel_button') }}
                    </button>
                    <button onclick="executeDelete()"
                            class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 transition-colors duration-200">
                        {{ __('admin/announcement/show.delete_modal.delete_button') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        let deleteId = null;
        
        async function toggleStatus(id) {
            try {
                const formData = new FormData();
                const url = '{{ route("admin.announcement.toggleStatus", ["id" => "__ID__"]) }}'.replace('__ID__', id);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('{{ __("admin/announcement/show.alerts.toggle_status_error") }}');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('{{ __("admin/announcement/show.alerts.toggle_status_error") }}');
            }
        }
        
        function confirmDelete(id) {
            deleteId = id;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        
        function hideDeleteModal() {
            deleteId = null;
            document.getElementById('deleteModal').classList.add('hidden');
        }
        
        async function executeDelete() {
            if (!deleteId) return;
            
            try {
                const url = '{{ route("admin.announcement.destroy", ["announcement" => "__ID__"]) }}'.replace('__ID__', deleteId);
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (response.ok) {
                    window.location.href = '{{ route("admin.announcement.index") }}';
                } else {
                    alert('{{ __("admin/announcement/show.alerts.delete_error") }}');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('{{ __("admin/announcement/show.alerts.delete_error") }}');
            }
            
            hideDeleteModal();
        }
        
        document.getElementById('deleteModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                hideDeleteModal();
            }
        });
    </script>
</x-layouts.app>