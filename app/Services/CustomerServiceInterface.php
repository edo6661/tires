<?php

namespace App\Services;

use Illuminate\Support\Collection; // Ganti dari Illuminate\Database\Eloquent\Collection
use Illuminate\Pagination\LengthAwarePaginator;

interface CustomerServiceInterface
{
    public function getCustomers(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function getCustomerDetail(int $id): ?array;
    public function getFirstTimeCustomers(): Collection;
    public function getRepeatCustomers(): Collection;
    public function getDormantCustomers(): Collection;
    public function getCustomerReservationHistory(int $customerId, ?int $userId = null): Collection;
    public function getCustomerTireStorage(int $customerId, ?int $userId = null): Collection;
    public function getCustomerStats(int $customerId, ?int $userId = null): array;
    public function searchCustomers(string $search): Collection;
    public function getCustomerTypeCounts(): array;
}