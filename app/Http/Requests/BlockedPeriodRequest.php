<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlockedPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_id' => 'nullable|exists:menus,id',
            'start_datetime' => 'required|date|after_or_equal:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'reason' => 'required|string|max:255',
            'all_menus' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'menu_id.exists' => 'The selected menu does not exist.',
            'start_datetime.required' => 'The start datetime field is required.',
            'start_datetime.date' => 'The start datetime must be a valid date.',
            'start_datetime.after_or_equal' => 'The start datetime must be after or equal to now.',
            'end_datetime.required' => 'The end datetime field is required.',
            'end_datetime.date' => 'The end datetime must be a valid date.',
            'end_datetime.after' => 'The end datetime must be after the start datetime.',
            'reason.required' => 'The reason field is required.',
            'reason.string' => 'The reason must be a string.',
            'reason.max' => 'The reason may not be greater than 255 characters.',
            'all_menus.boolean' => 'The all menus field must be true or false.',
        ];
    }
}
