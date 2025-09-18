<?php

namespace App\Repositories;

use Illuminate\Support\Collection; // Ganti dari Illuminate\Database\Eloquent\Collection
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface CustomerRepositoryInterface
{
    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getPaginatedWithCursor(array $filters = [], int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function findById(int $id): ?array;
    public function getFirstTimeCustomers(): Collection; // Sekarang menggunakan Support\Collection
    public function getRepeatCustomers(): Collection;
    public function getDormantCustomers(): Collection;
    public function getFirstTimeCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getRepeatCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getDormantCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getCustomerReservationHistory(int $customerId, ?int $userId = null): Collection;
    public function getCustomerTireStorage(int $customerId, ?int $userId = null): Collection;
    public function getCustomerStats(int $customerId, ?int $userId = null): array;
    public function searchCustomers(string $search): Collection;
}
