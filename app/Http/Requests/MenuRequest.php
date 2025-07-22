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
            'name' => 'required|string|max:255',
            'required_time' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'photo_path' => 'nullable|string|max:255',
            'display_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'color' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'required_time.required' => 'The required time field is required.',
            'required_time.integer' => 'The required time must be an integer.',
            'required_time.min' => 'The required time must be at least 1 minute.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',
            'description.string' => 'The description must be a string.',
            'photo_path.string' => 'The photo path must be a string.',
            'photo_path.max' => 'The photo path may not be greater than 255 characters.',
            'display_order.integer' => 'The display order must be an integer.',
            'display_order.min' => 'The display order must be at least 0.',
            'is_active.boolean' => 'The is active field must be true or false.',
        ];
    }
}