<header class="px-4 md:px-8 py-4 border-b border-gray-200 space-y-4 bg-white sticky top-0 z-10 w-full">
    <div class="flex items-center gap-4 justify-between">
        <button @click="sidebarOpen = !sidebarOpen" 
                class="p-2 text-gray-600 hover:bg-gray-100 rounded-md transition-colors duration-200">
            <i class="fas fa-bars text-lg"></i>
        </button>
        
        <div class="flex flex-col md:flex-row md:items-center gap-4 md:gap-12 text-xs select-none whitespace-nowrap">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-gray-700 select-text">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo for X Change Tire Installation Reservation" class="object-cover w-16" />
            </a>
        </div>

        <x-shared.form-hint-icon
            label="Logout"
            icon="fa-solid fa-right-from-bracket"  
            position="bottom"
            action="{{ route('logout') }}"
        />
    </div>
</header>