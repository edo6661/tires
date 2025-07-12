@auth
    @if(auth()->user()->isAdmin())
        <div x-data="sidebar()" class="bg-white border-r border-gray-200 flex flex-col sticky top-0 h-screen transition-all duration-300 ease-in-out" :class="isExpanded ? 'w-72' : 'w-20'">
            <div class="p-4 border-b border-gray-200">
                <button @click="toggle()" class="w-full flex items-center justify-center p-2 text-gray-600 hover:bg-gray-100 rounded-md transition-colors duration-200">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>

            <div class="p-4 overflow-y-auto flex-1">
                <div class="mb-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-gray-700 hover:bg-gray-50 p-2 rounded-md transition-colors duration-200">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Admin Logo" class="object-contain w-8 h-8 rounded flex-shrink-0" />
                        <span class="text-base font-semibold whitespace-nowrap overflow-hidden transition-all duration-300" 
                                :class="isExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">Dashboard</span>
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
    @endif
@endauth
<script>
    function sidebar() {
    return {
        isExpanded: true,
        
        toggle() {
            this.isExpanded = !this.isExpanded;
            if (!this.isExpanded) {
                document.querySelectorAll('[x-data*="dropdown"]').forEach(el => {
                    if (el.__x) {
                        el.__x.$data.isOpen = false;
                    }
                });
            }
        }
    }
}

function dropdown(type = 'reservation') {
    return {
        isOpen: false,
        items: [],
        
        init() {
            this.setItems(type);
        },
        
        toggle() {
            this.isOpen = !this.isOpen;
        },
        
        close() {
            this.isOpen = false;
        },
        
        setItems(type) {
            const currentRoute = window.location.pathname;
            
            const dropdownItems = {
                reservation: [
                    { 
                        id: 1, 
                        name: 'Calendar', 
                        icon: 'fas fa-calendar-alt', 
                        url: '{{ route('admin.reservation.calendar') }}',
                        isActive: currentRoute.includes('admin/reservation/calendar')
                    },
                    { 
                        id: 3, 
                        name: 'Blocked', 
                        icon: 'fas fa-ban', 
                        url: '{{ route('admin.reservation.block') }}',
                        isActive: currentRoute.includes('admin/reservation/block')
                    },
                    { 
                        id: 4, 
                        name: 'Availability', 
                        icon: 'fas fa-check-circle', 
                        url: '{{ route('admin.reservation.availability') }}',
                        isActive: currentRoute.includes('admin/reservation/availability')
                    }
                ],
                customer: [
                    { 
                        id: 1, 
                        name: 'Contact', 
                        icon: 'fa-solid fa-address-book', 
                        url: '#customer-list',
                        isActive: currentRoute.includes('customer-list')
                    },
                    { 
                        id: 2, 
                        name: 'Announcement', 
                        icon: 'fas fa-bullhorn', 
                        url: '#announcements',
                        isActive: currentRoute.includes('announcements')
                    },
                ],
                settings: [
                    { 
                        id: 1, 
                        name: 'Business Information', 
                        icon: 'fa-solid fa-store', 
                        url: '#business-info',
                        isActive: currentRoute.includes('business-info')
                    },
                    { 
                        id: 2, 
                        name: 'Menu', 
                        icon: 'fa-solid fa-book-open', 
                        url: '#menu-registration',
                        isActive: currentRoute.includes('menu-registration')
                    }
                ]
            };
            
            this.items = dropdownItems[type] || [];
        }
    }
}
</script>