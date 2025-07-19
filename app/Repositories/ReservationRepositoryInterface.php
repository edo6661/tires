<?php


namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Reservation;

interface ReservationRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?Reservation;
    public function create(array $data): Reservation;
    public function update(int $id, array $data): ?Reservation;
    public function delete(int $id): bool;
    public function getByUserId(int $userId): Collection;
    public function getByMenuId(int $menuId): Collection;
    public function getByStatus(string $status): Collection;
    public function getByDateRange(string $startDate, string $endDate): Collection;
    public function getUpcoming(): Collection;
    public function getCompleted(): Collection;
    public function getCancelled(): Collection;
    public function getTodayReservations(): Collection;
    public function checkAvailability(int $menuId, string $datetime, ?int $excludeReservationId = null): bool;
    public function bulkUpdateStatus(array $ids, string $status): bool;
    public function getByDateRangeAndMenu(string $startDate, string $endDate, int $menuId): Collection;
}
