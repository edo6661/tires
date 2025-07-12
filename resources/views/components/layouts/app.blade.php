<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservation</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="bg-gray-50 font-sans">
    <div class="flex min-h-screen">
        <x-layouts.header.admin-sidebar/>
        <div class="flex-1 flex flex-col">
            <x-layouts.header.customer-nav />
            <main class="flex-1 max-w-7xl mx-auto px-6 md:px-12 py-8 min-h-[200vh]">
                {{ $slot }}
            </main>
            <x-layouts.footer.customer-footer />
        </div>
    </div>

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
                    const dropdownItems = {
                        reservation: [
                            { id: 1, name: 'Calendar', icon: 'fas fa-calendar-alt', url: '#calendar' },
                            { id: 2, name: 'List', icon: 'fas fa-list', url: '#list' },
                            { id: 3, name: 'Blocked', icon: 'fas fa-ban', url: '#block' },
                            { id: 4, name: 'Availability', icon: 'fas fa-check-circle', url: '#availability' }
                        ],
                        customer: [
                            { id: 1, name: 'Contact', icon: 'fa-solid fa-address-book', url: '#customer-list' },
                            { id: 2, name: 'Announcement', icon: 'fas fa-bullhorn', url: '#announcements' },
                        ],
                        settings: [
                            { id: 1, name: 'Business Information', icon: 'fa-solid fa-store', url: '#business-info' },
                            { id: 2, name: 'Menu', icon: 'fa-solid fa-book-open', url: '#menu-registration' }
                        ]
                    };
                    
                    this.items = dropdownItems[type] || [];
                }
            }
        }
    </script>
</body>
</html>