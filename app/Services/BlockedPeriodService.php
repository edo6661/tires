<?php
namespace App\Services;
use App\Models\BlockedPeriod;
use App\Repositories\BlockedPeriodRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Exports\BlockedPeriodsExport;
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
    /**
     * Get paginated blocked periods with filters
     */
    public function getPaginatedBlockedPeriodsWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->blockedPeriodRepository->getPaginatedWithFilters($filters, $perPage);
    }
    public function findBlockedPeriod(int $id): ?BlockedPeriod
    {
        return $this->blockedPeriodRepository->findById($id);
    }
    public function createBlockedPeriod(array $data): BlockedPeriod
    {
        if ($data['start_datetime'] >= $data['end_datetime']) {
            throw new \InvalidArgumentException('Waktu mulai harus sebelum waktu selesai');
        }
        if ($this->checkScheduleConflictWithExclusion(
            $data['menu_id'] ?? null, 
            $data['start_datetime'], 
            $data['end_datetime'], 
            $data['all_menus'] ?? false
        )) {
            throw new \InvalidArgumentException('Terjadi konflik waktu dengan periode blokir yang sudah ada');
        }
        if ($data['all_menus'] ?? false) {
            $data['menu_id'] = null;
        }
        return $this->blockedPeriodRepository->create($data);
    }
    public function updateBlockedPeriod(int $id, array $data): ?BlockedPeriod
    {
        if (isset($data['start_datetime']) && isset($data['end_datetime']) && 
            $data['start_datetime'] >= $data['end_datetime']) {
            throw new \InvalidArgumentException('Waktu mulai harus sebelum waktu selesai');
        }
        if ($this->checkScheduleConflictWithExclusion(
            $data['menu_id'] ?? null, 
            $data['start_datetime'], 
            $data['end_datetime'], 
            $data['all_menus'] ?? false,
            $id
        )) {
            throw new \InvalidArgumentException('Terjadi konflik waktu dengan periode blokir yang sudah ada');
        }
        if ($data['all_menus'] ?? false) {
            $data['menu_id'] = null;
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
    /**
     * Check schedule conflict (original method for backward compatibility)
     */
    public function checkScheduleConflict(int $menuId, string $startDatetime, string $endDatetime): bool
    {
        return $this->blockedPeriodRepository->checkConflict($menuId, $startDatetime, $endDatetime);
    }
    /**
     * Enhanced conflict checking with exclusion support
     */
    public function checkScheduleConflictWithExclusion(
        ?int $menuId, 
        string $startDatetime, 
        string $endDatetime, 
        bool $allMenus = false, 
        ?int $excludeId = null
    ): bool {
        return $this->blockedPeriodRepository->checkConflictWithExclusion(
            $menuId, 
            $startDatetime, 
            $endDatetime, 
            $allMenus, 
            $excludeId
        );
    }
    /**
     * Get detailed information about conflicts
     */
    public function getConflictDetails(
        ?int $menuId, 
        string $startDatetime, 
        string $endDatetime, 
        bool $allMenus = false, 
        ?int $excludeId = null
    ): array {
        return $this->blockedPeriodRepository->getConflictDetails(
            $menuId, 
            $startDatetime, 
            $endDatetime, 
            $allMenus, 
            $excludeId
        );
    }
    public function getBlockedPeriodsByDate(string $date): Collection
    {   
        return $this->blockedPeriodRepository->getByDate($date);
    }
    public function getBlockedDatesInRange(string $startDate, string $endDate): array
    {
        return $this->blockedPeriodRepository->getBlockedDatesInRange($startDate, $endDate);
    }
    public function getBlockedHoursInRange(string $startDate, string $endDate): array
    {
        return $this->blockedPeriodRepository->getBlockedHoursInRange($startDate, $endDate);
    }
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->blockedPeriodRepository->getByDateRange($startDate, $endDate);
    }
    /**
     * Bulk delete blocked periods
     */
    public function bulkDelete(array $ids): int
    {
        return $this->blockedPeriodRepository->bulkDelete($ids);
    }
    /**
     * Export blocked periods to Excel
     */
    public function exportBlockedPeriods(array $filters = [])
    {
        $blockedPeriods = $this->blockedPeriodRepository->getAllWithFilters($filters);
        return response()->json([
            'success' => true,
            'data' => ($blockedPeriods)
        ]);
    }
    /**
     * Get statistics about blocked periods
     */
    public function getStatistics(array $filters = []): array
    {
        return $this->blockedPeriodRepository->getStatistics($filters);
    }
    /**
     * Check if a specific date/time is blocked for a menu
     */
    public function isTimeBlocked(?int $menuId, string $datetime): bool
    {
        return $this->blockedPeriodRepository->isTimeBlocked($menuId, $datetime);
    }
    /**
     * Get available time slots for a menu on a specific date
     */
    public function getAvailableTimeSlots(int $menuId, string $date, array $businessHours = []): array
    {
        $blockedPeriods = $this->getBlockedPeriodsByDate($date);
        if (empty($businessHours)) {
            $businessHours = [
                'start' => '08:00',
                'end' => '22:00',
                'interval' => 60 
            ];
        }
        $availableSlots = [];
        $currentTime = Carbon::parse($date . ' ' . $businessHours['start']);
        $endTime = Carbon::parse($date . ' ' . $businessHours['end']);
        while ($currentTime->lt($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($businessHours['interval']);
            $isBlocked = false;
            foreach ($blockedPeriods as $period) {
                if ($period->all_menus || $period->menu_id == $menuId) {
                    if ($currentTime->lt($period->end_datetime) && $slotEnd->gt($period->start_datetime)) {
                        $isBlocked = true;
                        break;
                    }
                }
            }
            if (!$isBlocked) {
                $availableSlots[] = [
                    'start' => $currentTime->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'datetime' => $currentTime->format('Y-m-d H:i:s')
                ];
            }
            $currentTime->addMinutes($businessHours['interval']);
        }
        return $availableSlots;
    }
    /**
     * Create recurring blocked periods
     */
    public function createRecurringBlockedPeriods(array $data): array
    {
        $created = [];
        $errors = [];
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $recurringType = $data['recurring_type']; 
        $recurringInterval = $data['recurring_interval'] ?? 1;
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            try {
                $periodData = [
                    'menu_id' => $data['menu_id'] ?? null,
                    'start_datetime' => $current->format('Y-m-d') . ' ' . $data['start_time'],
                    'end_datetime' => $current->format('Y-m-d') . ' ' . $data['end_time'],
                    'reason' => $data['reason'],
                    'all_menus' => $data['all_menus'] ?? false
                ];
                $created[] = $this->createBlockedPeriod($periodData);
            } catch (\Exception $e) {
                $errors[] = [
                    'date' => $current->format('Y-m-d'),
                    'error' => $e->getMessage()
                ];
            }
            switch ($recurringType) {
                case 'daily':
                    $current->addDays($recurringInterval);
                    break;
                case 'weekly':
                    $current->addWeeks($recurringInterval);
                    break;
                case 'monthly':
                    $current->addMonths($recurringInterval);
                    break;
            }
        }
        return [
            'created' => $created,
            'errors' => $errors,
            'total_created' => count($created),
            'total_errors' => count($errors)
        ];
    }
}