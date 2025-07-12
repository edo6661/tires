<header class="px-6 md:px-12 py-4 border-b border-gray-200 space-y-4 bg-white sticky top-0 z-10">
    @auth
        @if(auth()->user()->isAdmin())
        @else
            <button class="border border-gray-300 rounded-md px-2 py-1 font-medium hover:bg-gray-100 transition text-sm">
                Takanawa Gateway City
            </button>
        @endif
    @endauth

    @guest
        <button class="border border-gray-300 rounded-md px-2 py-1 font-medium hover:bg-gray-100 transition text-sm">
            Takanawa Gateway City
        </button>
    @endguest

    <div class="flex flex-col md:flex-row justify-between items-center">
        @auth
            @if(auth()->user()->isCustomer())
                <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-12 text-xs select-none whitespace-nowrap">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 text-gray-700 select-text">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Logo for X Change Tire Installation Reservation" class="object-cover w-16" />
                        <span class="text-base font-semibold">Tire Installation Reservation</span>
                    </a>
                </div>
            @endif
        @endauth

        @guest
            <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-12 text-xs select-none whitespace-nowrap">
                <a href="{{ route('home') }}" class="flex items-center gap-3 text-gray-700 select-text">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo for X Change Tire Installation Reservation" class="object-cover w-16" />
                    <span class="text-base font-semibold">Tire Installation Reservation</span>
                </a>
            </div>
        @endguest

        <nav class="mt-4 md:mt-0 flex gap-6">
            @auth
                @if(auth()->user()->isCustomer())
                    <x-shared.link-hint-icon 
                        label="Calendar" 
                        icon="fa-solid fa-calendar-days" 
                        position="bottom"
                        href="/"
                        activePath="/"
                    />
                    <x-shared.link-hint-icon 
                        label="Inquiry" 
                        icon="fa-solid fa-envelope" 
                        position="bottom"
                        href="/inquiry"
                        activePath="inquiry*"
                    />
                @endif
            @endauth

            @guest
                <x-shared.link-hint-icon 
                    label="Calendar" 
                    icon="fa-solid fa-calendar-days" 
                    position="bottom"
                    href="/"
                    activePath="/"
                />
                <x-shared.link-hint-icon 
                    label="Inquiry" 
                    icon="fa-solid fa-envelope" 
                    position="bottom"
                    href="/inquiry"
                    activePath="inquiry*"
                />
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



