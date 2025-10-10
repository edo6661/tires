<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'required_time' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'photo_path' => 'nullable|string|max:255',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'color' => 'nullable|string',
            'translations.en.name' => 'required|string|max:255',
            'translations.en.description' => 'nullable|string',
            'translations.ja.name' => 'required|string|max:255',
            'translations.ja.description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'translations.en.name.required' => 'The menu name in English is required.',
            'translations.ja.name.required' => 'The menu name in Japanese is required.',
            'required_time.required' => 'The required time field is required.',
            'required_time.min' => 'The required time must be at least 1 minute.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price cannot be less than 0.',
        ];
    }

    public function attributes(): array
    {
        return [
            'translations.en.name' => 'English Name',
            'translations.en.description' => 'English Description',
            'translations.ja.name' => 'Japanese Name',
            'translations.ja.description' => 'Japanese Description',
            'required_time' => 'Required Time',
            'price' => 'Price',
        ];
    }
}
