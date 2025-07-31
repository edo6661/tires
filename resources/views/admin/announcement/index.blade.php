<x-layouts.app>
    <div class="container space-y-6" x-data="announcementIndex()">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('admin/announcement/index.title') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('admin/announcement/index.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.announcement.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    {{ __('admin/announcement/index.add_button') }}
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('admin/announcement/index.stats.total') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $announcements->total() }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-bullhorn text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('admin/announcement/index.stats.active') }}</p>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $announcements->where('is_active', true)->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('admin/announcement/index.stats.inactive') }}</p>
                        <p class="text-2xl font-bold text-red-600">
                            {{ $announcements->where('is_active', false)->count() }}
                        </p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">{{ __('admin/announcement/index.stats.today') }}</p>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ $announcements->where('published_at', '>=', today())->count() }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar-day text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Session Messages --}}
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

        {{-- Filter & Search --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('admin/announcement/index.filters.title') }}</h3>
                <button @click="showFilters = !showFilters" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-filter"></i>
                    <span x-text="showFilters ? translations.hide_filters : translations.show_filters"></span>
                </button>
            </div>
            <div x-show="showFilters" x-transition class="space-y-4">
                <form method="GET" action="{{ route('admin.announcement.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/announcement/index.filters.status_label') }}</label>
                        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">{{ __('admin/announcement/index.filters.all_statuses') }}</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('admin/announcement/index.stats.active') }}</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ __('admin/announcement/index.stats.inactive') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/announcement/index.filters.start_date_label') }}</label>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/announcement/index.filters.end_date_label') }}</label>
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('admin/announcement/index.filters.search_label') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin/announcement/index.filters.search_placeholder') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="lg:col-span-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-search"></i>
                            {{ __('admin/announcement/index.filters.filter_button') }}
                        </button>
                        <a href="{{ route('admin.announcement.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors duration-200">
                            {{ __('admin/announcement/index.filters.reset_button') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Actions Bar --}}
        <div x-show="selectedItems.length > 0" class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between" x-cloak>
            <span class="text-blue-700" x-text="translations.selected_text.replace(':count', selectedItems.length)">
            </span>
            <div class="flex gap-2">
                <button @click="toggleStatusSelected(true)" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-check mr-1"></i>
                    {{ __('admin/announcement/index.bulk_actions.activate_button') }}
                </button>
                <button @click="toggleStatusSelected(false)" class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-colors duration-200">
                    <i class="fas fa-pause mr-1"></i>
                    {{ __('admin/announcement/index.bulk_actions.deactivate_button') }}
                </button>
                <button @click="deleteSelected()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-colors duration-200">
                    <i class="fas fa-trash mr-1"></i>
                    {{ __('admin/announcement/index.bulk_actions.delete_button') }}
                </button>
            </div>
        </div>

        {{-- Announcements Table --}}
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin/announcement/index.list.title') }}</h3>
                </div>
            </div>
            @if($announcements->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox"
                                           @change="toggleSelectAll($event.target.checked)"
                                           :checked="allItemIds.length > 0 && selectedItems.length === allItemIds.length"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin/announcement/index.table.headers.title') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin/announcement/index.table.headers.content') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin/announcement/index.table.headers.publish_date') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin/announcement/index.table.headers.status') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin/announcement/index.table.headers.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($announcements as $announcement)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $announcement->id }}" x-model.number="selectedItems" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 max-w-xs">
                                            <div class="truncate" title="{{ $announcement->title }}">
                                                {{ $announcement->title }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500 max-w-xs">
                                            <div class="truncate" title="{{ strip_tags($announcement->content) }}">
                                                {{ Str::limit(strip_tags($announcement->content), 50) }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="space-y-1">
                                            <div>{{ $announcement->published_at ? $announcement->published_at->format('d/m/Y') : '-' }}</div>
                                            <div class="text-xs">{{ $announcement->published_at ? $announcement->published_at->format('H:i') : '-' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($announcement->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle text-green-500 mr-1 text-xs"></i>
                                                {{ __('admin/announcement/index.table.status_active') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle text-red-500 mr-1 text-xs"></i>
                                                {{ __('admin/announcement/index.table.status_inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.announcement.show', $announcement->id) }}" class="text-blue-600 hover:text-blue-900" title="{{ __('admin/announcement/index.table.actions_tooltip.view') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.announcement.edit', $announcement->id) }}" class="text-yellow-600 hover:text-yellow-900" title="{{ __('admin/announcement/index.table.actions_tooltip.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="toggleStatus({{ $announcement->id }})" class="text-{{ $announcement->is_active ? 'red' : 'green' }}-600 hover:text-{{ $announcement->is_active ? 'red' : 'green' }}-900" title="{{ $announcement->is_active ? __('admin/announcement/index.table.actions_tooltip.deactivate') : __('admin/announcement/index.table.actions_tooltip.activate') }}">
                                                <i class="fas fa-{{ $announcement->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button @click="deleteSingle({{ $announcement->id }})" class="text-red-600 hover:text-red-900" title="{{ __('admin/announcement/index.table.actions_tooltip.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $announcements->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-bullhorn text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('admin/announcement/index.empty.title') }}</h3>
                    <p class="text-gray-500 mb-4">{{ __('admin/announcement/index.empty.description') }}</p>
                    <a href="{{ route('admin.announcement.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-plus"></i>
                        {{ __('admin/announcement/index.add_button') }}
                    </a>
                </div>
            @endif
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mt-2">{{ __('admin/announcement/index.delete_modal.title') }}</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500" x-text="deleteMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300">
                            {{ __('admin/announcement/index.delete_modal.cancel_button') }}
                        </button>
                        <button @click="confirmDelete()" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700">
                            {{ __('admin/announcement/index.delete_modal.delete_button') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function announcementIndex() {
            return {
                showFilters: false,
                showDeleteModal: false,
                selectedItems: [],
                allItemIds: [],
                deleteTarget: null,
                deleteMessage: '',
                translations: @json(__('admin/announcement/index.js')),
                init() {
                    this.allItemIds = Array.from(document.querySelectorAll('tbody input[type="checkbox"][value]'))
                                         .map(cb => parseInt(cb.value));
                },
                toggleSelectAll(checked) {
                    this.selectedItems = checked ? [...this.allItemIds] : [];
                },
                deleteSingle(id) {
                    this.deleteTarget = [id];
                    this.deleteMessage = this.translations.delete_single_confirm;
                    this.showDeleteModal = true;
                },
                deleteSelected() {
                    if (this.selectedItems.length === 0) return;
                    this.deleteTarget = [...this.selectedItems];
                    this.deleteMessage = this.translations.delete_multiple_confirm.replace(':count', this.selectedItems.length);
                    this.showDeleteModal = true;
                },
                async toggleStatusSelected(status) {
                    if (this.selectedItems.length === 0) {
                        alert(this.translations.select_at_least_one);
                        return;
                    }
                    try {
                        const formData = new FormData();
                        formData.append('ids', JSON.stringify(this.selectedItems));
                        formData.append('status', status);
                        
                        const response = await fetch('{{ route("admin.announcement.bulkToggleStatus") }}', {
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
                            alert(result.message || this.translations.error_status);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert(this.translations.error_status);
                    }
                },
                async confirmDelete() {
                    try {
                        if (this.deleteTarget.length === 1) {
                            const url = '{{ route("admin.announcement.destroy", ["announcement" => "__ID__"]) }}'.replace('__ID__', this.deleteTarget[0]);
                            const response = await fetch(url, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            });
                            const result = await response.json();
                            if (result.success || response.ok) {
                                window.location.reload();
                            } else {
                                alert(result.message || this.translations.error_delete);
                            }
                        } else {
                            const formData = new FormData();
                            formData.append('ids', JSON.stringify(this.deleteTarget));                            
                            const response = await fetch('{{ route("admin.announcement.bulkDelete") }}', {
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
                                alert(result.message || this.translations.error_delete);
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert(this.translations.error_delete);
                    }
                    this.showDeleteModal = false;
                }
            }
        }

        async function toggleStatus(id) {
            try {
                const formData = new FormData();
                const response = await fetch('{{ route("admin.announcement.toggleStatus", ["id" => "__ID__"]) }}'.replace('__ID__', id), {
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
                    alert(result.error || "{{ __('admin/announcement/index.js.error_toggle_status') }}");
                }
            } catch (error) {
                console.error('Error:', error);
                alert("{{ __('admin/announcement/index.js.error_toggle_status') }}");
            }
        }
    </script>
</x-layouts.app>