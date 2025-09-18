<?php

namespace App\Repositories;

use App\Models\BlockedPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface BlockedPeriodRepositoryInterface
{
    public function getAll(): Collection;

    public function getPaginated(int $perPage = 15): LengthAwarePaginator;

    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getPaginatedWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator;

    public function getAllWithFilters(array $filters): Collection;

    public function findById(int $id): ?BlockedPeriod;

    public function create(array $data): BlockedPeriod;

    public function update(int $id, array $data): ?BlockedPeriod;

    public function delete(int $id): bool;

    public function getByMenuId(int $menuId): Collection;

    public function getByDateRange(string $startDate, string $endDate): Collection;

    public function getActiveBlocks(): Collection;

    public function checkConflict(int $menuId, string $startDatetime, string $endDatetime): bool;

    public function checkConflictWithExclusion(
        ?int $menuId,
        string $startDatetime,
        string $endDatetime,
        bool $allMenus = false,
        ?int $excludeId = null
    ): bool;

    public function getConflictDetails(
        ?int $menuId,
        string $startDatetime,
        string $endDatetime,
        bool $allMenus = false,
        ?int $excludeId = null
    ): array;

    public function getByDate(string $date): Collection;

    public function getBlockedDatesInRange(string $startDate, string $endDate): array;

    public function getBlockedHoursInRange(string $startDate, string $endDate): array;

    public function bulkDelete(array $ids): int;

    public function getStatistics(array $filters = []): array;

    public function isTimeBlocked(?int $menuId, string $datetime): bool;

    public function getExpiringSoon(int $hours = 24): Collection;

    public function getByDuration(string $operator, int $hours): Collection;

    public function getMostBlockedMenus(int $limit = 10): array;

    public function getOverlappingPeriods(): Collection;
}
