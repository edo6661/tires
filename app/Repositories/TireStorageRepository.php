<?php

namespace App\Repositories;

use App\Models\TireStorage;
use App\Repositories\TireStorageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TireStorageRepository implements TireStorageRepositoryInterface
{
    protected $model;

    public function __construct(TireStorage $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->with('user')->orderBy('created_at', 'desc')->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?TireStorage
    {
        return $this->model->with('user')->find($id);
    }

    public function create(array $data): TireStorage
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?TireStorage
    {
        $tireStorage = $this->findById($id);
        if ($tireStorage) {
            $tireStorage->update($data);
            return $tireStorage;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $tireStorage = $this->findById($id);
        if ($tireStorage) {
            return $tireStorage->delete();
        }
        return false;
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getActiveStorages(): Collection
    {
        return $this->model->with('user')
            ->where('status', 'active')
            ->orderBy('storage_start_date', 'desc')
            ->get();
    }

    public function getEndedStorages(): Collection
    {
        return $this->model->with('user')
            ->where('status', 'ended')
            ->orderBy('storage_start_date', 'desc')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->with('user')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getActiveByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('storage_start_date', 'desc')
            ->get();
    }

    public function getEndedByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->where('status', 'ended')
            ->orderBy('storage_start_date', 'desc')
            ->get();
    }
}
