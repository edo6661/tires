<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Payment;

interface PaymentRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Payment;
    public function create(array $data): Payment;
    public function update(int $id, array $data): ?Payment;
    public function delete(int $id): bool;
    public function getByUserId(int $userId): Collection;
    public function getByReservationId(int $reservationId): Collection;
    public function getByStatus(string $status): Collection;
    public function getPaymentHistory(int $userId): Collection;
    public function getTotalRevenue(): float;
}
