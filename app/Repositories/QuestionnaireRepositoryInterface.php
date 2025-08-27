<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator; // ADD THIS
use App\Models\Questionnaire;

interface QuestionnaireRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;


    public function getCursorPaginated(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function searchWithCursor(string $query, int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getByCompletionStatusWithCursor(string $status, int $perPage = 15, ?string $cursor = null): CursorPaginator;

    public function findById(int $id): ?Questionnaire;
    public function create(array $data): Questionnaire;
    public function update(int $id, array $data): ?Questionnaire;
    public function delete(int $id): bool;
    public function findByReservationId(int $reservationId): ?Questionnaire;


    public function getCompletionStats(): array;
    public function search(string $query): Collection;
}
