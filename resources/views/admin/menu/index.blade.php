<x-layouts.app>
    <div class="container space-y-6" x-data="menuIndex()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-title-lg font-bold text-main-text">{{ __('admin/menu.title') }}</h1>
                <p class="text-main-text/70 mt-1 text-body-md">{{ __('admin/menu.subtitle') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menu.create') }}"
                    class="px-4 py-2 bg-main-button text-white rounded-lg hover:bg-btn-main-hover transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2 text-button-md font-semibold">
                    <i class="fas fa-plus"></i>
                    {{ __('admin/menu.add_menu') }}
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">{{ __('admin/menu.total_menus') }}</p>
                        <p class="text-title-md font-bold text-main-text">{{ $menus->total() }}</p>
                    </div>
                    <div class="bg-brand/10 p-3 rounded-full">
                        <i class="fas fa-utensils text-brand"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">{{ __('admin/menu.active') }}</p>
                        <p class="text-title-md font-bold text-green-600">
                            {{ $menus->where('is_active', true)->count() }}
                        </p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">{{ __('admin/menu.inactive') }}</p>
                        <p class="text-title-md font-bold text-red-600">
                            {{ $menus->where('is_active', false)->count() }}
                        </p>
                    </div>
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-body-md text-main-text/70">{{ __('admin/menu.average_price') }}</p>
                        <p class="text-title-md font-bold text-purple-600">
                            ¥ {{ number_format($menus->avg('price') ?? 0, 0, '.', ',') }}
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-money-bill-wave text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        {{ session('success') }}
                    </div>
                    <button @click="show = false" class="text-green-700 hover:text-green-900 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg animate-fade-in" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ session('error') }}
                    </div>
                    <button @click="show = false" class="text-red-700 hover:text-red-900 transition-colors duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-heading-lg font-semibold text-main-text">{{ __('admin/menu.filters_search') }}</h3>
                <button @click="showFilters = !showFilters" class="text-link hover:text-link-hover transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-filter"></i>
                    <span x-text="showFilters ? '{{ __('admin/menu.hide_filters') }}' : '{{ __('admin/menu.show_filters') }}'" class="text-body-md"></span>
                </button>
            </div>
            <div x-show="showFilters"
                 x-cloak 
                 x-transition:enter="transition-all ease-out duration-300"
                 x-transition:enter-start="opacity-0 max-h-0"
                 x-transition:enter-end="opacity-100 max-h-96"
                 x-transition:leave="transition-all ease-in duration-200"
                 x-transition:leave-start="opacity-100 max-h-96"
                 x-transition:leave-end="opacity-0 max-h-0"
                 class="space-y-4 overflow-hidden">
                <form method="GET" action="{{ route('admin.menu.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/menu.status') }}</label>
                        <select name="status" class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                            <option value="">{{ __('admin/menu.all_statuses') }}</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('admin/menu.active') }}</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ __('admin/menu.inactive') }}</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/menu.min_price_range') }}</label>
                        <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="0" 
                               class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                    </div>
                    <div>
                        <label class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/menu.max_price_range') }}</label>
                        <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="1000000" 
                               class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-body-md font-medium text-main-text mb-1">{{ __('admin/menu.search') }}</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin/menu.search_placeholder') }}" 
                               class="w-full border border-disabled rounded-lg px-3 py-2 text-body-md focus:ring-2 focus:ring-brand focus:border-brand transition-all duration-200">
                    </div>
                    <div class="lg:col-span-4 flex gap-2">
                        <button type="submit" 
                            class="px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand/90 transition-all duration-200 transform hover:scale-105 flex items-center gap-2 text-button-md font-semibold">
                            <i class="fas fa-search"></i>
                            {{ __('admin/menu.filter') }}
                        </button>
                        <a href="{{ route('admin.menu.index') }}" 
                           class="px-4 py-2 bg-secondary-button text-main-text rounded-lg hover:bg-secondary-button/80 transition-all duration-200 transform hover:scale-105 text-button-md">
                            {{ __('admin/menu.reset') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div x-show="selectedItems.length > 0"
             x-cloak 
             x-transition:enter="transition-all ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition-all ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="bg-sub border border-brand/20 rounded-lg p-4 flex items-center justify-between">
            <span class="text-brand text-body-md font-medium">
                {{ __('admin/menu.items_selected', ['count' => '']) }}<span x-text="selectedItems.length"></span>
            </span>
            <div class="flex gap-2">
                <button @click="toggleStatusSelected(true)" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition-all duration-200 transform hover:scale-105 text-body-md">
                    <i class="fas fa-check mr-1"></i>
                    {{ __('admin/menu.activate') }}
                </button>
                <button @click="toggleStatusSelected(false)" class="px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700 transition-all duration-200 transform hover:scale-105 text-body-md">
                    <i class="fas fa-pause mr-1"></i>
                    {{ __('admin/menu.deactivate') }}
                </button>
                <button @click="deleteSelected()" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition-all duration-200 transform hover:scale-105 text-body-md">
                    <i class="fas fa-trash mr-1"></i>
                    {{ __('admin/menu.delete') }}
                </button>
                <button @click="openReorderModal()" class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 transition-all duration-200 transform hover:scale-105 text-body-md">
                    <i class="fas fa-sort mr-1"></i>
                    {{ __('admin/menu.reorder') }}
                </button>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-disabled/20">
            <div class="p-6 border-b border-disabled/20">
                <div class="flex items-center justify-between">
                    <h3 class="text-heading-lg font-semibold text-main-text">{{ __('admin/menu.menu_list') }}</h3>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" @change="toggleSelectAll($event.target.checked)" 
                               class="rounded border-disabled text-brand focus:ring-brand">
                        <label class="text-body-md text-main-text/70">{{ __('admin/menu.select_all') }}</label>
                    </div>
                </div>
            </div>
            @if($menus->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-sub/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">
                                    <input type="checkbox" @change="toggleSelectAll($event.target.checked)" 
                                           :checked="allItemIds.length > 0 && selectedItems.length === allItemIds.length"
                                           class="rounded border-disabled text-brand focus:ring-brand">
                                </th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">{{ __('admin/menu.th_menu') }}</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">{{ __('admin/menu.th_price') }}</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">{{ __('admin/menu.th_time_required') }}</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">{{ __('admin/menu.th_order') }}</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">{{ __('admin/menu.th_status') }}</th>
                                <th class="px-6 py-3 text-left text-body-md font-medium text-main-text/70 uppercase tracking-wider">{{ __('admin/menu.th_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-disabled/20">
                            @foreach($menus as $menu)
                                <tr class="hover:bg-sub/30 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" value="{{ $menu->id }}" 
                                               @change="toggleSelect({{ $menu->id }}, $event.target.checked)"
                                               class="rounded border-disabled text-brand focus:ring-brand">
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            @if($menu->photo_path)
                                                <img src="{{ asset('storage/' . $menu->photo_path) }}" 
                                                     alt="{{ $menu->name }}" 
                                                     class="w-12 h-12 rounded-lg object-cover">
                                            @else
                                                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white font-bold"
                                                     style="background-color: {{ $menu->color }}">
                                                    {{ substr($menu->name, 0, 2) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-body-md font-medium text-main-text">{{ $menu->name }}</div>
                                                @if($menu->description)
                                                    <div class="text-body-md text-main-text/70 max-w-xs truncate" title="{{ $menu->description }}">
                                                        {{ Str::limit($menu->description, 40) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-body-md font-medium text-main-text">
                                            ¥ {{ number_format($menu->price, 0, '.', ',') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-body-md text-main-text/70">
                                        {{ $menu->required_time }} {{ __('admin/menu.time_unit_minutes') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-body-md text-main-text/70">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-md font-medium bg-disabled/20 text-main-text">
                                            #{{ $menu->display_order }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($menu->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-md font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check text-green-500 mr-1 text-xs"></i>
                                                {{ __('admin/menu.active') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-body-md font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times text-red-500 mr-1 text-xs"></i>
                                                {{ __('admin/menu.inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-body-md font-medium">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.menu.show', $menu->id) }}" 
                                               class="text-brand hover:text-brand/80 transition-all duration-200 transform hover:scale-110" title="{{ __('admin/menu.view_details') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.menu.edit', ['id' => $menu->id]) }}" 
                                               class="text-yellow-600 hover:text-yellow-700 transition-all duration-200 transform hover:scale-110" title="{{ __('admin/menu.edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button onclick="toggleStatus({{ $menu->id }})" 
                                                    class="text-{{ $menu->is_active ? 'red' : 'green' }}-600 hover:text-{{ $menu->is_active ? 'red' : 'green' }}-900 transition-all duration-200 transform hover:scale-110" 
                                                    title="{{ $menu->is_active ? __('admin/menu.deactivate') : __('admin/menu.activate') }}">
                                                <i class="fas fa-{{ $menu->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button @click="deleteSingle({{ $menu->id }})" 
                                                    class="text-red-600 hover:text-red-700 transition-all duration-200 transform hover:scale-110" title="{{ __('admin/menu.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-disabled/20">
                    {{ $menus->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-utensils text-disabled text-6xl mb-4"></i>
                    <h3 class="text-heading-lg font-medium text-main-text mb-2">{{ __('admin/menu.no_menus_title') }}</h3>
                    <p class="text-main-text/70 mb-4 text-body-md">{{ __('admin/menu.no_menus_description') }}</p>
                    <a href="{{ route('admin.menu.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-brand text-white rounded-lg hover:bg-brand/90 transition-all duration-300 transform hover:scale-105 hover:shadow-lg text-button-md font-semibold">
                        <i class="fas fa-plus"></i>
                        {{ __('admin/menu.add_menu') }}
                    </a>
                </div>
            @endif
        </div>

        <div x-show="showDeleteModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <h3 class="text-heading-lg font-medium text-main-text mt-2">{{ __('admin/menu.confirm_deletion_title') }}</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-body-md text-main-text/70" x-text="deleteMessage"></p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteModal = false" 
                                class="px-4 py-2 bg-secondary-button text-main-text text-button-md font-medium rounded-md shadow-sm hover:bg-secondary-button/80 transition-all duration-200">
                            {{ __('admin/menu.cancel') }}
                        </button>
                        <button @click="confirmDelete()" 
                                class="px-4 py-2 bg-red-600 text-white text-button-md font-medium rounded-md shadow-sm hover:bg-red-700 transition-all duration-200">
                            {{ __('admin/menu.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="showReorderModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-10 mx-auto p-5 border max-w-2xl shadow-lg rounded-md bg-white"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">
                <div class="mt-3">
                    <h3 class="text-heading-lg font-medium text-main-text mb-4">{{ __('admin/menu.reorder_title') }}</h3>
                    <p class="text-body-md text-main-text/70 mb-4">{{ __('admin/menu.reorder_description') }}</p>
                    <div class="space-y-2 max-h-96 overflow-y-auto" id="sortable-menu">
                        @foreach($menus as $menu)
                            <div class="flex items-center gap-3 p-3 border border-disabled/20 rounded-lg cursor-move bg-white hover:bg-sub/30 transition-colors" data-id="{{ $menu->id }}">
                                <div class="drag-handle cursor-grab active:cursor-grabbing">
                                    <i class="fas fa-grip-vertical text-main-text/50 hover:text-main-text/70"></i>
                                </div>
                                <div class="flex items-center gap-3 flex-1">
                                    @if($menu->photo_path)
                                        <img src="{{ asset('storage/' . $menu->photo_path) }}" 
                                             alt="{{ $menu->name }}" 
                                             class="w-8 h-8 rounded object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded flex items-center justify-center text-white text-xs font-bold"
                                             style="background-color: {{ $menu->color }}">
                                            {{ substr($menu->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-body-md font-medium text-main-text">{{ $menu->name }}</div>
                                        <div class="text-body-md text-main-text/70">{{ __('admin/menu.current_order', ['order' => $menu->display_order]) }}</div>
                                    </div>
                                </div>
                                <div class="text-body-md text-main-text/50">
                                    <i class="fas fa-arrows-alt-v"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex gap-2 justify-end">
                        <button @click="showReorderModal = false" 
                                class="px-4 py-2 bg-secondary-button text-main-text text-button-md font-medium rounded-md shadow-sm hover:bg-secondary-button/80 transition-all duration-200">
                            {{ __('admin/menu.cancel') }}
                        </button>
                        <button @click="saveReorder()" 
                                class="px-4 py-2 bg-brand text-white text-button-md font-medium rounded-md shadow-sm hover:bg-brand/90 transition-all duration-200">
                            {{ __('admin/menu.save_order') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function menuIndex() {
            return {
                showFilters: false,
                showDeleteModal: false,
                showReorderModal: false,
                selectedItems: [],
                allItemIds: @json($menus->pluck('id')->toArray()),
                deleteTarget: null,
                deleteMessage: '',
                sortableInstance: null,
                translations: {
                    deleteSingleConfirm: "{{ __('admin/menu.delete_single_confirm') }}",
                    deleteMultipleConfirm: "{{ __('admin/menu.delete_multiple_confirm') }}",
                    selectAtLeastOne: "{{ __('admin/menu.js_messages.select_at_least_one') }}",
                    errorStatus: "{{ __('admin/menu.js_messages.error_occurred_status') }}",
                    errorDelete: "{{ __('admin/menu.js_messages.error_occurred_delete') }}",
                    errorReorder: "{{ __('admin/menu.js_messages.error_occurred_reorder') }}",
                },
                init() {
                    this.$nextTick(() => {
                        this.initSortable();
                    });
                },
                initSortable() {
                    const sortableContainer = document.getElementById('sortable-menu');
                    if (sortableContainer && !this.sortableInstance) {
                        this.sortableInstance = window.Sortable.create(sortableContainer, {
                            handle: '.drag-handle',
                            animation: 200,
                            ghostClass: 'sortable-ghost',
                            chosenClass: 'sortable-chosen',
                            dragClass: 'sortable-drag',
                            onStart: function(evt) {
                                evt.item.classList.add('opacity-75');
                            },
                            onEnd: function(evt) {
                                evt.item.classList.remove('opacity-75');
                            }
                        });
                    }
                },
                openReorderModal() {
                    this.showReorderModal = true;
                    this.$nextTick(() => {
                        this.initSortable();
                    });
                },
                toggleSelectAll(checked) {
                    const checkboxes = document.querySelectorAll('input[type="checkbox"][value]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = checked;
                        const id = parseInt(checkbox.value);
                        if (checked && !this.selectedItems.includes(id)) {
                            this.selectedItems.push(id);
                        } else if (!checked) {
                            const index = this.selectedItems.indexOf(id);
                            if (index > -1) {
                                this.selectedItems.splice(index, 1);
                            }
                        }
                    });
                },
                toggleSelect(id, checked) {
                    if (checked && !this.selectedItems.includes(id)) {
                        this.selectedItems.push(id);
                    } else if (!checked) {
                        const index = this.selectedItems.indexOf(id);
                        if (index > -1) {
                            this.selectedItems.splice(index, 1);
                        }
                    }
                },
                deleteSingle(id) {
                    this.deleteTarget = [id];
                    this.deleteMessage = this.translations.deleteSingleConfirm;
                    this.showDeleteModal = true;
                },
                deleteSelected() {
                    if (this.selectedItems.length === 0) return;
                    this.deleteTarget = [...this.selectedItems];
                    this.deleteMessage = this.translations.deleteMultipleConfirm.replace(':count', this.selectedItems.length);
                    this.showDeleteModal = true;
                },
                async toggleStatusSelected(status) {
                    if (this.selectedItems.length === 0) {
                        alert(this.translations.selectAtLeastOne);
                        return;
                    }
                    try {
                        const urlTemplate = '{{ route("admin.menu.toggleStatus", ["id" => "__ID__"]) }}';
                        for (const id of this.selectedItems) {
                            await fetch(urlTemplate.replace('__ID__', id), {
                                method: 'PATCH',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json'
                                }
                            });
                        }
                        window.location.reload();
                    } catch (error) {
                        console.error('Error:', error);
                        alert(this.translations.errorStatus);
                    }
                },
                async confirmDelete() {
                    try {
                        if (this.deleteTarget.length === 1) {
                            const url = '{{ route("admin.menu.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', this.deleteTarget[0]);
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
                                alert(result.message || this.translations.errorDelete);
                            }
                        } else {
                            const response = await fetch('{{ route("admin.menu.bulk-delete") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    ids: this.deleteTarget
                                })
                            });
                            const result = await response.json();
                            if (result.success) {
                                window.location.reload();
                            } else {
                                alert(result.message || this.translations.errorDelete);
                            }
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert(this.translations.errorDelete);
                    }
                    this.showDeleteModal = false;
                },
                async saveReorder() {
                    const sortableItems = document.querySelectorAll('#sortable-menu > div');
                    const orderData = Array.from(sortableItems).map((item, index) => ({
                        id: parseInt(item.dataset.id),
                        display_order: index + 1
                    }));
                    try {
                        const response = await fetch('{{ route("admin.menu.reorder") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                order: orderData
                            })
                        });
                        const result = await response.json();
                        if (result.success) {
                            this.showReorderModal = false;
                            window.location.reload();
                        } else {
                            alert(result.error || this.translations.errorReorder);
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert(this.translations.errorReorder);
                    }
                }
            }
        }

        async function toggleStatus(id) {
            try {
                const url = '{{ route("admin.menu.toggleStatus", ["id" => "__ID__"]) }}'.replace('__ID__', id);
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const result = await response.json();
                if (result.success) {
                    window.location.reload();
                } else {
                    alert(result.error || "{{ __('admin/menu.js_messages.error_occurred_status') }}");
                }
            } catch (error) {
                console.error('Error:', error);
                alert("{{ __('admin/menu.js_messages.error_occurred_status') }}");
            }
        }
    </script>
</x-layouts.app>