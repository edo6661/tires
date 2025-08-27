<?php

namespace App\Repositories;

use App\Models\Questionnaire;
use App\Repositories\QuestionnaireRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\CursorPaginator;

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

    public function getCursorPaginated(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model
            ->with('reservation.user', 'reservation.menu')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function searchWithCursor(string $query, int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->model
            ->with('reservation.user', 'reservation.menu')
            ->where(function ($mainQuery) use ($query) {
                $mainQuery->whereHas('reservation', function ($q) use ($query) {
                    $q->where('reservation_number', 'like', "%{$query}%")
                      ->orWhereHas('user', function ($userQuery) use ($query) {
                          $userQuery->where('full_name', 'like', "%{$query}%")
                                   ->orWhere('email', 'like', "%{$query}%")
                                   ->orWhere('phone_number', 'like', "%{$query}%");
                      });
                      // Menu search dihapus untuk menghindari error
                })
                ->orWhere('questions_and_answers', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function getByCompletionStatusWithCursor(string $status, int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        $query = $this->model->with('reservation.user', 'reservation.menu');

        switch ($status) {
            case 'completed':
                // PostgreSQL safe approach - check if not null and has content
                $query->whereNotNull('questions_and_answers')
                      ->whereRaw("questions_and_answers::text != '[]'")
                      ->whereRaw("questions_and_answers::text != 'null'")
                      ->whereRaw("LENGTH(questions_and_answers::text) > 2");
                break;

            case 'incomplete':
                $query->where(function ($q) {
                    $q->whereNull('questions_and_answers')
                      ->orWhereRaw("questions_and_answers::text = '[]'")
                      ->orWhereRaw("questions_and_answers::text = 'null'")
                      ->orWhereRaw("LENGTH(questions_and_answers::text) <= 2");
                });
                break;

            case 'partial':
                // For partial - has some answers but might have empty ones
                $query->whereNotNull('questions_and_answers')
                      ->whereRaw("questions_and_answers::text != '[]'")
                      ->whereRaw("questions_and_answers::text != 'null'")
                      ->whereRaw("LENGTH(questions_and_answers::text) > 2");
                break;

            default:
                throw new \InvalidArgumentException('Invalid completion status: ' . $status);
        }

        return $query->orderBy('created_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    public function getCompletionStats(): array
    {
        $total = $this->model->count();

        // PostgreSQL safe approach
        $completed = $this->model
            ->whereNotNull('questions_and_answers')
            ->whereRaw("questions_and_answers::text != '[]'")
            ->whereRaw("questions_and_answers::text != 'null'")
            ->whereRaw("LENGTH(questions_and_answers::text) > 2")
            ->count();

        $incomplete = $this->model
            ->where(function ($q) {
                $q->whereNull('questions_and_answers')
                  ->orWhereRaw("questions_and_answers::text = '[]'")
                  ->orWhereRaw("questions_and_answers::text = 'null'")
                  ->orWhereRaw("LENGTH(questions_and_answers::text) <= 2");
            })
            ->count();

        $partial = $total - $completed - $incomplete;

        return [
            'total' => $total,
            'completed' => $completed,
            'incomplete' => $incomplete,
            'partial' => max(0, $partial),
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'completion_stats' => [
                'completed_percentage' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                'incomplete_percentage' => $total > 0 ? round(($incomplete / $total) * 100, 2) : 0,
                'partial_percentage' => $total > 0 ? round((max(0, $partial) / $total) * 100, 2) : 0,
            ]
        ];
    }

    public function search(string $query): Collection
    {
        return $this->model
            ->with('reservation.user', 'reservation.menu')
            ->where(function ($mainQuery) use ($query) {
                $mainQuery->whereHas('reservation', function ($q) use ($query) {
                    $q->where('reservation_number', 'like', "%{$query}%")
                      ->orWhereHas('user', function ($userQuery) use ($query) {
                          $userQuery->where('full_name', 'like', "%{$query}%")
                                   ->orWhere('email', 'like', "%{$query}%")
                                   ->orWhere('phone_number', 'like', "%{$query}%");
                      });
                      // Menu search dihapus untuk menghindari error
                })
                ->orWhere('questions_and_answers', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findById(int $id): ?Questionnaire
    {
        return $this->model->with('reservation.user', 'reservation.menu')->find($id);
    }

    public function create(array $data): Questionnaire
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Questionnaire
    {
        $questionnaire = $this->model->find($id);
        if ($questionnaire) {
            $questionnaire->update($data);
            return $questionnaire->load('reservation.user', 'reservation.menu');
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $questionnaire = $this->model->find($id);
        if ($questionnaire) {
            return $questionnaire->delete();
        }
        return false;
    }

    public function findByReservationId(int $reservationId): ?Questionnaire
    {
        return $this->model
            ->with('reservation.user', 'reservation.menu')
            ->where('reservation_id', $reservationId)
            ->first();
    }
}
