<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'required_time',
        'price',
        'description',
        'photo_path',
        'display_order',
        'is_active',
        'color', 
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    
    public static function getAvailableColors(): array
    {
        return [
            '#3B82F6' => 'Blue',
            '#10B981' => 'Green',
            '#F59E0B' => 'Yellow',
            '#EF4444' => 'Red',
            '#8B5CF6' => 'Purple',
            '#EC4899' => 'Pink',
            '#06B6D4' => 'Cyan',
            '#84CC16' => 'Lime',
            '#F97316' => 'Orange',
            '#6B7280' => 'Gray',
        ];
    }

    
    public function getColorWithOpacity(int $opacity = 10): string
    {
        $hex = str_replace('#', '', $this->color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return "rgba({$r}, {$g}, {$b}, 0.{$opacity})";
    }

    
    public function getTextColor(): string
    {
        $hex = str_replace('#', '', $this->color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance > 0.5 ? '#000000' : '#000000';
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function blockedPeriods()
    {
        return $this->hasMany(BlockedPeriod::class);
    }
}
