<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TireStorageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'tire_brand' => 'required|string|max:255',
            'tire_size' => 'required|string|max:255',
            'storage_start_date' => 'required|date',
            'planned_end_date' => 'required|date|after:storage_start_date',
            'storage_fee' => 'nullable|numeric|min:0',
            'status' => 'in:active,ended',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID field is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'tire_brand.required' => 'The tire brand field is required.',
            'tire_brand.string' => 'The tire brand must be a string.',
            'tire_brand.max' => 'The tire brand may not be greater than 255 characters.',
            'tire_size.required' => 'The tire size field is required.',
            'tire_size.string' => 'The tire size must be a string.',
            'tire_size.max' => 'The tire size may not be greater than 255 characters.',
            'storage_start_date.required' => 'The storage start date field is required.',
            'storage_start_date.date' => 'The storage start date must be a valid date.',
            'planned_end_date.required' => 'The planned end date field is required.',
            'planned_end_date.date' => 'The planned end date must be a valid date.',
            'planned_end_date.after' => 'The planned end date must be after the storage start date.',
            'storage_fee.numeric' => 'The storage fee must be a number.',
            'storage_fee.min' => 'The storage fee must be at least 0.',
            'status.in' => 'The selected status is invalid.',
            'notes.string' => 'The notes must be a string.',
        ];
    }
}
