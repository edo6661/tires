<header class="px-4 md:px-8 py-4 border-b border-disabled space-y-4 bg-white sticky top-0 z-10 w-full">
    {{-- <button class="border border-disabled rounded-md px-2 py-1 font-medium text-main-text hover:bg-sub transition-colors duration-300 text-sm">
        Takanawa Gateway City
    </button> --}}
    <div class="flex flex-col md:flex-row justify-between">
        <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-12 text-xs select-none whitespace-nowrap">
            <a href="{{ route('home') }}"
                class="flex items-center gap-3 text-brand hover:text-link-hover transition-colors duration-300 select-text">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo for X Change Tire Installation Reservation"
                    class="object-cover w-16" />
                <span class="text-base font-semibold text-main-text">
                    {{ __('app.tire_installation_reservation') }}
                </span>
            </a>
        </div>
        <nav class="mt-4 md:mt-0 flex items-center gap-6">

            <x-shared.link-hint-icon label="{{ __('app.calendar') }}" icon="fa-solid fa-calendar-days" position="bottom"
                href="{{ route('home') }}" activePath="/" />
            <x-shared.link-hint-icon label="{{ __('app.inquiry') }}" icon="fa-solid fa-envelope" position="bottom"
                :href="route('inquiry')" activePath="inquiry*" />
            @auth
                @if (auth()->user()->isCustomer())
                    <x-shared.link-hint-icon href="{{ route('customer.reservation.index') }}"
                        label="{{ __('app.reservations') }}" icon="fa-solid fa-book" position="bottom"
                        activePath="reservation*" />
                    <x-shared.link-hint-icon href="{{ route('profile.show') }}" label="{{ __('app.profile') }}"
                        icon="fa-solid fa-circle-user" position="bottom" activePath="profile*" />
                    <x-shared.form-hint-icon label="{{ __('app.logout') }}" icon="fa-solid fa-right-from-bracket"
                        position="bottom" action="{{ route('logout') }}" />
                @endif
            @endauth

            @guest
                <x-shared.link-hint-icon label="{{ __('app.login') }}" icon="fa-solid fa-user" position="bottom"
                    href="{{ route('login') }}" activePath="login*" />
            @endguest
            <x-shared.language-switcher style="dropdown" position="bottom-right" :show-flag="true" :show-name="false"
                :compact="true" />
        </nav>
    </div>
</header>
