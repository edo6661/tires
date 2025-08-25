@props(['businessSettings'])

<aside {{ $attributes->merge(['class' => 'w-full md:w-1/3 lg:w-1/4 bg-sub p-6 rounded-lg shadow-sm border border-brand/10']) }}>
    <div class="space-y-6">
        <div class="flex items-center gap-2">
            @if (isset($businessSettings->logo))
                <img src="{{ asset('storage/' . $businessSettings->logo) }}"
                    alt="{{ $businessSettings->shop_name ?? 'Logo' }}" class="h-8 w-auto">
            @else
                <img src="{{ asset('images/logo-remove-bg.png') }}" alt="Logo" class="h-8 w-auto">
            @endif
            <h2 class="text-title-md font-bold font-jp text-brand">
                {{ $businessSettings->shop_name ?: 'Takanawa Gateway City' }}
            </h2>
        </div>

        <div>
            <h3 class="text-heading-lg font-semibold text-main-text mb-2 font-jp">{{ __('inquiry.location') }}
            </h3>
            <p class="text-body-md text-main-text/90">
                {{ $businessSettings->address ?: '2095-8 Miyadera, Iruma-shi, Saitama-ken' }}</p>
        </div>

        <div>
            <h3 class="text-heading-lg font-semibold text-main-text mb-2 font-jp">
                {{ __('inquiry.opening_hours') }}</h3>
            <ul class="text-body-md text-main-text/90 space-y-1">
                @if ($businessSettings && $businessSettings->business_hours)
                    {{-- Mengambil array hari langsung dari file lokalisasi --}}
                    @foreach (__('inquiry.days') as $dayKey => $dayName)
                        @php
                            $hours = $businessSettings->business_hours[$dayKey] ?? null;
                            $isClosed = ($hours && isset($hours['closed']) && $hours['closed']) || !$hours;
                        @endphp
                        <li class="flex justify-between {{ $isClosed ? 'text-main-text/40' : '' }}">
                            <span>{{ $dayName }}</span>
                            @if ($isClosed)
                                <span>{{ __('inquiry.closed') }}</span>
                            @else
                                <span>{{ $hours['open'] }} ~ {{ $hours['close'] }}</span>
                            @endif
                        </li>
                    @endforeach
                @else
                    {{-- Fallback jika data tidak ada --}}
                    <li class="flex justify-between"><span>{{ __('inquiry.days.monday') }}</span><span>10:00 ~
                            18:00</span></li>
                    <li class="flex justify-between"><span>{{ __('inquiry.days.tuesday') }}</span><span>10:00 ~
                            18:00</span></li>
                    <li class="flex justify-between text-main-text/40">
                        <span>{{ __('inquiry.days.wednesday') }}</span><span>{{ __('inquiry.closed') }}</span>
                    </li>
                    <li class="flex justify-between text-main-text/40">
                        <span>{{ __('inquiry.days.thursday') }}</span><span>{{ __('inquiry.closed') }}</span>
                    </li>
                    <li class="flex justify-between"><span>{{ __('inquiry.days.friday') }}</span><span>10:00 ~
                            18:00</span></li>
                    <li class="flex justify-between"><span>{{ __('inquiry.days.saturday') }}</span><span>10:00
                            ~ 18:00</span></li>
                    <li class="flex justify-between"><span>{{ __('inquiry.days.sunday') }}</span><span>10:00 ~
                            18:00</span></li>
                @endif
            </ul>
        </div>

        <div class="pt-4 border-t border-brand/20">
            <a href="{{ route('about') }}"
                class="text-link hover:text-link-hover hover:underline transition-colors duration-300 block text-body-md @if(request()->routeIs('about')) text-brand font-semibold @endif">{{ __('home.about_us') }}</a>
            <a href="{{ route('inquiry') }}"
                class="text-link hover:text-link-hover hover:underline transition-colors duration-300 block mt-2 text-body-md @if(request()->routeIs('inquiry')) text-brand font-semibold @endif">{{ __('home.contact_us') }}</a>
            <a href="{{ route('terms') }}"
                class="text-link hover:text-link-hover hover:underline transition-colors duration-300 block mt-2 text-body-md @if(request()->routeIs('terms')) text-brand font-semibold @endif">{{ __('home.terms_of_service') }}</a>
            <a href="{{ route('privacy') }}"
                class="text-link hover:text-link-hover hover:underline transition-colors duration-300 block mt-2 text-body-md @if(request()->routeIs('privacy')) text-brand font-semibold @endif">{{ __('home.privacy') }}</a>
        </div>
    </div>
</aside>