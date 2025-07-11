<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionnaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reservation_id' => 'required|exists:reservations,id',
            'questions_and_answers' => 'required|array',
            'questions_and_answers.*.question' => 'required|string',
            'questions_and_answers.*.answer' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'reservation_id.required' => 'The reservation ID field is required.',
            'reservation_id.exists' => 'The selected reservation does not exist.',
            'questions_and_answers.required' => 'The questions and answers field is required.',
            'questions_and_answers.array' => 'The questions and answers must be an array.',
            'questions_and_answers.*.question.required' => 'Each question is required.',
            'questions_and_answers.*.question.string' => 'Each question must be a string.',
            'questions_and_answers.*.answer.required' => 'Each answer is required.',
            'questions_and_answers.*.answer.string' => 'Each answer must be a string.',
        ];
    }
}