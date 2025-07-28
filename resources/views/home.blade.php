<x-layouts.app>
    <div class="container">
        <section aria-label="Main Image and Introduction" class="mb-12 rounded-lg overflow-hidden shadow-lg">
            <img src="{{ asset('images/hero.jpg') }}" alt="Close-up of a car wheel illuminated by the sunset"
                class="w-full h-auto object-cover object-center" />
        </section>
        <div class="grid md:grid-cols-[250px_1fr] gap-10">
            <aside class="sm:sticky top-8 self-start text-main-text md:border-r md:border-disabled/50 md:pr-6 space-y-8">
                <div>
                    <p class="text-heading-md font-semibold mb-2 text-brand">Location</p>
                    <p class="text-body-md">{{ $businessSettings->address ?? '2095-8 Miyadera, Iruma-shi, Saitama' }}</p>
                </div>
                <div>
                    <p class="text-heading-md font-semibold mb-2 text-brand">Business Hours</p>
                    <ul class="space-y-1 leading-tight text-body-md">
                        @if ($businessSettings && $businessSettings->business_hours)
                            @php
                                $days = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri', 'saturday' => 'Sat', 'sunday' => 'Sun'];
                            @endphp
                            @foreach ($days as $day => $shortDay)
                                @php
                                    $hours = $businessSettings->business_hours[$day] ?? null;
                                @endphp
                                <li>
                                    <span class="font-medium w-8 inline-block">{{ $shortDay }}</span>
                                    @if ($hours && isset($hours['closed']) && $hours['closed'])
                                        Closed
                                    @elseif($hours && isset($hours['open']) && isset($hours['close']))
                                        {{ $hours['open'] }} - {{ $hours['close'] }}
                                    @else
                                        Closed
                                    @endif
                                </li>
                            @endforeach
                        @else
                            <li><span class="font-medium w-8 inline-block">Mon</span> 10:00 - 18:00</li>
                            <li><span class="font-medium w-8 inline-block">Tue</span> 10:00 - 18:00</li>
                            <li><span class="font-medium w-8 inline-block">Wed</span> Closed</li>
                            <li><span class="font-medium w-8 inline-block">Thu</span> Closed</li>
                            <li><span class="font-medium w-8 inline-block">Fri</span> 10:00 - 18:00</li>
                            <li><span class="font-medium w-8 inline-block">Sat</span> 10:00 - 18:00</li>
                            <li><span class="font-medium w-8 inline-block">Sun</span> 10:00 - 18:00</li>
                        @endif
                    </ul>
                </div>
                @if ($businessSettings && $businessSettings->phone_number)
                    <div>
                        <p class="text-heading-md font-semibold mb-2 text-brand">Phone</p>
                        <p class="text-body-md">{{ $businessSettings->phone_number }}</p>
                    </div>
                @endif
                <nav class="pt-6 border-t border-disabled/50 space-y-2">
                    <a href="#" class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">About Us</a>
                    <a href="#" class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">Contact Us</a>
                    <a href="#" class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">Terms of Service</a>
                </nav>
            </aside>
            <section aria-label="List of Bookable Services" class="space-y-6">
                @forelse ($menus as $menu)
                    <article
                        class="border border-disabled/50 rounded-lg p-4 flex items-center bg-white shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <div class="flex items-start justify-between gap-4 w-full">
                            <div class="space-y-2">
                                <h2 class="text-heading-lg font-semibold text-brand">{{ $menu->name }}</h2>
                                <p class="text-body-md text-main-text/80">{{ $menu->required_time }} min</p>
                                @if ($menu->description)
                                    <p class="text-body-md text-main-text pt-2">{{ Str::limit($menu->description, 100) }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col justify-between items-end shrink-0 gap-4 min-h-[80px]">
                                <button aria-label="View Details" class="text-secondary-button hover:text-link transition-colors" title="View Details">
                                    <i class="fa-solid fa-circle-info fa-lg" aria-hidden="true"></i>
                                </button>
                                <a href="{{ route('booking.first-step', ['menuId' => $menu->id]) }}"
                                    class="block rounded-full bg-main-button hover:bg-btn-main-hover text-footer-text text-button-md font-semibold px-6 py-2 transition-colors duration-300"
                                    type="button">Book</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <article
                        class="border border-disabled/50 rounded-lg p-4 flex items-center bg-white shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <div class="flex items-start justify-between gap-4 w-full">
                            <div class="space-y-2">
                                <h2 class="text-heading-lg font-semibold text-brand">Standard Tire Change</h2>
                                <p class="text-body-md text-main-text/80">60 min</p>
                            </div>
                            <div class="flex flex-col justify-between items-end shrink-0 gap-4 min-h-[80px]">
                                <button aria-label="View Details" class="text-secondary-button hover:text-link transition-colors" title="View Details">
                                    <i class="fa-solid fa-circle-info fa-lg" aria-hidden="true"></i>
                                </button>
                                <a href="#" class="block rounded-full bg-main-button hover:bg-btn-main-hover text-footer-text text-button-md font-semibold px-6 py-2 transition-colors duration-300" type="button">Book</a>
                            </div>
                        </div>
                    </article>
                @endforelse
            </section>
        </div>
    </div>
</x-layouts.app>