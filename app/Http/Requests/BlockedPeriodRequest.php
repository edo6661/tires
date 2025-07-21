<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\BlockedPeriodServiceInterface;
use Carbon\Carbon;
class BlockedPeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        $rules = [
            'menu_id' => [
                'nullable',
                'exists:menus,id',
                function ($attribute, $value, $fail) {
                    if (!$this->input('all_menus') && empty($value)) {
                        $fail('Menu harus dipilih jika tidak memblokir semua menu.');
                    }
                }
            ],
            'start_datetime' => [
                'required',
                'date',
                'after_or_equal:now',
                function ($attribute, $value, $fail) {
                    if ($this->input('end_datetime')) {
                        $start = Carbon::parse($value);
                        $end = Carbon::parse($this->input('end_datetime'));
                        if ($start->gte($end)) {
                            $fail('Waktu mulai harus sebelum waktu selesai.');
                        }
                        if ($start->diffInMinutes($end) < 15) {
                            $fail('Durasi minimum adalah 15 menit.');
                        }
                        if ($start->diffInDays($end) > 30) {
                            $fail('Durasi maksimum adalah 30 hari.');
                        }
                    }
                }
            ],
            'end_datetime' => [
                'required',
                'date',
                'after:start_datetime'
            ],
            'reason' => [
                'required',
                'string',
                'max:500',
                'min:3'
            ],
        ];
        return $rules;
    }
    public function messages(): array
    {
        return [
            'menu_id.exists' => 'Menu yang dipilih tidak valid.',
            'start_datetime.required' => 'Waktu mulai wajib diisi.',
            'start_datetime.date' => 'Format waktu mulai tidak valid.',
            'start_datetime.after_or_equal' => 'Waktu mulai tidak boleh sebelum waktu saat ini.',
            'end_datetime.required' => 'Waktu selesai wajib diisi.',
            'end_datetime.date' => 'Format waktu selesai tidak valid.',
            'end_datetime.after' => 'Waktu selesai harus setelah waktu mulai.',
            'reason.required' => 'Alasan pemblokiran wajib diisi.',
            'reason.string' => 'Alasan harus berupa teks.',
            'reason.max' => 'Alasan maksimal 500 karakter.',
            'reason.min' => 'Alasan minimal 3 karakter.',
            'all_menus.boolean' => 'Field semua menu harus bernilai true atau false.',
        ];
    }
    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'menu_id' => 'menu',
            'start_datetime' => 'waktu mulai',
            'end_datetime' => 'waktu selesai',
            'reason' => 'alasan',
            'all_menus' => 'semua menu',
        ];
    }
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'all_menus' => $this->boolean('all_menus'),
        ]);
        if ($this->input('all_menus')) {
            $this->merge(['menu_id' => null]);
        }
        if ($this->has('start_datetime') && $this->input('start_datetime')) {
            try {
                $startDateTime = Carbon::parse($this->input('start_datetime'));
                $this->merge(['start_datetime' => $startDateTime->format('Y-m-d H:i:s')]);
            } catch (\Exception $e) {
            }
        }
        if ($this->has('end_datetime') && $this->input('end_datetime')) {
            try {
                $endDateTime = Carbon::parse($this->input('end_datetime'));
                $this->merge(['end_datetime' => $endDateTime->format('Y-m-d H:i:s')]);
            } catch (\Exception $e) {
            }
        }
    }
    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->hasAny(['menu_id', 'start_datetime', 'end_datetime', 'all_menus'])) {
                $this->validateConflicts($validator);
            }
        });
    }
    /**
     * Validate time conflicts with existing blocked periods
     */
    private function validateConflicts($validator): void
    {
        $blockedPeriodService = app(BlockedPeriodServiceInterface::class);
        $menuId = $this->input('menu_id');
        $startDatetime = $this->input('start_datetime');
        $endDatetime = $this->input('end_datetime');
        $allMenus = $this->input('all_menus', false);
        $excludeId = $this->route('blocked_period') ? (int) $this->route('blocked_period') : null;
        $hasConflict = $blockedPeriodService->checkScheduleConflictWithExclusion(
            $menuId, 
            $startDatetime, 
            $endDatetime, 
            $allMenus, 
            $excludeId
        );
        if ($hasConflict) {
            $conflictDetails = $blockedPeriodService->getConflictDetails(
                $menuId, 
                $startDatetime, 
                $endDatetime, 
                $allMenus, 
                $excludeId
            );
            $conflictMessage = 'Terjadi konflik waktu dengan periode blokir berikut:';
            foreach ($conflictDetails as $conflict) {
                $conflictMessage .= "\n- {$conflict['menu_name']}: {$conflict['start_datetime']} - {$conflict['end_datetime']} ({$conflict['reason']})";
            }
            $validator->errors()->add('start_datetime', $conflictMessage);
        }
    }
}