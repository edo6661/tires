<x-layouts.app>
    <div class="container space-y-6" x-data="menuShow()">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <a href="{{ route('admin.menu.index') }}" class="text-main-text/70 hover:text-link transition-colors duration-200">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h1 class="text-title-lg font-bold text-main-text">{{ __('admin/menu.show_title') }}</h1>
                </div>
                <p class="text-main-text/70 text-body-md">{{ __('admin/menu.show_subtitle', ['name' => $menu->name]) }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.menu.edit', $menu->id) }}"
                   class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center gap-2 text-button-md font-semibold">
                    <i class="fas fa-edit"></i>
                    {{ __('admin/menu.edit_menu_button') }}
                </a>
                <a href="{{ route('admin.menu.index') }}"
                   class="px-4 py-2 bg-secondary-button text-main-text rounded-lg hover:bg-secondary-button/80 transition-all duration-300 transform hover:scale-105 flex items-center gap-2 text-button-md">
                    <i class="fas fa-list"></i>
                    {{ __('admin/menu.back_to_list_button') }}
                </a>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 space-y-6 border border-disabled/20 hover:shadow-md transition-all duration-300">
                    <div class="text-center">
                        @if($menu->photo_path)
                            <img src="{{ asset('storage/' . $menu->photo_path) }}"
                                 alt="{{ $menu->name }}"
                                 class="w-full max-w-sm mx-auto rounded-lg object-cover shadow-md">
                        @else
                            <div class="w-32 h-32 mx-auto rounded-lg flex items-center justify-center text-white font-bold text-4xl shadow-md"
                                 style="background-color: {{ $menu->color }}">
                                {{ substr($menu->name, 0, 2) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex justify-center">
                        @if($menu->is_active)
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-body-md font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                {{ __('admin/menu.status_active') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-body-md font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                {{ __('admin/menu.status_inactive') }}
                            </span>
                        @endif
                    </div>
                    <div class="flex flex-col gap-2">
                        <button onclick="toggleStatus({{ $menu->id }})"
                                class="w-full px-4 py-2 text-white rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center justify-center gap-2 text-button-md font-semibold {{ $menu->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                            <i class="fas fa-{{ $menu->is_active ? 'pause' : 'play' }}"></i>
                            {{ $menu->is_active ? __('admin/menu.deactivate_menu') : __('admin/menu.activate_menu') }}
                        </button>
                        <button @click="showDeleteModal()"
                                class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300 transform hover:scale-105 hover:shadow-lg flex items-center justify-center gap-2 text-button-md font-semibold">
                            <i class="fas fa-trash"></i>
                            {{ __('admin/menu.delete_menu_button') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300">
                    <h3 class="text-heading-lg font-semibold text-main-text mb-6">{{ __('admin/menu.info_title') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-body-md font-medium text-main-text/70 mb-1">{{ __('admin/menu.info_name') }}</label>
                                <p class="text-heading-lg font-semibold text-main-text">{{ $menu->name }}</p>
                            </div>
                            <div>
                                <label class="block text-body-md font-medium text-main-text/70 mb-1">{{ __('admin/menu.info_price') }}</label>
                                <p class="text-title-md font-bold text-green-600">
                                    ¥ {{ number_format($menu->price, 0, '.', ',') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-body-md font-medium text-main-text/70 mb-1">{{ __('admin/menu.info_time') }}</label>
                                <p class="text-heading-md text-main-text flex items-center gap-2">
                                    <i class="fas fa-clock text-brand"></i>
                                    {{ $menu->required_time }} {{ __('admin/menu.time_unit_minutes') }}
                                </p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-body-md font-medium text-main-text/70 mb-1">{{ __('admin/menu.info_order') }}</label>
                                <p class="text-heading-md text-main-text">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-body-md font-medium bg-disabled/20 text-main-text">
                                        #{{ $menu->display_order }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <label class="block text-body-md font-medium text-main-text/70 mb-1">{{ __('admin/menu.info_color') }}</label>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full border-2 border-disabled"
                                         style="background-color: {{ $menu->color }}"></div>
                                    <p class="text-main-text font-mono text-body-md">{{ $menu->color }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-body-md font-medium text-main-text/70 mb-1">{{ __('admin/menu.info_created_at') }}</label>
                                <p class="text-main-text text-body-md">
                                    {{ $menu->created_at->format('F d, Y, H:i') }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-body-md font-medium text-main-text/70 mb-1">{{ __('admin/menu.info_updated_at') }}</label>
                                <p class="text-main-text text-body-md">
                                    {{ $menu->updated_at->format('F d, Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($menu->description)
        <div class="bg-white rounded-lg shadow-sm p-6 border border-disabled/20 hover:shadow-md transition-all duration-300">
            <h3 class="text-heading-lg font-semibold text-main-text mb-4">{{ __('admin/menu.description_title') }}</h3>
            <div class="prose max-w-none">
                <p class="text-main-text/70 leading-relaxed text-body-md">{{ $menu->description }}</p>
            </div>
        </div>
        @endif

        <div x-show="showDeleteConfirm"
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
                    <h3 class="text-heading-lg font-medium text-main-text mt-2">{{ __('admin/menu.delete_modal_title') }}</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-body-md text-main-text/70">
                            {!! __('admin/menu.delete_modal_text', ['name' => $menu->name]) !!}
                        </p>
                    </div>
                    <div class="items-center px-4 py-3 flex gap-2 justify-center">
                        <button @click="showDeleteConfirm = false"
                                class="px-4 py-2 bg-secondary-button text-main-text text-button-md font-medium rounded-md shadow-sm hover:bg-secondary-button/80 transition-all duration-200">
                            {{ __('admin/menu.cancel') }}
                        </button>
                        <button @click="confirmDelete()"
                                :disabled="isDeleting"
                                class="px-4 py-2 bg-red-600 text-white text-button-md font-medium rounded-md shadow-sm hover:bg-red-700 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isDeleting" class="flex items-center gap-2">
                                <i class="fas fa-trash"></i>
                                {{ __('admin/menu.delete') }}
                            </span>
                            <span x-show="isDeleting" class="flex items-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i>
                                {{ __('admin/menu.deleting_text') }}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function menuShow() {
            return {
                showDeleteConfirm: false,
                isDeleting: false,
                showDeleteModal() {
                    this.showDeleteConfirm = true;
                },
                async confirmDelete() {
                    this.isDeleting = true;
                    try {
                        const url = '{{ route("admin.menu.destroy", ["id" => "__ID__"]) }}'.replace('__ID__', {{ $menu->id }});
                        const response = await fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.showDeleteConfirm = false;
                            const successDiv = document.createElement('div');
                            successDiv.className = 'fixed top-4 right-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50';
                            successDiv.innerHTML = `
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i>
                                    {{ __('admin/menu.js_messages.delete_success') }}
                                </div>
                            `;
                            document.body.appendChild(successDiv);
                            setTimeout(() => {
                                window.location.href = '{{ route("admin.menu.index") }}';
                            }, 1500);
                        } else {
                            throw new Error(data.message || "{{ __('admin/menu.js_messages.delete_failed') }}");
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'fixed top-4 right-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50';
                        errorDiv.innerHTML = `
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>${error.message || "{{ __('admin/menu.js_messages.error_occurred_delete') }}"}</span>
                                </div>
                                <button onclick="this.parentElement.parentElement.remove()" class="text-red-700 hover:text-red-900 ml-4">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                        document.body.appendChild(errorDiv);
                        setTimeout(() => {
                            if (errorDiv.parentNode) {
                                errorDiv.remove();
                            }
                        }, 5000);
                        this.showDeleteConfirm = false;
                    } finally {
                        this.isDeleting = false;
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