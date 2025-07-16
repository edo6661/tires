
<x-layouts.app>
    <section aria-label="Main Image and Introduction" class="mb-8 rounded-lg overflow-hidden shadow-lg">
        <img src="{{ asset('images/hero.jpg') }}" alt="Close-up of a car wheel illuminated by the sunset" class="w-full h-auto object-cover object-center"/>
    </section>

    <div class="grid md:grid-cols-[250px_1fr] gap-10">
        <aside class="sticky top-5 self-start text-xs md:text-sm text-gray-700 md:border-r md:border-gray-200 md:pr-6 space-y-6">
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

        <section aria-label="List of Bookable Services" class="space-y-6">
            @isset($menus)
            @foreach ($menus as $menu)
            <article class="border border-gray-200 rounded-lg p-4 flex items-center shadow-sm hover:shadow-md transition cursor-pointer bg-white">
                <div class="flex items-start justify-between gap-4 w-full">
                    <div class="space-y-4">
                        <h2 class="text-base font-semibold text-gray-900 mb-1">{{ $menu->name }}</h2>
                        <p class="text-xs text-gray-500">{{ $menu->required_time }} min</p>
                    </div>
                    <div class="flex flex-col justify-between items-end gap-4">
                        <button aria-label="View Details" class="text-gray-400 hover:text-[#4abaa7] transition" title="View Details">
                           <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        </button>
                        <button class="rounded-full bg-[#4abaa7] hover:bg-green-600 text-white text-sm font-semibold px-5 py-2 transition" type="button">Book</button>
                    </div>
                </div>
            </article>
            @endforeach
            @else
            <article class="border border-gray-200 rounded-lg p-4 flex items-center shadow-sm hover:shadow-md transition cursor-pointer bg-white">
                <div class="flex items-start justify-between gap-4 w-full">
                    <div class="space-y-4">
                        <h2 class="text-base font-semibold text-gray-900 mb-1">Standard Tire Change</h2>
                        <p class="text-xs text-gray-500">60 min</p>
                    </div>
                    <div class="flex flex-col justify-between items-end gap-4">
                        <button aria-label="View Details" class="text-gray-400 hover:text-[#4abaa7] transition" title="View Details">
                           <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        </button>
                        <button class="rounded-full bg-[#4abaa7] hover:bg-green-600 text-white text-sm font-semibold px-5 py-2 transition" type="button">Book</button>
                    </div>
                </div>
            </article>
            <article class="border border-gray-200 rounded-lg p-4 flex items-center shadow-sm hover:shadow-md transition cursor-pointer bg-white">
                <div class="flex items-start justify-between gap-4 w-full">
                    <div class="space-y-4">
                        <h2 class="text-base font-semibold text-gray-900 mb-1">Tire Rotation</h2>
                        <p class="text-xs text-gray-500">30 min</p>
                    </div>
                    <div class="flex flex-col justify-between items-end gap-4">
                        <button aria-label="View Details" class="text-gray-400 hover:text-[#4abaa7] transition" title="View Details">
                           <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                        </button>
                        <button class="rounded-full bg-[#4abaa7] hover:bg-green-600 text-white text-sm font-semibold px-5 py-2 transition" type="button">Book</button>
                    </div>
                </div>
            </article>
            @endisset
        </section>
    </div>
</x-layouts.app>
