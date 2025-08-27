<?php

namespace App\Services;

use App\Models\Questionnaire;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;

interface QuestionnaireServiceInterface
{
    public function getAllQuestionnaires(): Collection;
    public function getPaginatedQuestionnaires(int $perPage = 15): LengthAwarePaginator;
    public function getPaginatedQuestionnairesWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function findQuestionnaire(int $id): ?Questionnaire;
    public function createQuestionnaire(array $data): Questionnaire;
    public function updateQuestionnaire(int $id, array $data): ?Questionnaire;
    public function deleteQuestionnaire(int $id): bool;
    public function findQuestionnaireByReservation(int $reservationId): ?Questionnaire;
    public function validateQuestionnaireAnswers(array $questionsAndAnswers): bool;
    public function searchQuestionnairesWithCursor(string $query, int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getQuestionnairesByCompletionStatusWithCursor(string $status, int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getCompletionStatistics(): array;
}
