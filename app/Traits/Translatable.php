<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

trait Translatable
{
    protected static $translationCache = [];

    /**
     * Boot the trait
     */
    protected static function bootTranslatable()
    {
        // Clear cache ketika model di-update/delete
        static::saved(function ($model) {
            $model->clearTranslationCache();
        });

        static::deleted(function ($model) {
            $model->clearTranslationCache();
        });
    }

    /**
     * Get all translations for this model
     */
    public function translations(): HasMany
    {
        $relationName = $this->getTranslationModelName();
        return $this->hasMany($relationName);
    }

    /**
     * Get translation for specific locale with caching
     */
    public function translation(?string $locale = null)
    {
        $locale = $locale ?: App::getLocale();
        $cacheKey = $this->getTranslationCacheKey($locale);
        
        if (isset(self::$translationCache[$cacheKey])) {
            return self::$translationCache[$cacheKey];
        }

        $translation = $this->translations()
            ->where('locale', $locale)
            ->first();

        self::$translationCache[$cacheKey] = $translation;
        
        return $translation;
    }

    /**
     * Get translated attribute with fallback
     */
    public function getTranslatedAttribute(string $attribute, string $locale = null)
    {
        $locale = $locale ?: App::getLocale();
        
        // Coba dapatkan translation untuk locale yang diminta
        $translation = $this->translation($locale);
        
        if ($translation && !empty($translation->$attribute)) {
            return $translation->$attribute;
        }

        // Fallback ke default locale
        $fallbackLocale = config('app.fallback_locale', 'en');
        if ($locale !== $fallbackLocale) {
            $fallbackTranslation = $this->translation($fallbackLocale);
            if ($fallbackTranslation && !empty($fallbackTranslation->$attribute)) {
                return $fallbackTranslation->$attribute;
            }
        }

        // Jika masih tidak ada, return null atau default value
        return $this->getDefaultTranslation($attribute);
    }

    /**
     * Create or update translation
     */
    public function setTranslation(string $locale, array $attributes): self
    {
        $translation = $this->translations()
            ->where('locale', $locale)
            ->first();

        if ($translation) {
            $translation->update($attributes);
        } else {
            $this->translations()->create(array_merge($attributes, ['locale' => $locale]));
        }

        $this->clearTranslationCache();
        return $this;
    }

    /**
     * Bulk set translations
     */
    public function setTranslations(array $translations): self
    {
        foreach ($translations as $locale => $attributes) {
            $this->setTranslation($locale, $attributes);
        }
        return $this;
    }

    /**
     * Get translation model name
     */
    protected function getTranslationModelName(): string
    {
        $modelName = class_basename($this);
        return "App\\Models\\{$modelName}Translation";
    }

    /**
     * Scope untuk load translations
     */
    public function scopeWithTranslations($query, ?string $locale = null)
    {
        $locale = $locale ?: App::getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');
        
        return $query->with(['translations' => function ($query) use ($locale, $fallbackLocale) {
            $query->whereIn('locale', [$locale, $fallbackLocale])
                  ->orderByRaw("CASE WHEN locale = ? THEN 1 ELSE 2 END", [$locale]);
        }]);
    }

    /**
     * Scope untuk filter by translated field
     */
    public function scopeWhereTranslation($query, string $field, $value, string $locale = null)
    {
        $locale = $locale ?: App::getLocale();
        $relationName = $this->getTranslationModelName();
        
        return $query->whereHas('translations', function ($query) use ($field, $value, $locale) {
            $query->where('locale', $locale)
                  ->where($field, $value);
        });
    }

    /**
     * Magic method untuk akses attribute yang ditranslate
     */
    public function __get($key)
    {
        if (in_array($key, $this->getTranslatableFields())) {
            return $this->getTranslatedAttribute($key);
        }

        return parent::__get($key);
    }

    /**
     * Clear translation cache
     */
    protected function clearTranslationCache(): void
    {
        $pattern = $this->getTable() . ':' . $this->id . ':*';
        foreach (self::$translationCache as $key => $value) {
            if (str_starts_with($key, $this->getTable() . ':' . $this->id . ':')) {
                unset(self::$translationCache[$key]);
            }
        }
    }

    /**
     * Get translation cache key
     */
    protected function getTranslationCacheKey(string $locale): string
    {
        return $this->getTable() . ':' . $this->id . ':' . $locale;
    }

    /**
     * Get default translation for attribute
     */
    protected function getDefaultTranslation(string $attribute)
    {
        return null; // Override di model jika perlu default value
    }

    /**
     * Get translatable fields untuk model ini
     */
    abstract protected function getTranslatableFields(): array;
}