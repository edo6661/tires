<header class="px-4 md:px-8 py-4 border-b border-gray-200 space-y-4 bg-white sticky top-0 z-10 w-full">
    <button class="border border-gray-300 rounded-md px-2 py-1 font-medium hover:bg-gray-100 transition text-sm">
        Takanawa Gateway City
    </button>

    <div class="flex flex-col md:flex-row justify-between">
        <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-12 text-xs select-none whitespace-nowrap">
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-gray-700 select-text">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo for X Change Tire Installation Reservation" class="object-cover w-16" />
                <span class="text-base font-semibold">Tire Installation Reservation</span>
            </a>
        </div>

        <nav class="mt-4 md:mt-0 flex gap-6">
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
            @auth
                @if(auth()->user()->isCustomer())
                    <x-shared.form-hint-icon
                        label="Logout"
                        icon="fa-solid fa-right-from-bracket"  
                        position="bottom"
                        action="{{ route('logout') }}"
                    />
                    <x-shared.link-hint-icon 
                        href="{{ route('customer.reservation.index') }}"
                        label="Reservations"
                        icon="fa-solid fa-book"
                        position="bottom"
                        activePath="reservation*"
                        
                    />
                    
                @endif
            @endauth
            
            @guest
                <x-shared.link-hint-icon 
                    label="Login" 
                    icon="fa-solid fa-user" 
                    position="bottom"
                    href="/login"
                    activePath="login*"
                />
            @endguest
        </nav>
    </div>
</header>