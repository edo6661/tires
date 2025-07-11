<?php

namespace App\Repositories;

use App\Models\Questionnaire;
use App\Repositories\QuestionnaireRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class QuestionnaireRepository implements QuestionnaireRepositoryInterface
{
    protected $model;

    public function __construct(Questionnaire $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->with('reservation')->orderBy('created_at', 'desc')->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('reservation')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Questionnaire
    {
        return $this->model->with('reservation')->find($id);
    }

    public function create(array $data): Questionnaire
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Questionnaire
    {
        $questionnaire = $this->findById($id);
        if ($questionnaire) {
            $questionnaire->update($data);
            return $questionnaire;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $questionnaire = $this->findById($id);
        if ($questionnaire) {
            return $questionnaire->delete();
        }
        return false;
    }

    public function findByReservationId(int $reservationId): ?Questionnaire
    {
        return $this->model->where('reservation_id', $reservationId)->first();
    }
}