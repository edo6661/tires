@props([
    'position' => 'bottom-right',
    'style' => 'dropdown',
    'showFlag' => true,
    'showName' => true,
    'compact' => false,
])
@php
    use App\Http\Middleware\SetLocale;
    $supportedLocales = SetLocale::getSupportedLocales();
    $currentLocale = app()->getLocale();
    $currentLocaleInfo = SetLocale::getLocaleInfo($currentLocale);

    $flagImages = [
        'en' => 'https://flagcdn.com/w40/gb.png',
        'ja' => 'https://flagcdn.com/w40/jp.png',
    ];

    $positionClasses = [
        'bottom-right' => 'top-full mt-2 right-0',
        'bottom-left' => 'top-full mt-2 left-0',
        'top-right' => 'bottom-full mb-2 right-0',
        'top-left' => 'bottom-full mb-2 left-0',
    ];
    $dropdownPosition = isset($positionClasses[$position])
        ? $positionClasses[$position]
        : $positionClasses['bottom-right'];
@endphp
@if ($style === 'dropdown')
    <div class="relative" x-data="{ open: false }" @click.away="open = false">
        <button @click="open = !open"
            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
            aria-expanded="false" aria-haspopup="true">
            @if ($showFlag && $currentLocaleInfo)
                @if (isset($flagImages[$currentLocale]))
                    <img src="{{ $flagImages[$currentLocale] }}" alt="{{ $currentLocaleInfo['name'] }} flag"
                        class="w-4 h-3 object-cover rounded-sm">
                @else
                    <span class="text-lg">{{ $currentLocaleInfo['flag'] }}</span>
                @endif
            @endif
            @if ($showName && $currentLocaleInfo && !$compact)
                <span>{{ $currentLocaleInfo['name'] }}</span>
            @elseif(!$showFlag)
                <span>{{ strtoupper(SetLocale::getRoutePrefix($currentLocale)) }}</span>
            @endif
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute {{ $dropdownPosition }} w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50"
            style="display: none;">
            <div class="py-1">
                @foreach ($supportedLocales as $localeCode => $localeInfo)
                    @php
                        $isActive = $localeCode === $currentLocale;
                        $routeName = Route::currentRouteName();
                        // Convert locale code to route prefix
                        $routePrefix = SetLocale::getRoutePrefix($localeCode);
                        $routeParams = array_merge(request()->route()->parameters(), ['locale' => $routePrefix]);
                    @endphp
                    <a href="{{ route($routeName, $routeParams) }}"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150 {{ $isActive ? 'bg-blue-50 text-blue-700 font-medium' : '' }}"
                        @if ($isActive) aria-current="true" @endif>
                        @if ($showFlag)
                            @if (isset($flagImages[$localeCode]))
                                <img src="{{ $flagImages[$localeCode] }}" alt="{{ $localeInfo['name'] }} flag"
                                    class="w-5 h-4 object-cover rounded-sm flex-shrink-0">
                            @else
                                <span class="text-lg">{{ $localeInfo['flag'] }}</span>
                            @endif
                        @endif
                        <div class="flex flex-col">
                            <span>{{ $localeInfo['name'] }}</span>
                            <span class="text-xs text-gray-500 uppercase">{{ $routePrefix }}</span>
                        </div>
                        @if ($isActive)
                            <svg class="w-4 h-4 ml-auto text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@elseif($style === 'buttons')
    <div class="flex items-center {{ $compact ? 'gap-1' : 'gap-2' }}">
        @foreach ($supportedLocales as $localeCode => $localeInfo)
            @php
                $isActive = $localeCode === $currentLocale;
                $routeName = Route::currentRouteName();
                // Convert locale code to route prefix
                $routePrefix = SetLocale::getRoutePrefix($localeCode);
                $routeParams = array_merge(request()->route()->parameters(), ['locale' => $routePrefix]);
            @endphp
            <a href="{{ route($routeName, $routeParams) }}"
                class="flex items-center {{ $compact ? 'gap-1 px-2 py-1' : 'gap-2 px-3 py-2' }} text-sm font-medium rounded-md transition-all duration-200 {{ $isActive ? 'bg-blue-100 text-blue-700 ring-1 ring-blue-200' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}"
                @if ($isActive) aria-current="true" @endif>
                @if ($showFlag)
                    @if (isset($flagImages[$localeCode]))
                        <img src="{{ $flagImages[$localeCode] }}" alt="{{ $localeInfo['name'] }} flag"
                            class="{{ $compact ? 'w-4 h-3' : 'w-5 h-4' }} object-cover rounded-sm">
                    @else
                        <span class="{{ $compact ? 'text-base' : 'text-lg' }}">{{ $localeInfo['flag'] }}</span>
                    @endif
                @endif
                @if ($showName && !$compact)
                    <span>{{ $localeInfo['name'] }}</span>
                @elseif(!$showFlag || $compact)
                    <span class="uppercase">{{ $localeCode }}</span>
                @endif
            </a>
        @endforeach
    </div>
@elseif($style === 'select')
    <div class="relative">
        <select onchange="window.location.href = this.value"
            class="appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @foreach ($supportedLocales as $localeCode => $localeInfo)
                @php
                    $isActive = $localeCode === $currentLocale;
                    $routeName = Route::currentRouteName();
                    // Convert locale code to route prefix
                    $routePrefix = SetLocale::getRoutePrefix($localeCode);
                    $routeParams = array_merge(request()->route()->parameters(), ['locale' => $routePrefix]);
                @endphp
                <option value="{{ route($routeName, $routeParams) }}" {{ $isActive ? 'selected' : '' }}>
                    @if ($showFlag && !isset($flagImages[$localeCode]))
                        {{ $localeInfo['flag'] }}
                    @endif
                    {{ $localeInfo['name'] }}
                    @if (!$showName)
                        ({{ strtoupper($localeCode) }})
                    @endif
                </option>
            @endforeach
        </select>
        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>
@endif
