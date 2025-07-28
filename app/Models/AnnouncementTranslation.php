<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnnouncementTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'announcement_id',
        'locale',
        'title',
        'content',
    ];

    public $timestamps = true;

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(Announcement::class);
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