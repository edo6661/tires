@props(['type', 'icon', 'title', 'isActive' => false])

<div x-data="dropdown('{{ $type }}')" class="relative">
    <button 
        @click="toggle()"
        class="w-full flex items-center justify-between px-3 py-2 text-body-md hover:bg-sub hover:text-brand hover:shadow-sm rounded-md transition-all duration-300 transform hover:scale-[1.02] {{ $isActive ? 'font-medium text-brand bg-sub' : 'text-main-text' }}"
        :class="{ '{{ $isActive ? 'bg-sub' : 'bg-sub/50' }}': isOpen }"
    >
        <div class="flex items-center gap-3">
            <i class="{{ $icon }} {{ $isActive ? 'text-brand' : 'text-main-text' }} w-4 text-center flex-shrink-0 transition-colors duration-300"></i>
            <span class="whitespace-nowrap">{{ $title }}</span>
        </div>
        <i class="fas fa-chevron-down text-xs transition-all duration-400 flex-shrink-0" 
            :class="{ 'rotate-180 text-brand': isOpen, 'text-main-text': !isOpen }"></i>
    </button>
    
    <div 
        x-show="isOpen" 
        x-transition:enter="transition-all ease-in-out duration-300"
        x-transition:enter-start="max-h-0 opacity-0"
        x-transition:enter-end="max-h-96 opacity-100"
        x-transition:leave="transition-all ease-in-out duration-300"
        x-transition:leave-start="max-h-96 opacity-100"
        x-transition:leave-end="max-h-0 opacity-0"
        class="mt-1 space-y-1 ml-4 border-l-2 border-sub pl-4 overflow-hidden"
    >
        <template x-for="item in items" :key="item.id">
            <a :href="item.url" 
               class="block px-3 py-2 text-body-md text-main-text hover:bg-sub/50 hover:text-brand hover:shadow-sm rounded-md transition-all duration-300 transform hover:scale-[1.02] hover:translate-x-1"
               :class="{ 'bg-sub text-brand font-medium shadow-sm': item.isActive }">
                <i :class="item.icon" class="text-xs mr-2 transition-colors duration-300"
                   :class="item.isActive ? 'text-brand' : 'text-main-text'"
                ></i>
                <span x-text="item.name"></span>
            </a>
        </template>
    </div>
</div>