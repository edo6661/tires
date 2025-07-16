<?php

namespace App\Services;

use App\Models\BlockedPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BlockedPeriodServiceInterface
{
    public function getAllBlockedPeriods(): Collection;
    public function getPaginatedBlockedPeriods(int $perPage = 15): LengthAwarePaginator;
    public function findBlockedPeriod(int $id): ?BlockedPeriod;
    public function createBlockedPeriod(array $data): BlockedPeriod;
    public function updateBlockedPeriod(int $id, array $data): ?BlockedPeriod;
    public function deleteBlockedPeriod(int $id): bool;
    public function getBlockedPeriodsByMenu(int $menuId): Collection;
    public function getBlockedPeriodsByDateRange(string $startDate, string $endDate): Collection;
    public function getActiveBlockedPeriods(): Collection;
    public function checkScheduleConflict(int $menuId, string $startDatetime, string $endDatetime): bool;
    public function getBlockedPeriodsByDate(string $date): Collection;
    public function getBlockedDatesInRange(string $startDate, string $endDate): array;
    public function getBlockedHoursInRange(string $startDate, string $endDate): array;
    public function getByDateRange(string $startDate, string $endDate): Collection;

}