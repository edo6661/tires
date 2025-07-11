<?php


namespace App\Services;

use App\Models\Questionnaire;
use App\Repositories\QuestionnaireRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function findQuestionnaire(int $id): ?Questionnaire
    {
        return $this->questionnaireRepository->findById($id);
    }

    public function createQuestionnaire(array $data): Questionnaire
    {
        if (!$this->validateQuestionnaireAnswers($data['questions_and_answers'])) {
            throw new \InvalidArgumentException('Invalid questionnaire format');
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
        if (!is_array($questionsAndAnswers)) {
            return false;
        }

        foreach ($questionsAndAnswers as $item) {
            if (!is_array($item) || !isset($item['question']) || !isset($item['answer'])) {
                return false;
            }
        }

        return true;
    }
}
