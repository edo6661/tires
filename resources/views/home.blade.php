<x-layouts.app>
    <div class="container">
        <section aria-label="{{ __('home.main_image_aria') }}" class="mb-12 rounded-lg overflow-hidden shadow-lg">
            <img src="{{ asset('images/hero.jpg') }}" alt="{{ __('home.hero_alt') }}"
                class="w-full h-auto object-cover object-center" />
        </section>
        <div class="grid md:grid-cols-[250px_1fr] gap-10">
            <aside class="sm:sticky top-8 self-start text-main-text md:border-r md:border-disabled/50 md:pr-6 space-y-8">
                <div>
                    <p class="text-heading-md font-semibold mb-2 text-brand">{{ __('home.shop_name') }}</p>
                    @if ($businessSettings && $businessSettings->shop_name)
                        <p class="text-title-md font-bold text-brand">{{ $businessSettings->shop_name }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-heading-md font-semibold mb-2 text-brand">{{ __('home.location') }}</p>
                    @if ($businessSettings && $businessSettings->address)
                        <p class="text-body-md">{{ $businessSettings->address }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-heading-md font-semibold mb-2 text-brand">{{ __('home.business_hours') }}</p>
                    <ul class="space-y-1 leading-tight text-body-md">
                        @if ($businessSettings && $businessSettings->business_hours)
                            @foreach (__('home.days') as $dayKey => $dayTranslation)
                                @php
                                    $hours = $businessSettings->business_hours[$dayKey] ?? null;
                                @endphp
                                <li>
                                    <span class="font-medium w-8 inline-block">{{ $dayTranslation }}</span>
                                    @if ($hours && isset($hours['closed']) && $hours['closed'])
                                        {{ __('home.closed') }}
                                    @elseif($hours && isset($hours['open']) && isset($hours['close']))
                                        {{ $hours['open'] }} - {{ $hours['close'] }}
                                    @else
                                        {{ __('home.closed') }}
                                    @endif
                                </li>
                            @endforeach
                        @else
                            {{-- Fallback data jika business settings tidak ada --}}
                            <li><span class="font-medium w-8 inline-block">{{ __('home.days.monday') }}</span> 10:00 -
                                18:00</li>
                            <li><span class="font-medium w-8 inline-block">{{ __('home.days.tuesday') }}</span> 10:00 -
                                18:00</li>
                            <li><span class="font-medium w-8 inline-block">{{ __('home.days.wednesday') }}</span>
                                {{ __('home.closed') }}</li>
                            <li><span class="font-medium w-8 inline-block">{{ __('home.days.thursday') }}</span>
                                {{ __('home.closed') }}</li>
                            <li><span class="font-medium w-8 inline-block">{{ __('home.days.friday') }}</span> 10:00 -
                                18:00</li>
                            <li><span class="font-medium w-8 inline-block">{{ __('home.days.saturday') }}</span> 10:00
                                - 18:00</li>
                            <li><span class="font-medium w-8 inline-block">{{ __('home.days.sunday') }}</span> 10:00 -
                                18:00</li>
                        @endif
                    </ul>
                </div>
                @if ($businessSettings && $businessSettings->phone_number)
                    <div>
                        <p class="text-heading-md font-semibold mb-2 text-brand">{{ __('home.phone') }}</p>
                        <p class="text-body-md">{{ $businessSettings->phone_number }}</p>
                    </div>
                @endif
                <nav class="pt-6 border-t border-disabled/50 space-y-2">
                    <a href="{{ route('about') }}"
                        class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">{{ __('home.about_us') }}</a>
                    <a href="{{ route('inquiry') }}"
                        class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">{{ __('home.contact_us') }}</a>
                    <a href="{{ route('terms') }}"
                        class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">{{ __('home.terms_of_service') }}</a>
                    <a href="{{ route('privacy') }}"
                        class="block text-link hover:text-link-hover text-body-md transition-colors duration-200">{{ __('home.privacy') }}</a>
                </nav>
            </aside>
            <section aria-label="{{ __('home.services_list_aria') }}" class="space-y-6">
                @forelse ($menus as $menu)
                    <article
                        class="border border-disabled/50 rounded-lg p-4 flex items-center bg-white shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <div class="flex items-start justify-between gap-4 w-full">
                            <div class="space-y-2">
                                <h2 class="text-heading-lg font-semibold text-brand">{{ $menu->name }}</h2>
                                <p class="text-body-md text-main-text/80">{{ $menu->required_time }}
                                    {{ __('home.duration_unit') }}</p>
                                @if ($menu->description)
                                    <p class="text-body-md text-main-text pt-2">
                                        {{ Str::limit($menu->description, 100) }}</p>
                                @endif
                            </div>
                            <div class="flex flex-col justify-between items-end shrink-0 gap-4 min-h-[80px]">
                                <button aria-label="{{ __('home.view_details') }}"
                                    class="text-secondary-button hover:text-link transition-colors"
                                    title="{{ __('home.view_details') }}">
                                    <i class="fa-solid fa-circle-info fa-lg" aria-hidden="true"></i>
                                </button>
                                <a href="{{ route('booking.first-step', ['menuId' => $menu->id]) }}"
                                    class="block rounded-full bg-main-button hover:bg-btn-main-hover text-footer-text text-button-md font-semibold px-6 py-2 transition-colors duration-300"
                                    type="button">{{ __('home.book_button') }}</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <article
                        class="border border-disabled/50 rounded-lg p-4 flex items-center bg-white shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <div class="flex items-start justify-between gap-4 w-full">
                            <div class="space-y-2">
                                <h2 class="text-heading-lg font-semibold text-brand">
                                    {{ __('home.empty_service_name') }}</h2>
                                <p class="text-body-md text-main-text/80">{{ __('home.empty_service_duration') }}
                                    {{ __('home.duration_unit') }}</p>
                            </div>
                            <div class="flex flex-col justify-between items-end shrink-0 gap-4 min-h-[80px]">
                                <button aria-label="{{ __('home.view_details') }}"
                                    class="text-secondary-button hover:text-link transition-colors"
                                    title="{{ __('home.view_details') }}">
                                    <i class="fa-solid fa-circle-info fa-lg" aria-hidden="true"></i>
                                </button>
                                <a href="#"
                                    class="block rounded-full bg-main-button hover:bg-btn-main-hover text-footer-text text-button-md font-semibold px-6 py-2 transition-colors duration-300"
                                    type="button">{{ __('home.book_button') }}</a>
                            </div>
                        </div>
                    </article>
                @endforelse
            </section>
        </div>
    </div>
</x-layouts.app>
