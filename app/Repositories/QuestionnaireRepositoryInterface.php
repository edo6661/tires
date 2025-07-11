<?php


namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Questionnaire;

interface QuestionnaireRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Questionnaire;
    public function create(array $data): Questionnaire;
    public function update(int $id, array $data): ?Questionnaire;
    public function delete(int $id): bool;
    public function findByReservationId(int $reservationId): ?Questionnaire;
}
