@props(['icon', 'title', 'href', 'isActive' => false])

<a href="{{ $href }}" 
   class="flex items-center gap-3 px-3 py-2 text-body-md hover:bg-sub hover:text-brand hover:shadow-sm rounded-md transition-all duration-300 transform hover:scale-[1.02] hover:translate-x-1 {{ $isActive ? 'text-brand bg-sub font-medium shadow-sm' : 'text-main-text' }}">
    <i class="{{ $icon }} {{ $isActive ? 'text-brand' : 'text-main-text' }} w-4 text-center flex-shrink-0 transition-colors duration-300"></i>
    <span class="whitespace-nowrap">{{ $title }}</span>
</a>