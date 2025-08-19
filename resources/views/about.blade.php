<x-layouts.app>
    <div class="container">
        <div class="grid md:grid-cols-[250px_1fr] gap-10">
            <aside class="sm:sticky top-8 self-start text-main-text md:border-r md:border-disabled/50 md:pr-6 space-y-8">
                <div>
                    <p class="text-heading-md font-semibold mb-2 text-brand">{{ __('home.location') }}</p>
                    <p class="text-body-md">{{ $businessSettings->address }}</p>
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
                        class="text-link hover:text-link-hover hover:underline transition-colors duration-300 block text-body-md">{{ __('home.about_us') }}</a>
                    <a href="{{ route('inquiry') }}"
                        class="text-link hover:text-link-hover hover:underline transition-colors duration-300 block mt-2 text-body-md">{{ __('home.contact_us') }}</a>
                    <a href="{{ route('terms') }}"
                        class="text-link hover:text-link-hover hover:underline transition-colors duration-300 block mt-2 text-body-md">{{ __('home.terms_of_service') }}</a>
                    <a href="{{ route('privacy') }}"
                        class="text-link hover:text-link-hover hover:underline transition-colors duration-300 block mt-2 text-body-md">{{ __('home.privacy') }}</a>
                </nav>
            </aside>

            <main>
                <div class="wrapper space-y-8">
                    <h1 class="text-title-lg font-bold text-brand pb-4 border-b border-disabled/50">
                        {{ __('about.title') }}
                    </h1>

                    <p class="text-body-md text-main-text leading-relaxed">
                        {{ __('about.mission_statement') }}
                    </p>

                    <dl class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.company_name_label') }}
                            </dt>
                            <dd class="md:col-span-3 text-main-text/90">{{ __('about.company_name_value') }}</dd>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.address_label') }}</dt>
                            <dd class="md:col-span-3 text-main-text/90">{{ __('about.address_value') }}</dd>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <dt class="md:col-span-1 font-semibold text-main-text">
                                {{ __('about.representative_label') }}</dt>
                            <dd class="md:col-span-3 text-main-text/90">{{ __('about.representative_value') }}</dd>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.phone_label') }}</dt>
                            <dd class="md:col-span-3 text-main-text/90">{{ __('about.phone_value') }}</dd>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.fax_label') }}</dt>
                            <dd class="md:col-span-3 text-main-text/90">{{ __('about.fax_value') }}</dd>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <dt class="md:col-span-1 font-semibold text-main-text">
                                {{ __('about.business_hours_label') }}</dt>
                            <dd class="md:col-span-3 text-main-text/90">{{ __('about.business_hours_value') }}</dd>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                            <dt class="md:col-span-1 font-semibold text-main-text">{{ __('about.holidays_label') }}
                            </dt>
                            <dd class="md:col-span-3 text-main-text/90">{{ __('about.holidays_value') }}</dd>
                        </div>
                    </dl>
                </div>
            </main>
        </div>
    </div>
</x-layouts.app>
