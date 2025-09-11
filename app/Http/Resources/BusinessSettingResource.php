<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class BusinessSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = App::getLocale();

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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
}
}
