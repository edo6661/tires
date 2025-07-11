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
        return $this->model->with('menu')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_datetime', [$startDate, $endDate])
                    ->orWhereBetween('end_datetime', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_datetime', '<=', $startDate)
                            ->where('end_datetime', '>=', $endDate);
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
}