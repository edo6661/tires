<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:menus,id',
            'status' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'The IDs field is required.',
            'ids.array' => 'The IDs must be an array.',
            'ids.*.integer' => 'Each ID must be an integer.',
            'ids.*.exists' => 'One or more selected menus do not exist.',
            'status.required' => 'The status field is required.',
            'status.boolean' => 'The status must be true or false.',
        ];
    }
}
