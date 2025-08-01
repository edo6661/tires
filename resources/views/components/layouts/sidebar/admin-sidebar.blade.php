<div x-show="sidebarOpen"
     x-transition:enter="transition ease-in-out duration-300 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in-out duration-300 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed left-0 top-0 bottom-0 w-72 bg-white border-r border-disabled flex flex-col z-50 mt-[73px] shadow-lg"
     x-cloak
     >
    <div class="p-4 overflow-y-auto flex-1">
        <div class="mb-4">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-3 text-main-text hover:bg-sub hover:text-brand p-2 rounded-md transition-all duration-300 hover:shadow-sm hover:scale-[1.02] transform">
                <img src="{{ asset('images/logo.jpg') }}" alt="Admin Logo" class="object-contain w-8 h-8 rounded flex-shrink-0" />
                <span class="text-base font-semibold whitespace-nowrap">{{ __('admin/sidebar.dashboard') }}</span>
            </a>
        </div>
        <nav class="flex-1 space-y-2">
            <div class="space-y-1">
                <x-layouts.header.admin-dropdown-menu
                    type="reservation"
                    icon="fas fa-calendar-alt"
                    title="{{ __('admin/sidebar.reservation.title') }}"
                    {{-- :is-active="request()->routeIs('admin.reservation.*')" --}}
                />
                <x-layouts.header.admin-dropdown-menu
                    type="customer"
                    icon="fas fa-headset"
                    title="{{ __('admin/sidebar.customer_support.title') }}"
                    {{-- :is-active="request()->routeIs('admin.customer.*')" --}}
                />
                <x-layouts.header.admin-dropdown-menu
                    type="settings"
                    icon="fas fa-cog"
                    title="{{ __('admin/sidebar.settings.title') }}"
                    {{-- :is-active="request()->routeIs('admin.settings.*')" --}}
                />
                <x-layouts.header.admin-dropdown-menu-item
                    icon="fas fa-users"
                    title="{{ __('admin/sidebar.customer_management') }}"
                    href="{{ route('admin.customer.index') }}"
                    :is-active="request()->routeIs('admin.customer.*')"
                />
                {{-- <x-layouts.header.admin-dropdown-menu-item
                    icon="fas fa-tree"
                    title="{{ __('admin/sidebar.tire_management') }}"
                    href="{{ route('admin.tire-storage.index') }}"
                    :is-active="request()->routeIs('admin.tire-storage.*')"
                />
                <x-layouts.header.admin-dropdown-menu-item
                    icon="fas fa-chart-bar"
                    title="{{ __('admin/sidebar.aggregation_analysis') }}"
                    href="#"
                    :is-active="request()->routeIs('admin.analytics')"
                /> --}}
            </div>
        </nav>
    </div>
</div>