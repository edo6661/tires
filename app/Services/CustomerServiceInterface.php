<?php

namespace App\Services;

use Illuminate\Support\Collection; // Ganti dari Illuminate\Database\Eloquent\Collection
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface CustomerServiceInterface
{
    public function getCustomers(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getPaginatedCustomersWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator;
    public function getCustomerDetail(int $id): ?array;
    public function getFirstTimeCustomers(): Collection;
    public function getRepeatCustomers(): Collection;
    public function getDormantCustomers(): Collection;
    public function getFirstTimeCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getRepeatCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getDormantCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getCustomerReservationHistory(int $customerId, ?int $userId = null): Collection;
    public function getCustomerTireStorage(int $customerId, ?int $userId = null): Collection;
    public function getCustomerStats(int $customerId, ?int $userId = null): array;
    public function searchCustomers(string $search): Collection;
    public function searchCustomersWithCursor(string $search, int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator;
    public function getCustomerTypeCounts(): array;
}
