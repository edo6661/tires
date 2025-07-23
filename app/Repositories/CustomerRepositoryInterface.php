<?php

namespace App\Repositories;

use Illuminate\Support\Collection; // Ganti dari Illuminate\Database\Eloquent\Collection
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?array;
    public function getFirstTimeCustomers(): Collection; // Sekarang menggunakan Support\Collection
    public function getRepeatCustomers(): Collection;
    public function getDormantCustomers(): Collection;
    public function getCustomerReservationHistory(int $customerId, ?int $userId = null): Collection;
    public function getCustomerTireStorage(int $customerId, ?int $userId = null): Collection;
    public function getCustomerStats(int $customerId, ?int $userId = null): array;
    public function searchCustomers(string $search): Collection;
}