@props(['icon', 'title', 'href', 'isActive' => false])

<a href="{{ $href }}" 
   class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-100 rounded-md transition-colors duration-200 {{ $isActive ? 'text-green-600 bg-green-50' : '' }}">
    <i class="{{ $icon }} {{ $isActive ? 'text-green-600' : 'text-gray-500' }} w-4 text-center flex-shrink-0"></i>
    <span class="whitespace-nowrap overflow-hidden transition-all duration-300" 
            :class="isExpanded ? 'opacity-100 w-auto' : 'opacity-0 w-0'">{{ $title }}</span>
</a>