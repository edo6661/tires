<?php

namespace App\Services;

use App\Models\BlockedPeriod;
use App\Repositories\BlockedPeriodRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BlockedPeriodService implements BlockedPeriodServiceInterface
{
    protected $blockedPeriodRepository;

    public function __construct(BlockedPeriodRepositoryInterface $blockedPeriodRepository)
    {
        $this->blockedPeriodRepository = $blockedPeriodRepository;
    }

    public function getAllBlockedPeriods(): Collection
    {
        return $this->blockedPeriodRepository->getAll();
    }

    public function getPaginatedBlockedPeriods(int $perPage = 15): LengthAwarePaginator
    {
        return $this->blockedPeriodRepository->getPaginated($perPage);
    }

    public function findBlockedPeriod(int $id): ?BlockedPeriod
    {
        return $this->blockedPeriodRepository->findById($id);
    }

    public function createBlockedPeriod(array $data): BlockedPeriod
    {
        // Validate that start_datetime is before end_datetime
        if ($data['start_datetime'] >= $data['end_datetime']) {
            throw new \InvalidArgumentException('Start datetime must be before end datetime');
        }

        return $this->blockedPeriodRepository->create($data);
    }

    public function updateBlockedPeriod(int $id, array $data): ?BlockedPeriod
    {
        // Validate that start_datetime is before end_datetime
        if (isset($data['start_datetime']) && isset($data['end_datetime']) && $data['start_datetime'] >= $data['end_datetime']) {
            throw new \InvalidArgumentException('Start datetime must be before end datetime');
        }

        return $this->blockedPeriodRepository->update($id, $data);
    }

    public function deleteBlockedPeriod(int $id): bool
    {
        return $this->blockedPeriodRepository->delete($id);
    }

    public function getBlockedPeriodsByMenu(int $menuId): Collection
    {
        return $this->blockedPeriodRepository->getByMenuId($menuId);
    }

    public function getBlockedPeriodsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->blockedPeriodRepository->getByDateRange($startDate, $endDate);
    }

    public function getActiveBlockedPeriods(): Collection
    {
        return $this->blockedPeriodRepository->getActiveBlocks();
    }

    public function checkScheduleConflict(int $menuId, string $startDatetime, string $endDatetime): bool
    {
        return $this->blockedPeriodRepository->checkConflict($menuId, $startDatetime, $endDatetime);
    }
}
