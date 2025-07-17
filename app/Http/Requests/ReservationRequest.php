<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reservation_number' => 'required|string|max:255|unique:reservations,reservation_number,' . $this->route('reservation'),
            'user_id' => 'nullable|exists:users,id',
            'menu_id' => 'required|exists:menus,id',
            'reservation_datetime' => 'required|date|after:now',
            'number_of_people' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'status' => 'in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
            
            // Field kredensial user - required jika user_id null
            'full_name' => [
                Rule::requiredIf(function () {
                    return is_null($this->input('user_id'));
                }),
                'nullable',
                'string',
                'max:255'
            ],
            'full_name_kana' => [
                Rule::requiredIf(function () {
                    return is_null($this->input('user_id'));
                }),
                'nullable',
                'string',
                'max:255'
            ],
            'email' => [
                Rule::requiredIf(function () {
                    return is_null($this->input('user_id'));
                }),
                'nullable',
                'email',
                'max:255'
            ],
            'phone_number' => [
                Rule::requiredIf(function () {
                    return is_null($this->input('user_id'));
                }),
                'nullable',
                'string',
                'max:20'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'reservation_number.required' => 'The reservation number field is required.',
            'reservation_number.string' => 'The reservation number must be a string.',
            'reservation_number.max' => 'The reservation number may not be greater than 255 characters.',
            'reservation_number.unique' => 'The reservation number has already been taken.',
            'user_id.required' => 'The user ID field is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'menu_id.required' => 'The menu ID field is required.',
            'menu_id.exists' => 'The selected menu does not exist.',
            'reservation_datetime.required' => 'The reservation datetime field is required.',
            'reservation_datetime.date' => 'The reservation datetime must be a valid date.',
            'reservation_datetime.after' => 'The reservation datetime must be after now.',
            'number_of_people.required' => 'The number of people field is required.',
            'number_of_people.integer' => 'The number of people must be an integer.',
            'number_of_people.min' => 'The number of people must be at least 1.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'status.in' => 'The selected status is invalid.',
            'notes.string' => 'The notes must be a string.',
            'full_name.required' => 'The full name field is required when user ID is not provided.',
            'full_name.string' => 'The full name must be a string.',
            'full_name.max' => 'The full name may not be greater than 255 characters.',
            'full_name_kana.required' => 'The full name kana field is required when user ID is not provided.',
            'full_name_kana.string' => 'The full name kana must be a string.',
            'full_name_kana.max' => 'The full name kana may not be greater than 255 characters.',
            'email.required' => 'The email field is required when user ID is not provided.',
            'email.email' => 'The email must be a valid email address.',
            'email.max' => 'The email may not be greater than 255 characters.',
            'phone_number.required' => 'The phone number field is required when user ID is not provided.',
            'phone_number.string' => 'The phone number must be a string.',
            'phone_number.max' => 'The phone number may not be greater than 20 characters.',
        ];
    }
}