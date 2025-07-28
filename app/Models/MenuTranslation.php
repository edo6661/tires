<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class MenuTranslation extends Model
{
    use HasFactory;
    protected $fillable = [
        'menu_id',
        'locale',
        'name',
        'description',
    ];
    public $timestamps = true;
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }
    public function scopeForLocale($query, string $locale)
    {
        return $query->where('locale', $locale);
    }
    public function scopeWithFallback($query, string $locale, string $fallbackLocale = 'en')
    {
        return $query->whereIn('locale', [$locale, $fallbackLocale])
                    ->orderByRaw("CASE WHEN locale = ? THEN 1 ELSE 2 END", [$locale]);
    }
}
