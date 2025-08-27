<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionnaireResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reservation_id' => $this->reservation_id,
            'questions_and_answers' => $this->questions_and_answers,
            'total_questions' => count($this->questions_and_answers ?? []),
            'answered_questions' => $this->getAnsweredQuestionsCount(),
            'completion_rate' => $this->getCompletionRate(),
            
            // Relations
            'reservation' => $this->whenLoaded('reservation', function () {
                return new ReservationResource($this->reservation);
            }),

            // Timestamps
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get count of answered questions
     */
    private function getAnsweredQuestionsCount(): int
    {
        if (!$this->questions_and_answers) {
            return 0;
        }

        $answeredCount = 0;
        foreach ($this->questions_and_answers as $qa) {
            if (!empty($qa['answer']) && trim($qa['answer']) !== '') {
                $answeredCount++;
            }
        }

        return $answeredCount;
    }

    /**
     * Get completion rate percentage
     */
    private function getCompletionRate(): float
    {
        $totalQuestions = count($this->questions_and_answers ?? []);
        
        if ($totalQuestions === 0) {
            return 0;
        }

        $answeredQuestions = $this->getAnsweredQuestionsCount();
        return round(($answeredQuestions / $totalQuestions) * 100, 2);
    }
}

