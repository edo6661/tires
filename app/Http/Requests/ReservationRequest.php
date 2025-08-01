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
        $reservationId = $this->route('reservation');
        return [
            'reservation_number' => 'nullable|string|max:255|unique:reservations,reservation_number,' . $reservationId,
            'user_id' => 'nullable|exists:users,id',
            'menu_id' => 'required|exists:menus,id',
            'reservation_datetime' => [
                'required',
                'date',
                $reservationId ? 'date' : 'after:now',
                function ($attribute, $value, $fail) use ($reservationId) {
                    if (!$this->input('menu_id') || !$value) {
                        return;
                    }
                    $reservationService = app(\App\Services\ReservationService::class);
                    $isAvailable = $reservationService->checkAvailability(
                        $this->input('menu_id'),
                        $value,
                        $reservationId 
                    );
                    if (!$isAvailable) {
                        $fail(__('admin/reservation/create.validation.reservation_datetime_unavailable'));
                    }
                }
            ],
            'number_of_people' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'status' => 'in:pending,confirmed,completed,cancelled',
            'notes' => 'nullable|string',
            'customer_type' => 'required|in:existing,guest', 
            'full_name' => [
                Rule::requiredIf(fn () => $this->input('customer_type') === 'guest'),
                'nullable',
                'string',
                'max:255'
            ],
            'full_name_kana' => [
                Rule::requiredIf(fn () => $this->input('customer_type') === 'guest'),
                'nullable',
                'string',
                'max:255'
            ],
            'email' => [
                Rule::requiredIf(fn () => $this->input('customer_type') === 'guest'),
                'nullable',
                'email',
                'max:255'
            ],
            'phone_number' => [
                Rule::requiredIf(fn () => $this->input('customer_type') === 'guest'),
                'nullable',
                'string',
                'max:20'
            ],
        ];
    }
    public function messages(): array
    {
        $prefix = 'admin/reservation/create.validation.';
        return [
            'user_id.required' => __($prefix . 'user_id_required'),
            'user_id.exists' => __($prefix . 'user_id_exists'),
            'menu_id.required' => __($prefix . 'menu_id_required'),
            'menu_id.exists' => __($prefix . 'menu_id_exists'),
            'reservation_datetime.required' => __($prefix . 'reservation_datetime_required'),
            'reservation_datetime.date' => __($prefix . 'reservation_datetime_date'),
            'reservation_datetime.after' => __($prefix . 'reservation_datetime_after'),
            'reservation_datetime_unavailable' => __($prefix . 'reservation_datetime_unavailable', [
                'datetime' => $this->input('reservation_datetime')
            ]),
            'number_of_people.required' => __($prefix . 'number_of_people_required'),
            'number_of_people.integer' => __($prefix . 'number_of_people_integer'),
            'number_of_people.min' => __($prefix . 'number_of_people_min'),
            'amount.required' => __($prefix . 'amount_required'),
            'amount.numeric' => __($prefix . 'amount_numeric'),
            'amount.min' => __($prefix . 'amount_min'),
            'status.in' => __($prefix . 'status_in'),
            'notes.string' => __($prefix . 'notes_string'),
            'full_name.required' => __($prefix . 'full_name_required'),
            'full_name.string' => __($prefix . 'full_name_string'),
            'full_name.max' => __($prefix . 'full_name_max'),
            'full_name_kana.required' => __($prefix . 'full_name_kana_required'),
            'full_name_kana.string' => __($prefix . 'full_name_kana_string'),
            'full_name_kana.max' => __($prefix . 'full_name_kana_max'),
            'email.required' => __($prefix . 'email_required'),
            'email.email' => __($prefix . 'email_email'),
            'email.max' => __($prefix . 'email_max'),
            'phone_number.required' => __($prefix . 'phone_number_required'),
            'phone_number.string' => __($prefix . 'phone_number_string'),
            'phone_number.max' => __($prefix . 'phone_number_max'),
        ];
    }
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        
    }
}