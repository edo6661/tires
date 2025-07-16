@props(['type', 'icon', 'title', 'isActive' => false])

<div x-data="dropdown('{{ $type }}')" class="relative">
    <button 
        @click="toggle()"
        class="w-full flex items-center justify-between px-3 py-2 text-sm hover:bg-gray-100 rounded-md transition-colors duration-200 {{ $isActive ? 'font-medium text-green-600 bg-green-50' : '' }}"
        :class="{ '{{ $isActive ? 'bg-green-100' : 'bg-gray-100' }}': isOpen }"
    >
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} {{ $isActive ? 'text-green-600' : 'text-gray-500' }} w-4 text-center flex-shrink-0"></i>
            <span class="whitespace-nowrap">{{ $title }}</span>
        </div>
        <i class="fas fa-chevron-down text-xs transition-transform duration-400 flex-shrink-0" 
            :class="{ 'rotate-180': isOpen }"></i>
    </button>
    
    <div 
        x-show="isOpen" 
        x-transition:enter="transition-all ease-in-out duration-300"
        x-transition:enter-start="max-h-0"
        x-transition:enter-end="max-h-96"
        x-transition:leave="transition-all ease-in-out duration-300"
        x-transition:leave-start="max-h-96"
        x-transition:leave-end="max-h-0"
        class="mt-1 space-y-1 ml-4 border-l-2 border-gray-200 pl-4 overflow-hidden"
    >
        <template x-for="item in items" :key="item.id">
            <a :href="item.url" 
               class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-800 rounded-md transition-colors duration-150"
               :class="{ 'bg-green-50 text-green-600 font-medium': item.isActive }">
                <i :class="item.icon" class="text-xs mr-2 "
                    :class="item.isActive ? 'text-green-600' : 'text-gray-500'"
                ></i>
                <span x-text="item.name"></span>
            </a>
        </template>
    </div>
</div>