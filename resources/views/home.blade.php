<x-layouts.app>
     <div class="bg-white text-gray-900 font-sans leading-relaxed">
        <header class="px-6 md:px-12 py-4 border-b border-gray-200 space-y-4">
            <button class="border border-gray-300 rounded-md px-2 py-1 font-medium hover:bg-gray-100 transition">Takanawa Gateway City</button>
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-12 text-xs select-none whitespace-nowrap">
                    <div class="flex items-center gap-3 text-gray-700 cursor-default select-text">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo for X Change Tire Installation Reservation" class="w-8 h-8" onerror="this.style.display='none'" />
                        <span class="text-base font-semibold">Tire Installation Reservation</span>
                    </div>
                </div>
                <nav class="mt-4 md:mt-0 flex gap-6 text-gray-500">
                    <button aria-label="Calendar" class="hover:text-[#4abaa7] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" role="img" aria-hidden="true">
                            <title>Calendar Icon</title>
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                    </button>
                    <button aria-label="Email" class="hover:text-[#4abaa7] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" role="img" aria-hidden="true">
                            <title>Email Icon</title>
                            <path d="M4 4h16v16H4z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </button>
                    <button aria-label="User" class="hover:text-[#4abaa7] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" role="img" aria-hidden="true">
                            <title>User Icon</title>
                            <circle cx="12" cy="7" r="4"></circle>
                            <path d="M5.5 21a7 7 0 0 1 13 0"></path>
                        </svg>
                    </button>
                </nav>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-6 md:px-12 py-8">
            <section aria-label="Main Image and Introduction" class="mb-8 rounded-lg overflow-hidden shadow-lg">
                <img src="{{ asset('images/hero.bmp') }}" alt="Close-up of a car wheel illuminated by the sunset" class="w-full h-auto object-cover object-center" onerror="this.style.display='none'" />
            </section>

            <div class="grid md:grid-cols-[250px_1fr] gap-10">
                <aside class="sticky top-5 self-start text-xs md:text-sm text-gray-700 border-r border-gray-200 pr-6 space-y-6">
                    <div>
                        <p class="font-semibold mb-1">Location</p>
                        <p>2095-8 Miyadera, Iruma-shi, Saitama</p>
                    </div>
                    <div>
                        <p class="font-semibold mb-1">Business Hours</p>
                        <ul class="space-y-1 leading-tight">
                            <li>Mon 10:00 - 18:00</li>
                            <li>Tue 10:00 - 18:00</li>
                            <li>Wed Closed</li>
                            <li>Thu Closed</li>
                            <li>Fri 10:00 - 18:00</li>
                            <li>Sat 10:00 - 18:00</li>
                            <li>Sun 10:00 - 18:00</li>
                        </ul>
                    </div>
                    <nav class="pt-4 border-t border-gray-200 space-y-1">
                        <a href="#" class="block text-gray-600 hover:text-green-600 transition">About Us</a>
                        <a href="#" class="block text-gray-600 hover:text-green-600 transition">Contact Us</a>
                        <a href="#" class="block text-gray-600 hover:text-green-600 transition">Terms of Service</a>
                    </nav>
                </aside>

                {{-- <section aria-label="List of Bookable Services" class="space-y-6">
                    @foreach ($services as $service)
                    <article class="border border-gray-200 rounded-lg p-4 flex items-center shadow-sm hover:shadow-md transition cursor-pointer">
                        <div class="flex items-start justify-between gap-4 w-full">
                            <div class="space-y-4">
                                <h2 class="text-base font-semibold text-gray-900 mb-1">{{ $service->label() }}</h2>
                                <p class="text-xs text-gray-500">{{ $service->time() }} min</p>
                            </div>
                            <div class="flex flex-col justify-between items-end gap-8">
                                <button aria-label="View Details" class="text-gray-400 hover:text-[#4abaa7] transition" title="View Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" role="img" aria-hidden="true">
                                        <title>Details Icon</title>
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                </button>
                                <button class="rounded-full bg-[#4abaa7] hover:bg-green-600 text-white text-sm font-semibold px-5 py-2 transition" type="button">Book</button>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </section> --}}
            </div>
        </main>

        <footer class="text-center text-xs text-gray-500 py-6 border-t border-gray-200 px-6 md:px-12 flex flex-col md:flex-row justify-between items-center gap-4 md:gap-0">
            <div class="space-x-2">
                <a href="#" class="hover:text-green-600 transition">RESERVATION Terms of Service</a>
                <span>|</span>
                <a href="#" class="hover:text-green-600 transition">RESERVATION Privacy Policy</a>
            </div>
            <div class="flex items-center gap-3 bg-white border border-gray-300 rounded-lg px-3 py-1 shadow-sm select-none">
                <span class="text-gray-700 text-xs font-semibold">Free Reservation System</span>
            </div>
        </footer>
    </div>
</x-layouts.app>