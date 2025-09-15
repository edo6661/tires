<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class MenuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Check for X-Locale header first, then fall back to App::getLocale()
        $locale = $request->header('X-Locale') ?? App::getLocale();

        return [
            'id' => $this->id,
            'name' => $this->getTranslatedAttribute('name', $locale),
            'description' => $this->getTranslatedAttribute('description', $locale),
            'required_time' => $this->required_time,
            'price' => [
                'amount' => $this->price,
                'formatted' => $this->getFormattedPrice($locale),
                'currency' => $this->getCurrency($locale)
            ],
            'photo_path' => $this->photo_path ? url($this->photo_path) : null,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'color' => [
                'hex' => $this->color,
                'rgba_light' => $this->getColorWithOpacity(10),
                'text_color' => $this->getTextColor()
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Always include all translations
            'translations' => $this->translations ? $this->translations->mapWithKeys(function ($translation) {
                return [
                    $translation->locale => [
                        'name' => $translation->name,
                        'description' => $translation->description
                    ]
                ];
            }) : (object)[],

            'meta' => [
                'locale' => $locale,
                'fallback_used' => $this->isFallbackUsed($locale)
            ]
        ];
    }

    protected function getFormattedPrice(string $locale): string
    {
        $formatters = [
            'en' => fn($price) => '$' . number_format($price, 2),
            'ja' => fn($price) => '¥' . number_format($price, 0)
        ];

        $formatter = $formatters[$locale] ?? $formatters['en'];
        return $formatter($this->price);
    }

    protected function getCurrency(string $locale): string
    {
        $currencies = [
            'en' => 'USD',
            'ja' => 'JPY'
        ];

        return $currencies[$locale] ?? $currencies['en'];
    }

    protected function isFallbackUsed(string $locale): bool
    {
        $translation = $this->translation($locale);

        if (!$translation || empty($translation->name)) {
            return true;
        }

        return false;
    }
}
