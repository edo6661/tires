<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Repositories\QuestionnaireRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator; // ADD THIS

class QuestionnaireService implements QuestionnaireServiceInterface
{
    protected $questionnaireRepository;

    public function __construct(QuestionnaireRepositoryInterface $questionnaireRepository)
    {
        $this->questionnaireRepository = $questionnaireRepository;
    }

    public function getAllQuestionnaires(): Collection
    {
        return $this->questionnaireRepository->getAll();
    }

    public function getPaginatedQuestionnaires(int $perPage = 15): LengthAwarePaginator
    {
        return $this->questionnaireRepository->getPaginated($perPage);
    }

    // ADD CURSOR PAGINATION METHODS
    public function getPaginatedQuestionnairesWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->questionnaireRepository->getCursorPaginated($perPage, $cursor);
    }

    public function searchQuestionnairesWithCursor(string $query, int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        if (empty(trim($query))) {
            throw new \InvalidArgumentException('Search query cannot be empty');
        }

        return $this->questionnaireRepository->searchWithCursor($query, $perPage, $cursor);
    }

    public function getQuestionnairesByCompletionStatusWithCursor(string $status, int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        $allowedStatuses = ['completed', 'incomplete', 'partial'];
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException('Invalid completion status: ' . $status);
        }

        return $this->questionnaireRepository->getByCompletionStatusWithCursor($status, $perPage, $cursor);
    }

    public function getCompletionStatistics(): array
    {
        return $this->questionnaireRepository->getCompletionStats();
    }

    public function findQuestionnaire(int $id): ?Questionnaire
    {
        return $this->questionnaireRepository->findById($id);
    }

    public function createQuestionnaire(array $data): Questionnaire
    {
        if (!$this->validateQuestionnaireAnswers($data['questions_and_answers'])) {
            throw new \InvalidArgumentException('Invalid questionnaire format');
        }

        // Validate that reservation doesn't already have a questionnaire
        $existingQuestionnaire = $this->questionnaireRepository->findByReservationId($data['reservation_id']);
        if ($existingQuestionnaire) {
            throw new \InvalidArgumentException('Questionnaire already exists for this reservation');
        }

        return $this->questionnaireRepository->create($data);
    }

    public function updateQuestionnaire(int $id, array $data): ?Questionnaire
    {
        if (isset($data['questions_and_answers']) && !$this->validateQuestionnaireAnswers($data['questions_and_answers'])) {
            throw new \InvalidArgumentException('Invalid questionnaire format');
        }

        return $this->questionnaireRepository->update($id, $data);
    }

    public function deleteQuestionnaire(int $id): bool
    {
        return $this->questionnaireRepository->delete($id);
    }

    public function findQuestionnaireByReservation(int $reservationId): ?Questionnaire
    {
        return $this->questionnaireRepository->findByReservationId($reservationId);
    }

    public function validateQuestionnaireAnswers(array $questionsAndAnswers): bool
    {
        if (!is_array($questionsAndAnswers) || empty($questionsAndAnswers)) {
            return false;
        }

        foreach ($questionsAndAnswers as $item) {
            if (!is_array($item)) {
                return false;
            }

            // Check required fields
            if (!isset($item['question']) || !isset($item['answer'])) {
                return false;
            }

            // Check data types
            if (!is_string($item['question']) || !is_string($item['answer'])) {
                return false;
            }

            // Check minimum length
            if (strlen(trim($item['question'])) === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get questionnaire answer summary
     */
    public function getAnswerSummary(int $questionnaireId): array
    {
        $questionnaire = $this->findQuestionnaire($questionnaireId);

        if (!$questionnaire) {
            throw new \InvalidArgumentException('Questionnaire not found');
        }

        $answers = $questionnaire->questions_and_answers;
        $totalQuestions = count($answers);
        $answeredQuestions = 0;
        $emptyAnswers = 0;

        foreach ($answers as $qa) {
            if (!empty(trim($qa['answer']))) {
                $answeredQuestions++;
            } else {
                $emptyAnswers++;
            }
        }

        $completionPercentage = $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100, 2) : 0;

        return [
            'questionnaire_id' => $questionnaireId,
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'empty_answers' => $emptyAnswers,
            'completion_percentage' => $completionPercentage,
            'status' => $this->determineCompletionStatus($completionPercentage),
            'reservation_number' => $questionnaire->reservation?->reservation_number,
            'customer_name' => $questionnaire->reservation?->user?->full_name,
        ];
    }

    /**
     * Determine completion status based on percentage
     */
    private function determineCompletionStatus(float $percentage): string
    {
        if ($percentage === 0.0) {
            return 'incomplete';
        } elseif ($percentage === 100.0) {
            return 'completed';
        } else {
            return 'partial';
        }
    }

    /**
     * Get questionnaires with filters
     */
    public function getFilteredQuestionnaires(array $filters, int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        // This could be extended to handle multiple filters
        if (isset($filters['completion_status'])) {
            return $this->getQuestionnairesByCompletionStatusWithCursor(
                $filters['completion_status'],
                $perPage,
                $cursor
            );
        }

        if (isset($filters['search'])) {
            return $this->searchQuestionnairesWithCursor(
                $filters['search'],
                $perPage,
                $cursor
            );
        }

        return $this->getPaginatedQuestionnairesWithCursor($perPage, $cursor);
    }
}
