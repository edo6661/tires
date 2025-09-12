<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = App::getLocale();

        return [
            'id' => $this->id,
            'title' => $this->getTranslatedAttribute('title', $locale),
            'content' => $this->getTranslatedAttribute('content', $locale),
            'is_active' => $this->is_active,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Always include all translations
            'translations' => $this->translations ? $this->translations->mapWithKeys(function ($translation) {
                return [
                    $translation->locale => [
                        'title' => $translation->title,
                        'content' => $translation->content,
                    ]
                ];
            }) : (object)[],

            'meta' => [
                'locale' => $locale,
                'fallback_used' => $this->isFallbackUsed($locale)
            ]
        ];
    }

    protected function isFallbackUsed(string $locale): bool
    {
        $translation = $this->translation($locale);

        if (!$translation || empty($translation->title)) {
            return true;
        }

        return false;
    }
}
