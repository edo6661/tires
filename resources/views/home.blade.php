<x-layouts.app>
    <section aria-label="Main Image and Introduction" class="mb-8 rounded-lg overflow-hidden shadow-lg">
        @if($businessSettings && $businessSettings->top_image_path)
        <img src="{{ $businessSettings->path_top_image_url }}" alt="{{ $businessSettings->shop_name ?? 'Shop Image' }}" class="w-full max-h-[420px] object-cover object-center"/>
        @else
            <img src="{{ asset('images/hero.jpg') }}" alt="Close-up of a car wheel illuminated by the sunset" class="w-full h-auto object-cover object-center"/>
        @endif
    </section>

    <div class="grid md:grid-cols-[250px_1fr] gap-10">
        <aside class="sticky top-5 self-start text-xs md:text-sm text-gray-700 md:border-r md:border-gray-200 md:pr-6 space-y-6">
            <div>
                <p class="font-semibold mb-1">Location</p>
                <p>{{ $businessSettings->address ?? '2095-8 Miyadera, Iruma-shi, Saitama' }}</p>
            </div>
            <div>
                <p class="font-semibold mb-1">Business Hours</p>
                <ul class="space-y-1 leading-tight">
                    @if($businessSettings && $businessSettings->business_hours)
                        @php
                            $days = [
                                'monday' => 'Mon',
                                'tuesday' => 'Tue', 
                                'wednesday' => 'Wed',
                                'thursday' => 'Thu',
                                'friday' => 'Fri',
                                'saturday' => 'Sat',
                                'sunday' => 'Sun'
                            ];
                        @endphp
                        @foreach($days as $day => $shortDay)
                            @php
                                $hours = $businessSettings->business_hours[$day] ?? null;
                            @endphp
                            <li>
                                {{ $shortDay }} 
                                @if($hours && isset($hours['closed']) && $hours['closed'])
                                    Closed
                                @elseif($hours && isset($hours['open']) && isset($hours['close']))
                                    {{ $hours['open'] }} - {{ $hours['close'] }}
                                @else
                                    Closed
                                @endif
                            </li>
                        @endforeach
                    @else
                        <li>Mon 10:00 - 18:00</li>
                        <li>Tue 10:00 - 18:00</li>
                        <li>Wed Closed</li>
                        <li>Thu Closed</li>
                        <li>Fri 10:00 - 18:00</li>
                        <li>Sat 10:00 - 18:00</li>
                        <li>Sun 10:00 - 18:00</li>
                    @endif
                </ul>
            </div>
            @if($businessSettings && $businessSettings->phone_number)
                <div>
                    <p class="font-semibold mb-1">Phone</p>
                    <p>{{ $businessSettings->phone_number }}</p>
                </div>
            @endif
            <nav class="pt-4 border-t border-gray-200 space-y-1">
                <a href="#" class="block text-gray-600 hover:text-green-600 transition">About Us</a>
                <a href="#" class="block text-gray-600 hover:text-green-600 transition">Contact Us</a>
                <a href="#" class="block text-gray-600 hover:text-green-600 transition">Terms of Service</a>
            </nav>
        </aside>

        <section aria-label="List of Bookable Services" class="space-y-6">
            @if($menus && $menus->count() > 0)
                @foreach ($menus as $menu)
                <article class="border border-gray-200 rounded-lg p-4 flex items-center shadow-sm hover:shadow-md transition cursor-pointer bg-white">
                    <div class="flex items-start justify-between gap-4 w-full">
                        <div class="space-y-4">
                            <h2 class="text-base font-semibold text-gray-900 mb-1">{{ $menu->name }}</h2>
                            <p class="text-xs text-gray-500">{{ $menu->required_time }} min</p>
                            @if($menu->description)
                                <p class="text-xs text-gray-600">{{ Str::limit($menu->description, 100) }}</p>
                            @endif
                            {{-- @if($menu->price)
                                <p class="text-sm font-medium text-gray-900">¥{{ number_format($menu->price) }}</p>
                            @endif --}}
                        </div>
                        <div class="flex flex-col justify-between items-end gap-4">
                            <button aria-label="View Details" class="text-gray-400 hover:text-[#4abaa7] transition" title="View Details">
                               <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                            </button>
                            <a href="{{ route('booking.first-step', ['menuId'=>$menu->id]) }}" class="block rounded-full bg-[#4abaa7] hover:bg-green-600 text-white text-sm font-semibold px-5 py-2 transition" type="button">Book</a>
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
                            <a href="#" class="block rounded-full bg-[#4abaa7] hover:bg-green-600 text-white text-sm font-semibold px-5 py-2 transition" type="button">Book</a>
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
            @endif
        </section>
    </div>
</x-layouts.app>