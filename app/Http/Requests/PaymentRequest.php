<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
            'status' => 'in:pending,completed,failed,refunded',
            'transaction_id' => 'nullable|string|max:255',
            'payment_details' => 'nullable|array',
            'paid_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user ID field is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'reservation_id.exists' => 'The selected reservation does not exist.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'payment_method.required' => 'The payment method field is required.',
            'payment_method.string' => 'The payment method must be a string.',
            'payment_method.max' => 'The payment method may not be greater than 255 characters.',
            'status.in' => 'The selected status is invalid.',
            'transaction_id.string' => 'The transaction ID must be a string.',
            'transaction_id.max' => 'The transaction ID may not be greater than 255 characters.',
            'payment_details.array' => 'The payment details must be an array.',
            'paid_at.date' => 'The paid at must be a valid date.',
        ];
    }
}
