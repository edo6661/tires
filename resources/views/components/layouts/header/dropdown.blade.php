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