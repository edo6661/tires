<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BusinessSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shop_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'access_information' => 'nullable|string',
            'business_hours' => 'required|array',
            'business_hours.*' => 'required|array',
            'business_hours.*.closed' => 'sometimes|boolean',
            'business_hours.*.open' => 'required_without:business_hours.*.closed|date_format:H:i|nullable',
            'business_hours.*.close' => 'required_without:business_hours.*.closed|date_format:H:i|nullable|after:business_hours.*.open',
            'website_url' => 'nullable|url|max:255',
            'site_name' => 'nullable|string|max:255',
            'shop_description' => 'nullable|string',
            'top_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'site_public' => 'boolean',
            'reply_email' => 'nullable|email|max:255',
            'terms_of_use' => 'nullable|string',
            'privacy_policy' => 'nullable|string',
            'google_analytics_id' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'shop_name.required' => 'Shop name is required.',
            'phone_number.required' => 'Phone number is required.',
            'address.required' => 'Address is required.',
            'business_hours.required' => 'Business hours are required.',
            'business_hours.*.open.required_without' => 'Opening time is required if the shop is not closed.',
            'business_hours.*.close.required_without' => 'Closing time is required if the shop is not closed.',
            'business_hours.*.open.date_format' => 'Opening time must be in HH:MM format.',
            'business_hours.*.close.date_format' => 'Closing time must be in HH:MM format.',
            'business_hours.*.close.after' => 'Closing time must be after opening time.',
            'website_url.url' => 'Website URL format is invalid.',
            'reply_email.email' => 'Reply email format is invalid.',
            'top_image.image' => 'The file must be an image.',
            'top_image.mimes' => 'Image must be in one of the following formats: jpeg, png, jpg, gif.',
            'top_image.max' => 'Image size must not exceed 2MB.',
        ];
    }

    protected function prepareForValidation()
    {
        $businessHours = $this->input('business_hours', []);
        $transformedHours = [];

        foreach ($businessHours as $day => $hours) {
            if (isset($hours['closed']) && $hours['closed'] == '1') {
                $transformedHours[$day] = ['closed' => true];
            } else {
                $transformedHours[$day] = [
                    'open' => $hours['open'] ?? null,
                    'close' => $hours['close'] ?? null,
                    'closed' => false
                ];
            }
        }

        $this->merge([
            'business_hours' => $transformedHours,
            'site_public' => $this->has('site_public') ? true : false,
        ]);
    }
}
