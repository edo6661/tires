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
            'website_url' => 'nullable|url|max:255',
            'site_name' => 'nullable|string|max:255',
            'shop_description' => 'nullable|string',
            'top_image_path' => 'nullable|string|max:255',
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
            'shop_name.required' => 'The shop name field is required.',
            'shop_name.string' => 'The shop name must be a string.',
            'shop_name.max' => 'The shop name may not be greater than 255 characters.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number may not be greater than 20 characters.',
            'address.required' => 'The address field is required.',
            'address.string' => 'The address must be a string.',
            'access_information.string' => 'The access information must be a string.',
            'business_hours.required' => 'The business hours field is required.',
            'business_hours.array' => 'The business hours must be an array.',
            'website_url.url' => 'The website URL must be a valid URL.',
            'website_url.max' => 'The website URL may not be greater than 255 characters.',
            'site_name.string' => 'The site name must be a string.',
            'site_name.max' => 'The site name may not be greater than 255 characters.',
            'shop_description.string' => 'The shop description must be a string.',
            'top_image_path.string' => 'The top image path must be a string.',
            'top_image_path.max' => 'The top image path may not be greater than 255 characters.',
            'site_public.boolean' => 'The site public field must be true or false.',
            'reply_email.email' => 'The reply email must be a valid email address.',
            'reply_email.max' => 'The reply email may not be greater than 255 characters.',
            'terms_of_use.string' => 'The terms of use must be a string.',
            'privacy_policy.string' => 'The privacy policy must be a string.',
            'google_analytics_id.string' => 'The google analytics ID must be a string.',
            'google_analytics_id.max' => 'The google analytics ID may not be greater than 255 characters.',
        ];
    }
}