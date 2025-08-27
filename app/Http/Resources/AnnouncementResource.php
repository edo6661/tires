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
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,

            // Semua terjemahan (opsional jika ?include_translations di query)
            'translations' => $this->when($request->has('include_translations'), function () {
                return $this->translations->mapWithKeys(function ($translation) {
                    return [
                        $translation->locale => [
                            'title' => $translation->title,
                            'content' => $translation->content,
                        ]
                    ];
                });
            }),

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
