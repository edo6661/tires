<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'user_id' => 'required|exists:users,id',
            'menu_id' => 'required|exists:menus,id',
            'reservation_datetime' => 'required|date|after:now',
            'number_of_people' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'status' => 'in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
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
        ];
    }
}