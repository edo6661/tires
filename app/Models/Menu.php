<?php
namespace App\Models;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Menu extends Model
{
    use HasFactory, Translatable;
    protected $fillable = [
        'required_time',
        'price',
        'photo_path',
        'display_order',
        'is_active',
        'color',
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    // Load all translations, not filtered by locale
    protected $with = ['translations'];
    protected function getTranslatableFields(): array
    {
        return ['name', 'description'];
    }
    protected function getDefaultTranslation(string $attribute)
    {
        $defaults = [
            'name' => 'Unnamed Menu',
            'description' => 'No description available',
        ];
        return $defaults[$attribute] ?? null;
    }
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
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
    public function getDarkenedTextColor(): string
    {
        $hex = str_replace('#', '', $this->color);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = floor($r * 0.5);
        $g = floor($g * 0.5);
        $b = floor($b * 0.5);

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function blockedPeriods()
    {
        return $this->hasMany(BlockedPeriod::class);
    }
    public function scopeTranslated($query, ?string $locale = null)
    {
        return $query->withTranslations($locale);
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
    public function getNameInLocale(string $locale): ?string
    {
        return $this->getTranslatedAttribute('name', $locale);
    }
    public function getDescriptionInLocale(string $locale): ?string
    {
        return $this->getTranslatedAttribute('description', $locale);
    }
}
