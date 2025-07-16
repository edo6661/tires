{{-- resources/views/components/features/reservation/calendar/menu-legend.blade.php --}}
@props(['menus'])

@if(isset($menus) && count($menus) > 0)
    <div class="mb-4 p-3 bg-white rounded-lg border border-gray-200">
        <h4 class="text-sm font-medium text-gray-900 mb-2">Menu Legend:</h4>
        <div class="flex flex-wrap gap-2">
            @foreach($menus as $menu)
                <div class="flex items-center space-x-2 px-2 py-1 rounded-md text-xs"
                    style="background-color: {{ $menu->getColorWithOpacity(10) }}; border-left: 3px solid {{ $menu->color }};">
                    <div class="w-2 h-2 rounded-full" style="background-color: {{ $menu->color }};"></div>
                    <span class="text-gray-700">{{ $menu->name }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif