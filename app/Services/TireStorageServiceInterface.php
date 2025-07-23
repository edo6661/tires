<?php

namespace App\Services;

use App\Models\TireStorage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface TireStorageServiceInterface
{
    public function getAllTireStorages(): Collection;
    public function getPaginatedTireStorages(int $perPage = 15): LengthAwarePaginator;
    public function findTireStorage(int $id): ?TireStorage;
    public function createTireStorage(array $data): TireStorage;
    public function updateTireStorage(int $id, array $data): ?TireStorage;
    public function deleteTireStorage(int $id): bool;
    public function getTireStoragesByUser(int $userId): Collection;
    public function getActiveTireStorages(): Collection;
    public function getEndedTireStorages(): Collection;
    public function getTireStoragesByStatus(string $status): Collection;
    public function getActiveTireStoragesByUser(int $userId): Collection;
    public function getEndedTireStoragesByUser(int $userId): Collection;
    public function endTireStorage(int $id): bool;
    public function calculateStorageFee(int $id): float;
    public function getPaginatedTireStoragesWithFilters(int $perPage = 15, array $filters = []): LengthAwarePaginator;   
}