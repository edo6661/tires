<?php

namespace App\Services;

use App\Models\BlockedPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;

interface BlockedPeriodServiceInterface
{
    public function getAllBlockedPeriods(): Collection;

    public function getPaginatedBlockedPeriods(int $perPage = 15): LengthAwarePaginator;

    public function getPaginatedBlockedPeriodsWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function getPaginatedBlockedPeriodsWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator;

    public function findBlockedPeriod(int $id): ?BlockedPeriod;

    public function createBlockedPeriod(array $data): BlockedPeriod;

    public function updateBlockedPeriod(int $id, array $data): ?BlockedPeriod;

    public function deleteBlockedPeriod(int $id): bool;

    public function getBlockedPeriodsByMenu(int $menuId): Collection;

    public function getBlockedPeriodsByDateRange(string $startDate, string $endDate): Collection;

    public function getActiveBlockedPeriods(): Collection;

    public function checkScheduleConflict(int $menuId, string $startDatetime, string $endDatetime): bool;

    public function checkScheduleConflictWithExclusion(
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

    public function getBlockedPeriodsByDate(string $date): Collection;

    public function getBlockedDatesInRange(string $startDate, string $endDate): array;

    public function getBlockedHoursInRange(string $startDate, string $endDate): array;

    public function getByDateRange(string $startDate, string $endDate): Collection;

    public function bulkDelete(array $ids): int;

    public function exportBlockedPeriods(array $filters = []);

    public function getStatistics(array $filters = []): array;

    public function getBlockedPeriodStatistics(): array;

    public function isTimeBlocked(?int $menuId, string $datetime): bool;

    public function getAvailableTimeSlots(int $menuId, string $date, array $businessHours = []): array;

    public function createRecurringBlockedPeriods(array $data): array;
}
