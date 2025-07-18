<?php


namespace App\Repositories;

use App\Models\BlockedPeriod;
use App\Repositories\BlockedPeriodRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class BlockedPeriodRepository implements BlockedPeriodRepositoryInterface
{
    protected $model;

    public function __construct(BlockedPeriod $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->with('menu')->orderBy('start_datetime', 'desc')->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with('menu')
            ->orderBy('start_datetime', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?BlockedPeriod
    {
        return $this->model->with('menu')->find($id);
    }

    public function create(array $data): BlockedPeriod
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?BlockedPeriod
    {
        $blockedPeriod = $this->findById($id);
        if ($blockedPeriod) {
            $blockedPeriod->update($data);
            return $blockedPeriod;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $blockedPeriod = $this->findById($id);
        if ($blockedPeriod) {
            return $blockedPeriod->delete();
        }
        return false;
    }

    public function getByMenuId(int $menuId): Collection
    {
        return $this->model->where('menu_id', $menuId)
            ->orWhere('all_menus', true)
            ->orderBy('start_datetime', 'desc')
            ->get();
    }

    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->with(['menu']) // Assuming you have menu relationship
            ->where(function ($query) use ($startDate, $endDate) {
                // Get periods that overlap with the date range
                $query->whereBetween('start_datetime', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->orWhereBetween('end_datetime', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                    // Include periods that completely encompass the date range
                    $subQuery->where('start_datetime', '<=', Carbon::parse($startDate)->startOfDay())
                            ->where('end_datetime', '>=', Carbon::parse($endDate)->endOfDay());
                });
            })
            ->orderBy('start_datetime')
            ->get();
    }

    public function getActiveBlocks(): Collection
    {
        return $this->model->with('menu')
            ->where('end_datetime', '>=', Carbon::now())
            ->orderBy('start_datetime')
            ->get();
    }

    public function checkConflict(int $menuId, string $startDatetime, string $endDatetime): bool
    {
        return $this->model->where(function ($query) use ($menuId) {
                $query->where('menu_id', $menuId)
                    ->orWhere('all_menus', true);
            })
            ->where(function ($query) use ($startDatetime, $endDatetime) {
                $query->whereBetween('start_datetime', [$startDatetime, $endDatetime])
                    ->orWhereBetween('end_datetime', [$startDatetime, $endDatetime])
                    ->orWhere(function ($q) use ($startDatetime, $endDatetime) {
                        $q->where('start_datetime', '<=', $startDatetime)
                            ->where('end_datetime', '>=', $endDatetime);
                    });
            })
            ->exists();
    }
    public function getByDate(string $date): Collection
    {
        return $this->model->with('menu')
            ->where(function ($query) use ($date) {
                $query->whereDate('start_datetime', '<=', $date)
                    ->whereDate('end_datetime', '>=', $date);
            })
            ->get();
    }
    public function getBlockedDatesInRange(string $startDate, string $endDate): array
    {
        $blockedPeriods = $this->getByDateRange($startDate, $endDate);
        $blockedDates = [];
        
        foreach ($blockedPeriods as $period) {
            $affectedDates = $period->getAffectedDates();
            foreach ($affectedDates as $date) {
                if (!isset($blockedDates[$date])) {
                    $blockedDates[$date] = [];
                }
                $blockedDates[$date][] = $period;
            }
        }
        
        return $blockedDates;
    }

    public function getBlockedHoursInRange(string $startDate, string $endDate): array
    {
        $blockedPeriods = $this->getByDateRange($startDate, $endDate);
        $blockedHours = [];
        
        foreach ($blockedPeriods as $period) {
            $hours = $period->getBlockedHours();
            foreach ($hours as $hour) {
                if (!isset($blockedHours[$hour['date']])) {
                    $blockedHours[$hour['date']] = [];
                }
                $blockedHours[$hour['date']][] = $hour['hour'];
            }
        }
        
        return $blockedHours;
    }

}