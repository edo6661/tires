<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class BusinessSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Prefer explicit locale param, then X-Locale header, then App::getLocale()
        $locale = $request->input('locale') ?? $request->header('X-Locale') ?? App::getLocale();

        return [
            'id' => $this->id,
            'shop_name' => $this->getTranslatedAttribute('shop_name', $locale),
            'site_name' => $this->getTranslatedAttribute('site_name', $locale),
            'shop_description' => $this->getTranslatedAttribute('shop_description', $locale),
            'access_information' => $this->getTranslatedAttribute('access_information', $locale),
            'terms_of_use' => $this->getTranslatedAttribute('terms_of_use', $locale),
            'privacy_policy' => $this->getTranslatedAttribute('privacy_policy', $locale),
            'address' => $this->getTranslatedAttribute('address', $locale),
            'phone_number' => $this->phone_number,
            'business_hours' => $this->business_hours,
            'website_url' => $this->website_url,
            'top_image_path' => $this->top_image_path,
            'top_image_url' => $this->path_top_image_url,
            'site_public' => $this->site_public,
            'reply_email' => $this->reply_email,
            'google_analytics_id' => $this->google_analytics_id,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            // Always include all translations
            'translations' => $this->translations ? $this->translations->mapWithKeys(function ($translation) {
                return [
                    $translation->locale => [
                        'shop_name' => $translation->shop_name,
                        'site_name' => $translation->site_name,
                        'shop_description' => $translation->shop_description,
                        'access_information' => $translation->access_information,
                        'terms_of_use' => $translation->terms_of_use,
                        'privacy_policy' => $translation->privacy_policy,
                        'address' => $translation->address,
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

        if (!$translation || empty($translation->shop_name)) {
            return true;
        }

        return false;
    }
}
