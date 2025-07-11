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
    <header class="px-6 md:px-12 py-4 border-b border-gray-200 space-y-4 bg-white">
        <button class="border border-gray-300 rounded-md px-2 py-1 font-medium hover:bg-gray-100 transition text-sm">Takanawa Gateway City</button>
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-12 text-xs select-none whitespace-nowrap">
                <a href="{{ route('home') }}" class="flex items-center gap-3 text-gray-700  select-text">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo for X Change Tire Installation Reservation" class="object-cover w-16" />
                    <span class="text-base font-semibold">Tire Installation Reservation</span>
                </a>
            </div>
            <nav class="mt-4 md:mt-0 flex gap-6">
                 <x-shared.link-hint-icon 
                    label="Calendar" 
                    icon="fa-solid fa-calendar-days" 
                    position="bottom"
                    href="/calendar"
                    activePath="calendar*"
                />
                <x-shared.link-hint-icon 
                    label="Inquiry" 
                    icon="fa-solid fa-envelope" 
                    position="bottom"
                    href="/inquiry"
                    activePath="inquiry*"
                />
                <x-shared.link-hint-icon 
                    label="Settings" 
                    icon="fa-solid fa-gear" 
                    position="bottom"
                    href="/settings"
                    activePath="settings*"
                />
                @guest
                    <x-shared.link-hint-icon 
                        label="Login" 
                        icon="fa-solid fa-user" 
                        position="bottom"
                        href="/login"
                        activePath="login*"
                    />
                @endguest
                @auth
                     <x-shared.form-hint-icon
                        label="Logout"
                        icon="fa-solid fa-right-from-bracket"  
                        position="bottom"
                        action="{{ route('logout') }}"
                    />
                @endauth
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 md:px-12 py-8">
        {{ $slot }}
    </main>

    <footer class="text-center text-xs text-gray-500 py-6 border-t border-gray-200 px-6 md:px-12 flex flex-col md:flex-row justify-between items-center gap-4 md:gap-0 bg-white">
        <div class="space-x-2">
            <a href="#" class="hover:text-green-600 transition">RESERVATION Terms of Service</a>
            <span>|</span>
            <a href="#" class="hover:text-green-600 transition">RESERVATION Privacy Policy</a>
        </div>
        <div class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-3 py-1 shadow-sm select-none">
            <span class="text-gray-700 text-xs font-semibold">Free Reservation System</span>
        </div>
    </footer>
</body>
</html>