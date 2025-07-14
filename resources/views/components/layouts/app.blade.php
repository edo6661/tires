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
    <div class="flex min-h-screen relative">
        <x-layouts.header.admin-sidebar/>
        <div class="flex-1 flex flex-col">
            <x-layouts.header.customer-nav />
            <main class="flex-1 w-full mx-auto px-2 md:px-4 py-8">
                {{ $slot }}
            </main>
            <x-layouts.footer.customer-footer />
        </div>
    </div>

    
</body>
</html>