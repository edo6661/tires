<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class MenuIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1|max:100',
            'paginate' => 'sometimes|in:true,false',
            'locale' => 'sometimes|string|in:en,ja'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = collect($validator->errors()->all())->map(function ($message, $index) {
            return [
                'field' => 'request_params',
                'tag' => 'validation_error',
                'value' => request()->all(),
                'message' => $message
            ];
        })->values()->toArray();

        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'code' => 422,
                'error' => $errors
            ], 422)
        );
    }
}
