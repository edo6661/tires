<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockedPeriod extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'menu_id',
        'start_datetime',
        'end_datetime',
        'reason',
        'all_menus',
    ];
    
    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'all_menus' => 'boolean',
    ];
    
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
    
    public function getDurationInHours(): float
    {
        return $this->start_datetime->diffInHours($this->end_datetime, true);
    }
    
    public function getDurationInMinutes(): int
    {
        return $this->start_datetime->diffInMinutes($this->end_datetime, true);
    }
    
    public function isShortDuration(): bool
    {
        return $this->getDurationInHours() <= 24;
    }
    
    public function getDurationText(): string
    {
        $hours = $this->getDurationInHours();
        
        if ($hours < 1) {
            return $this->getDurationInMinutes() . ' minute';
        } elseif ($hours < 24) {
            return number_format($hours, 1) . ' hours';
        } else {
            return number_format($hours / 24, 1) . ' days';
        }
    }
    
    public function scopeShortDuration($query)
    {
        return $query->whereRaw('TIMESTAMPDIFF(HOUR, start_datetime, end_datetime) <= 24');
    }
    
    public function scopeByDurationHours($query, $hours)
    {
        return $query->whereRaw('TIMESTAMPDIFF(HOUR, start_datetime, end_datetime) <= ?', [$hours]);
    }
    
    public function isActive(): bool
    {
        $now = now();
        return $now->between($this->start_datetime, $this->end_datetime);
    }
    
    public static function createWithHours($menuId, $startDatetime, $durationHours, $reason, $allMenus = false)
    {
        $endDatetime = $startDatetime->copy()->addHours($durationHours);
        
        return self::create([
            'menu_id' => $allMenus ? null : $menuId,
            'start_datetime' => $startDatetime,
            'end_datetime' => $endDatetime,
            'reason' => $reason,
            'all_menus' => $allMenus,
        ]);
    }
    public function getAffectedDates(): array
    {
        $dates = [];
        $current = $this->start_datetime->copy()->startOfDay();
        $end = $this->end_datetime->copy()->startOfDay();
        
        while ($current->lte($end)) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }
        
        return $dates;
    }

    public function getBlockedHours(): array
    {
        $hours = [];
        $current = $this->start_datetime->copy();
        $end = $this->end_datetime->copy();
        
        while ($current->lte($end)) {
            $hours[] = [
                'date' => $current->format('Y-m-d'),
                'hour' => $current->format('H:i'),
                'datetime' => $current->format('Y-m-d H:i:s')
            ];
            $current->addHour();
        }
        
        return $hours;
    }
    public function getBlockedMenusInfo()
    {
        if ($this->all_menus) {
            return [
                'type' => 'all',
                'message' => 'All menus blocked',
                'menus' => []
            ];
        }
        
        return [
            'type' => 'specific',
            'message' => 'Specific menu blocked',
            'menus' => [$this->menu]
        ];
    }

    public function getTooltipInfo()
    {
        $menuInfo = $this->getBlockedMenusInfo();
        
        return [
            'reason' => $this->reason,
            'start_time' => $this->start_datetime->format('H:i'),
            'end_time' => $this->end_datetime->format('H:i'),
            'date' => $this->start_datetime->format('Y-m-d'),
            'is_all_menus' => $this->all_menus,
            'blocked_menu' => $this->menu ? $this->menu->name : null,
            'menu_info' => $menuInfo
        ];
    }
}