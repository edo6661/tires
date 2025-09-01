<?php


namespace App\Repositories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

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
    public function getByDateRangeAndMenu(string $startDate, string $endDate, int $menuId, ?int $excludeReservationId = null): Collection;
    public function getCursorPaginated(int $perPage = 15, ?string $cursor = null): CursorPaginator;

    // Customer-specific methods
    public function getByUserIdWithCursor(int $userId, int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getCountByUserId(int $userId): int;
    public function getRecentByUserId(int $userId, int $limit = 5): Collection;
    public function getByUserIdAndStatus(int $userId, string $status): Collection;
    public function getByUserIdAndStatusWithCursor(int $userId, string $status, int $perPage = 15, ?string $cursor = null): CursorPaginator;
    public function getCountByUserIdAndStatus(int $userId, string $status): int;
}
