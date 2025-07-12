@auth
    @if(auth()->user()->isAdmin())
        <div x-data="sidebar()" class="bg-white border-r border-gray-200 flex flex-col sticky top-0 h-screen transition-all duration-300 ease-in-out" :class="isExpanded ? 'w-72' : 'w-20'">
            <div class="p-4 border-b border-gray-200">
                <button @click="toggle()" class="w-full flex items-center justify-center p-2 text-gray-600 hover:bg-gray-100 rounded-md transition-colors duration-200">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>

            <div class="p-4 overflow-y-auto flex-1">
                <div class="mb-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 text-gray-700 hover:bg-gray-50 p-2 rounded-md transition-colors duration-200">
                        <img src="{{ asset('images/logo.jpg') }}" alt="Admin Logo" class="object-contain w-8 h-8 rounded flex-shrink-0" />
                        <span class="text-base font-semibold whitespace-nowrap overflow-hidden transition-all duration-300" 
                                :class="isExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">Dashboard</span>
                    </a>
                </div>

                <nav class="flex-1 space-y-2">
                    <div class="space-y-1">
                        <div x-data="dropdown()" class="relative">
                            <button 
                                @click="isExpanded ? toggle() : null"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm hover:bg-gray-100 rounded-md transition-colors duration-200 group"
                                :class="{ 'bg-gray-100': isOpen }"
                            >
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-calendar-alt text-gray-500 w-4 text-center flex-shrink-0"></i>
                                    <span class="whitespace-nowrap overflow-hidden transition-all duration-300" 
                                            :class="isExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">Reservation Management</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-400 flex-shrink-0" 
                                    :class="{ 'rotate-180': isOpen }"
                                    x-show="isExpanded"></i>
                            </button>
                            
                            <div 
                                x-show="isOpen && isExpanded" 
                                x-transition:enter="transition-all ease-in-out duration-300"
                                x-transition:enter-start="max-h-0"
                                x-transition:enter-end="max-h-96"
                                x-transition:leave="transition-all ease-in-out duration-300"
                                x-transition:leave-start="max-h-96"
                                x-transition:leave-end="max-h-0"
                                class="mt-1 space-y-1 ml-4 border-l-2 border-gray-200 pl-4 overflow-hidden"
                            >
                                <template x-for="item in items" :key="item.id">
                                    <a :href="item.url" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-800 rounded-md transition-colors duration-150">
                                        <i :class="item.icon" class="text-xs mr-2 text-gray-400"></i>
                                        <span x-text="item.name"></span>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <div x-data="dropdown('customer')" class="relative">
                            <button 
                                @click="isExpanded ? toggle() : null"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm hover:bg-gray-100 rounded-md transition-colors duration-200 font-medium text-green-600 bg-green-50"
                                :class="{ 'bg-green-100': isOpen }"
                            >
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-headset text-green-600 w-4 text-center flex-shrink-0"></i>
                                    <span class="whitespace-nowrap overflow-hidden transition-all duration-300" 
                                            :class="isExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">Customer Support</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-400 flex-shrink-0" 
                                    :class="{ 'rotate-180': isOpen }"
                                    x-show="isExpanded"></i>
                            </button>
                            
                            <div 
                                x-show="isOpen && isExpanded" 
                                x-transition:enter="transition-all ease-in-out duration-300"
                                x-transition:enter-start="max-h-0"
                                x-transition:enter-end="max-h-96"
                                x-transition:leave="transition-all ease-in-out duration-300"
                                x-transition:leave-start="max-h-96"
                                x-transition:leave-end="max-h-0"
                                class="mt-1 space-y-1 ml-4 border-l-2 border-gray-200 pl-4 overflow-hidden"
                            >
                                <template x-for="item in items" :key="item.id">
                                    <a :href="item.url" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-800 rounded-md transition-colors duration-150">
                                        <i :class="item.icon" class="text-xs mr-2 text-gray-400"></i>
                                        <span x-text="item.name"></span>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <div x-data="dropdown('settings')" class="relative">
                            <button 
                                @click="isExpanded ? toggle() : null"
                                class="w-full flex items-center justify-between px-3 py-2 text-sm hover:bg-gray-100 rounded-md transition-colors duration-200"
                                :class="{ 'bg-gray-100': isOpen }"
                            >
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-cog text-gray-500 w-4 text-center flex-shrink-0"></i>
                                    <span class="whitespace-nowrap overflow-hidden transition-all duration-300" 
                                            :class="isExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">Settings</span>
                                </div>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-400 flex-shrink-0" 
                                    :class="{ 'rotate-180': isOpen }"
                                    x-show="isExpanded"></i>
                            </button>
                            
                            <div 
                                x-show="isOpen && isExpanded" 
                                x-transition:enter="transition-all ease-in-out duration-300"
                                x-transition:enter-start="max-h-0"
                                x-transition:enter-end="max-h-96"
                                x-transition:leave="transition-all ease-in-out duration-300"
                                x-transition:leave-start="max-h-96"
                                x-transition:leave-end="max-h-0"
                                class="mt-1 space-y-1 ml-4 border-l-2 border-gray-200 pl-4 overflow-hidden"
                            >
                                <template x-for="item in items" :key="item.id">
                                    <a :href="item.url" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-800 rounded-md transition-colors duration-150">
                                        <i :class="item.icon" class="text-xs mr-2 text-gray-400"></i>
                                        <span x-text="item.name"></span>
                                    </a>
                                </template>
                            </div>
                        </div>

                        <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-100 rounded-md font-medium transition-colors duration-200">
                            <i class="fas fa-users text-gray-500 w-4 text-center flex-shrink-0"></i>
                            <span class="whitespace-nowrap overflow-hidden transition-all duration-300" 
                                    :class="isExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">Customer List</span>
                        </a>
                        <a href="#" class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-100 rounded-md font-medium transition-colors duration-200">
                            <i class="fas fa-chart-bar text-gray-500 w-4 text-center flex-shrink-0"></i>
                            <span class="whitespace-nowrap overflow-hidden transition-all duration-300" 
                                    :class="isExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">Aggregation / Analysis</span>
                        </a>
                    </div>
                </nav>
            </div>
        </div>
    @endif
@endauth