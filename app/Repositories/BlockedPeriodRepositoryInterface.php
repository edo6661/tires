<?php
namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\BlockedPeriod;

interface BlockedPeriodRepositoryInterface
{
    public function getAll(): Collection;
    public function getPaginated(int $perPage = 15): LengthAwarePaginator;
    public function findById(int $id): ?BlockedPeriod;
    public function create(array $data): BlockedPeriod;
    public function update(int $id, array $data): ?BlockedPeriod;
    public function delete(int $id): bool;
    public function getByMenuId(int $menuId): Collection;
    public function getByDateRange(string $startDate, string $endDate): Collection;
    public function getActiveBlocks(): Collection;
    public function checkConflict(int $menuId, string $startDatetime, string $endDatetime): bool;
    public function getByDate(string $date): Collection;
    public function getBlockedDatesInRange(string $startDate, string $endDate): array;
    public function getBlockedHoursInRange(string $startDate, string $endDate): array;
    
}
