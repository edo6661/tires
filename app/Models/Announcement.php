<?php
namespace App\Models;

use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory, Translatable;

    protected $fillable = [
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected $with = ['translations'];

    protected function getTranslatableFields(): array
    {
        return ['title', 'content'];
    }

    protected function getDefaultTranslation(string $attribute)
    {
        $defaults = [
            'title' => 'Untitled Announcement',
            'content' => 'No content available',
        ];
        return $defaults[$attribute] ?? null;
    }

    public function scopeTranslated($query, ?string $locale = null)
    {
        return $query->withTranslations($locale);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function getTitleInLocale(string $locale): ?string
    {
        return $this->getTranslatedAttribute('title', $locale);
    }

    public function getContentInLocale(string $locale): ?string
    {
        return $this->getTranslatedAttribute('content', $locale);
    }
}