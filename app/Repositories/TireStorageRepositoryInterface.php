<?php


namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\TireStorage;

interface TireStorageRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?TireStorage;
    public function create(array $data): TireStorage;
    public function update(int $id, array $data): ?TireStorage;
    public function delete(int $id): bool;
    public function getByUserId(int $userId): Collection;
    public function getActiveStorages(): Collection;
    public function getEndedStorages(): Collection;
    public function getByStatus(string $status): Collection;
    public function getActiveByUserId(int $userId): Collection;
    public function getEndedByUserId(int $userId): Collection;
    public function getPaginatedWithFilters(int $perPage = 15, array $filters = []): LengthAwarePaginator;
}
