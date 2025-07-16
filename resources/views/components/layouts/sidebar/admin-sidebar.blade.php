<div x-show="sidebarOpen"
     x-transition:enter="transition ease-in-out duration-300 transform"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in-out duration-300 transform"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed left-0 top-0 bottom-0 w-72 bg-white border-r border-gray-200 flex flex-col z-50 mt-[73px]"
     x-cloak
     >
    <div class="p-4 overflow-y-auto flex-1">
        <div class="mb-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-gray-700 hover:bg-gray-50 p-2 rounded-md transition-colors duration-200">
                <img src="{{ asset('images/logo.jpg') }}" alt="Admin Logo" class="object-contain w-8 h-8 rounded flex-shrink-0" />
                <span class="text-base font-semibold whitespace-nowrap">Dashboard</span>
            </a>
        </div>
        <nav class="flex-1 space-y-2">
            <div class="space-y-1">
                <x-layouts.header.admin-dropdown-menu
                    type="reservation" 
                    icon="fas fa-calendar-alt" 
                    title="Reservation Management"
                    :is-active="request()->routeIs('admin.reservation.*')"
                />
                <x-layouts.header.admin-dropdown-menu
                    type="customer" 
                    icon="fas fa-headset" 
                    title="Customer Support"
                    :is-active="request()->routeIs('admin.customer.*')"
                />
                <x-layouts.header.admin-dropdown-menu
                    type="settings" 
                    icon="fas fa-cog" 
                    title="Settings"
                    :is-active="request()->routeIs('admin.settings.*')"
                />
                <x-layouts.header.admin-dropdown-menu-item
                    icon="fas fa-users" 
                    title="Customer List"
                    href="#"
                    :is-active="request()->routeIs('admin.customers')"
                />
                <x-layouts.header.admin-dropdown-menu-item
                    icon="fas fa-chart-bar" 
                    title="Aggregation / Analysis"
                    href="#"
                    :is-active="request()->routeIs('admin.analytics')"
                />
            </div>
        </nav>
    </div>
</div>