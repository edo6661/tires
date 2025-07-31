<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
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
        return [
            'menu_id' => [
                'nullable',
                'exists:menus,id',
                function ($attribute, $value, $fail) {
                    if (!$this->input('all_menus') && empty($value)) {
                        $fail(__('admin/blocked-period/create.validation.menu_required_if_not_all'));
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
                            $fail(__('admin/blocked-period/create.validation.start_before_end'));
                        }
                        if ($start->diffInMinutes($end) < 15) {
                            $fail(__('admin/blocked-period/create.validation.min_duration'));
                        }
                        if ($start->diffInDays($end) > 30) {
                            $fail(__('admin/blocked-period/create.validation.max_duration'));
                        }
                    }
                }
            ],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
            'reason' => ['required', 'string', 'max:500', 'min:3'],
            'all_menus' => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if (!is_bool($value)) {
                        $fail(__('admin/blocked-period/create.validation.all_menus_boolean'));
                    }
                }
            ]
        ];
    }
    public function messages(): array
    {
        return [
            'menu_id.exists' => __('admin/blocked-period/create.validation.menu_id.exists'),
            'start_datetime.required' => __('admin/blocked-period/create.validation.start_datetime.required'),
            'start_datetime.date' => __('admin/blocked-period/create.validation.start_datetime.date'),
            'start_datetime.after_or_equal' => __('admin/blocked-period/create.validation.start_datetime.after_or_equal'),
            'end_datetime.required' => __('admin/blocked-period/create.validation.end_datetime.required'),
            'end_datetime.date' => __('admin/blocked-period/create.validation.end_datetime.date'),
            'end_datetime.after' => __('admin/blocked-period/create.validation.end_datetime.after'),
            'reason.required' => __('admin/blocked-period/create.validation.reason.required'),
            'reason.string' => __('admin/blocked-period/create.validation.reason.string'),
            'reason.max' => __('admin/blocked-period/create.validation.reason.max'),
            'reason.min' => __('admin/blocked-period/create.validation.reason.min'),
            'all_menus.boolean' => __('admin/blocked-period/create.validation.all_menus_boolean'),
        ];
    }
    public function attributes(): array
    {
        return __('admin/blocked-period/create.attributes');
    }
    protected function prepareForValidation(): void
    {
        $this->merge(['all_menus' => $this->boolean('all_menus')]);
        if ($this->input('all_menus')) {
            $this->merge(['menu_id' => null]);
        }
        if ($this->has('start_datetime') && $this->input('start_datetime')) {
            try {
                $this->merge(['start_datetime' => Carbon::parse($this->input('start_datetime'))->format('Y-m-d H:i:s')]);
            } catch (\Exception $e) {}
        }
        if ($this->has('end_datetime') && $this->input('end_datetime')) {
            try {
                $this->merge(['end_datetime' => Carbon::parse($this->input('end_datetime'))->format('Y-m-d H:i:s')]);
            } catch (\Exception $e) {}
        }
    }
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->hasAny(['menu_id', 'start_datetime', 'end_datetime', 'all_menus'])) {
                $this->validateConflicts($validator);
            }
        });
    }
    private function validateConflicts($validator): void
    {
        $blockedPeriodService = app(BlockedPeriodServiceInterface::class);
        $excludeId = $this->route('blocked_period') ? (int) $this->route('blocked_period') : null;
        $hasConflict = $blockedPeriodService->checkScheduleConflictWithExclusion(
            $this->input('menu_id'), $this->input('start_datetime'), $this->input('end_datetime'), $this->input('all_menus', false), $excludeId
        );
        if ($hasConflict) {
            $conflictDetails = $blockedPeriodService->getConflictDetails(
                $this->input('menu_id'), $this->input('start_datetime'), $this->input('end_datetime'), $this->input('all_menus', false), $excludeId
            );
            $conflictMessageDetails = '';
            foreach ($conflictDetails as $conflict) {
                $conflictMessageDetails .= "\n- {$conflict['menu_name']}: {$conflict['start_datetime']} - {$conflict['end_datetime']} ({$conflict['reason']})";
            }
            $finalMessage = __('admin/blocked-period/create.validation.conflict_message', ['details' => $conflictMessageDetails]);
            $validator->errors()->add('start_datetime', $finalMessage);
        }
    }
}