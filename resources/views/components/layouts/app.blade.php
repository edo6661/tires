<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Reservation</title>

    {{-- Favicon Links --}}
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="font-en">
    {{-- <p>
        {{ __('app.dashboard') }}
    </p> --}}
    <div class="relative" x-data="{ sidebarOpen: false }">
        @auth
            @if (auth()->user()->isAdmin())
                <x-layouts.header.admin-nav />
            @else
                <x-layouts.header.customer-nav />
            @endif
        @endauth
        @guest
            <x-layouts.header.customer-nav />
        @endguest
        @auth
            @if (auth()->user()->isAdmin())
                <x-layouts.sidebar.admin-sidebar />
            @endif
        @endauth
        <div class="flex-1 flex flex-col">
            <main class="flex-1 w-full mx-auto min-h-screen">
                {{ $slot }}
            </main>
            <x-layouts.footer.customer-footer />
        </div>
    </div>

    {{-- Tambahkan script ini untuk meneruskan terjemahan ke JS --}}
    <script>
        window.translations = {
            sidebar: @json(__('admin/sidebar'))
        };
    </script>

    <script>
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
                    const trans = window.translations.sidebar;

                    const dropdownItems = {
                        reservation: [{
                                id: 1,
                                name: trans.reservation.items.calendar,
                                icon: 'fas fa-calendar-alt',
                                url: '{{ route('admin.reservation.calendar') }}',
                                isActive: currentRoute.includes('admin/reservation/calendar')
                            },
                            {
                                id: 3,
                                name: trans.reservation.items.blocked,
                                icon: 'fas fa-ban',
                                url: '{{ route('admin.blocked-period.index') }}',
                                isActive: currentRoute.includes('admin/blocked-period')
                            },
                            {
                                id: 4,
                                name: trans.reservation.items.availability,
                                icon: 'fas fa-check-circle',
                                url: '{{ route('admin.reservation.viewAvailability') }}',
                                isActive: currentRoute.includes('admin/reservation/availability')
                            }
                        ],
                        customer: [{
                                id: 1,
                                name: trans.customer_support.items.contact,
                                icon: 'fa-solid fa-address-book',
                                url: '{{ route('admin.contact.index') }}',
                                isActive: currentRoute.includes('admin/contact')
                            },
                            {
                                id: 2,
                                name: trans.customer_support.items.announcement,
                                icon: 'fas fa-bullhorn',
                                url: '{{ route('admin.announcement.index') }}',
                                isActive: currentRoute.includes('admin/announcement')
                            },
                        ],
                        settings: [{
                                id: 1,
                                name: trans.settings.items.business_information,
                                icon: 'fa-solid fa-store',
                                url: '{{ route('admin.business-setting.index') }}',
                                isActive: currentRoute.includes('admin/business-setting')
                            },
                            {
                                id: 2,
                                name: trans.settings.items.menu,
                                icon: 'fa-solid fa-book-open',
                                url: '{{ route('admin.menu.index') }}',
                                isActive: currentRoute.includes('admin/menu')
                            }
                        ]
                    };
                    this.items = dropdownItems[type] || [];
                }
            }
        }
    </script>
</body>

</html>
