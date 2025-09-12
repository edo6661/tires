<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
            'translations.en.title' => 'required|string|max:255',
            'translations.en.content' => 'required|string',
            'translations.ja.title' => 'required|string|max:255',
            'translations.ja.content' => 'required|string',
        ];
    }
    public function messages(): array
    {
        return [
            'translations.en.title.required' => 'The title in English must be filled in.',
            'translations.en.title.string' => 'The title in English must be in text form.',
            'translations.en.title.max' => 'The title in English must not exceed 255 characters.',
            'translations.en.content.required' => 'The content in English must be filled in.',
            'translations.en.content.string' => 'The content in English must be in text form.',
            'translations.ja.title.required' => 'The title in Japanese must be filled in.',
            'translations.ja.title.string' => 'The title in Japanese must be in text form.',
            'translations.ja.title.max' => 'The title in Japanese must not exceed 255 characters.',
            'translations.ja.content.required' => 'The content in Japanese must be filled in.',
            'translations.ja.content.string' => 'The content in Japanese must be in text form.',
            'is_active.boolean' => 'The active status must be either true or false.',
            'published_at.date' => 'The publication date must be a valid date.',
        ];
    }
    /**
     * Prepare data for validation
     */
    protected function prepareForValidation()
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => $this->boolean('is_active')
            ]);
        }
        if (empty($this->published_at)) {
            $this->merge([
                'published_at' => now()
            ]);
        }
    }
    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'translations.en.title' => 'English title',
            'translations.en.content' => 'English content',
            'translations.ja.title' => 'Japanese title',
            'translations.ja.content' => 'Japanese content',
            'is_active' => 'Active Status',
            'published_at' => 'Published Date',
        ];
    }
}
