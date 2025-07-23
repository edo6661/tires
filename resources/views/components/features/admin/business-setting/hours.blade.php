@props(['businessSettings'])

@php
    $days = [
        'monday' => 'Senin',
        'tuesday' => 'Selasa', 
        'wednesday' => 'Rabu',
        'thursday' => 'Kamis',
        'friday' => 'Jumat',
        'saturday' => 'Sabtu',
        'sunday' => 'Minggu'
    ];
    
    $currentDay = strtolower(now()->format('l'));
    $businessHours = $businessSettings->business_hours ?? [];
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 p-4']) }}>
    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
        <i class="fas fa-clock text-green-600 mr-2"></i>
        Jam Operasional
    </h3>
    
    <div class="space-y-2">
        @foreach($days as $day => $dayName)
            @php
                $hours = $businessHours[$day] ?? null;
                $isToday = $day === $currentDay;
                $isClosed = $hours && isset($hours['closed']) && $hours['closed'];
                $isOpen = false;
                
                if (!$isClosed && $hours && isset($hours['open']) && isset($hours['close'])) {
                    $currentTime = now()->format('H:i');
                    $isOpen = $isToday && $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
                }
            @endphp
            
            <div class="flex justify-between items-center py-1 {{ $isToday ? 'bg-blue-50 rounded px-2' : '' }}">
                <span class="font-medium text-gray-700 {{ $isToday ? 'text-blue-900' : '' }}">
                    {{ $dayName }}
                    @if($isToday)
                        <span class="text-xs text-blue-600">(Hari Ini)</span>
                    @endif
                </span>
                
                <div class="flex items-center space-x-2">
                    <span class="text-gray-900">
                        @if($isClosed)
                            <span class="text-red-600 font-medium">Tutup</span>
                        @elseif($hours && isset($hours['open']) && isset($hours['close']))
                            {{ $hours['open'] }} - {{ $hours['close'] }}
                        @else
                            <span class="text-red-600 font-medium">Tutup</span>
                        @endif
                    </span>
                    
                    @if($isToday && !$isClosed && $hours)
                        @if($isOpen)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-circle text-green-400 mr-1" style="font-size: 6px;"></i>
                                Buka
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-circle text-red-400 mr-1" style="font-size: 6px;"></i>
                                Tutup
                            </span>
                        @endif
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    @php
        $todayHours = $businessHours[$currentDay] ?? null;
        $isTodayClosed = $todayHours && isset($todayHours['closed']) && $todayHours['closed'];
    @endphp
    
    @if(!$isTodayClosed && $todayHours && isset($todayHours['open']) && isset($todayHours['close']))
        @php
            $currentTime = now()->format('H:i');
            $openTime = $todayHours['open'];
            $closeTime = $todayHours['close'];
            $minutesUntilClose = null;
            
            if ($currentTime >= $openTime && $currentTime <= $closeTime) {
                $now = now();
                $closingTime = now()->setTimeFromTimeString($closeTime);
                $minutesUntilClose = $now->diffInMinutes($closingTime, false);
            }
        @endphp
        
        @if($minutesUntilClose !== null && $minutesUntilClose > 0 && $minutesUntilClose <= 60)
            <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm text-yellow-800 flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                    Tutup dalam {{ $minutesUntilClose }} menit lagi
                </p>
            </div>
        @endif
    @endif
</div>