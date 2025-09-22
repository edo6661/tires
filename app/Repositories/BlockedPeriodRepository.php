<?php
namespace App\Repositories;
use App\Models\BlockedPeriod;
use App\Repositories\BlockedPeriodRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
    public function getPaginatedWithFilters(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('menu');
        $query = $this->applyFilters($query, $filters);
        return $query->orderBy('start_datetime', 'desc')->paginate($perPage);
    }

    public function getPaginatedWithCursor(int $perPage = 15, ?string $cursor = null, array $filters = []): CursorPaginator
    {
        $query = $this->model->with('menu');
        $query = $this->applyFilters($query, $filters);
        return $query->orderBy('start_datetime', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }
    public function getAllWithFilters(array $filters): Collection
    {
        $query = $this->model->with('menu');
        $query = $this->applyFilters($query, $filters);
        return $query->orderBy('start_datetime', 'desc')->get();
    }
    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['menu_id'])) {
            $query->where('menu_id', $filters['menu_id']);
        }
        // Add handling for all_menus filter
        if (isset($filters['all_menus'])) {
            $query->where('all_menus', $filters['all_menus']);
        }
        if (!empty($filters['start_date'])) {
            $query->whereDate('start_datetime', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('end_datetime', '<=', $filters['end_date']);
        }
        if (!empty($filters['status'])) {
            $now = Carbon::now();
            switch ($filters['status']) {
                case 'active':
                    $query->where('start_datetime', '<=', $now)
                          ->where('end_datetime', '>=', $now);
                    break;
                case 'upcoming':
                    $query->where('start_datetime', '>', $now);
                    break;
                case 'expired':
                    $query->where('end_datetime', '<', $now);
                    break;
            }
        }
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('menu', function ($menuQuery) use ($search) {
                      // Fixed: Search in menu translations instead of directly on menu
                      $menuQuery->whereHas('translations', function ($translationQuery) use ($search) {
                          $translationQuery->where('name', 'like', "%{$search}%");
                      });
                  });
            });
        }
        return $query;
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
            return $blockedPeriod->fresh();
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
        return $this->model->with(['menu'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_datetime', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->orWhereBetween('end_datetime', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->orWhere(function ($subQuery) use ($startDate, $endDate) {
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
    public function checkConflictWithExclusion(
        ?int $menuId,
        string $startDatetime,
        string $endDatetime,
        bool $allMenus = false,
        ?int $excludeId = null
    ): bool {
        $query = $this->model->newQuery();
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $query->where(function ($q) use ($menuId, $allMenus) {
            if ($allMenus) {
                $q->where('all_menus', true)
                  ->orWhereNotNull('menu_id');
            } else {
                $q->where('all_menus', true);
                if ($menuId) {
                    $q->orWhere('menu_id', $menuId);
                }
            }
        });
        $query->where(function ($q) use ($startDatetime, $endDatetime) {
            $q->where(function ($timeQuery) use ($startDatetime, $endDatetime) {
                $timeQuery->whereBetween('start_datetime', [$startDatetime, $endDatetime])
                    ->orWhereBetween('end_datetime', [$startDatetime, $endDatetime])
                    ->orWhere(function ($subQuery) use ($startDatetime, $endDatetime) {
                        $subQuery->where('start_datetime', '<=', $startDatetime)
                                ->where('end_datetime', '>=', $endDatetime);
                    })
                    ->orWhere(function ($subQuery) use ($startDatetime, $endDatetime) {
                        $subQuery->where('start_datetime', '>=', $startDatetime)
                                ->where('end_datetime', '<=', $endDatetime);
                    });
            });
        });
        return $query->exists();
    }
    public function getConflictDetails(
        ?int $menuId,
        string $startDatetime,
        string $endDatetime,
        bool $allMenus = false,
        ?int $excludeId = null
    ): array {
        $query = $this->model->with('menu');
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        $query->where(function ($q) use ($menuId, $allMenus) {
            if ($allMenus) {
                $q->where('all_menus', true)
                  ->orWhereNotNull('menu_id');
            } else {
                $q->where('all_menus', true);
                if ($menuId) {
                    $q->orWhere('menu_id', $menuId);
                }
            }
        });
        $query->where(function ($q) use ($startDatetime, $endDatetime) {
            $q->where(function ($timeQuery) use ($startDatetime, $endDatetime) {
                $timeQuery->whereBetween('start_datetime', [$startDatetime, $endDatetime])
                    ->orWhereBetween('end_datetime', [$startDatetime, $endDatetime])
                    ->orWhere(function ($subQuery) use ($startDatetime, $endDatetime) {
                        $subQuery->where('start_datetime', '<=', $startDatetime)
                                ->where('end_datetime', '>=', $endDatetime);
                    })
                    ->orWhere(function ($subQuery) use ($startDatetime, $endDatetime) {
                        $subQuery->where('start_datetime', '>=', $startDatetime)
                                ->where('end_datetime', '<=', $endDatetime);
                    });
            });
        });
        $conflicts = $query->get();
        return $conflicts->map(function ($period) {
            return [
                'id' => $period->id,
                'menu_name' => $period->all_menus ? 'All Menu' : ($period->menu ? $period->menu->name : 'Menu not found'),
                'start_datetime' => $period->start_datetime->format('d/m/Y H:i'),
                'end_datetime' => $period->end_datetime->format('d/m/Y H:i'),
                'reason' => $period->reason,
                'all_menus' => $period->all_menus
            ];
        })->toArray();
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
    public function bulkDelete(array $ids): int
    {
        return $this->model->whereIn('id', $ids)->delete();
    }
    public function getStatistics(array $filters = []): array
    {
        $query = $this->model->newQuery();
        $query = $this->applyFilters($query, $filters);
        $now = Carbon::now();
        return [
            'total' => $query->count(),
            'active' => (clone $query)->where('start_datetime', '<=', $now)
                                    ->where('end_datetime', '>=', $now)
                                    ->count(),
            'upcoming' => (clone $query)->where('start_datetime', '>', $now)->count(),
            'expired' => (clone $query)->where('end_datetime', '<', $now)->count(),
            'all_menus' => (clone $query)->where('all_menus', true)->count(),
            'specific_menus' => (clone $query)->where('all_menus', false)->count(),
            'total_duration_hours' => (clone $query)->get()
                                                   ->sum(function ($period) {
                                                       return $period->getDurationInHours();
                                                   }),
        ];
    }
    public function isTimeBlocked(?int $menuId, string $datetime): bool
    {
        return $this->model->where(function ($query) use ($menuId) {
                $query->where('all_menus', true);
                if ($menuId) {
                    $query->orWhere('menu_id', $menuId);
                }
            })
            ->where('start_datetime', '<=', $datetime)
            ->where('end_datetime', '>=', $datetime)
            ->exists();
    }
    public function getExpiringSoon(int $hours = 24): Collection
    {
        $now = Carbon::now();
        $expireTime = $now->copy()->addHours($hours);
        return $this->model->with('menu')
            ->where('end_datetime', '>', $now)
            ->where('end_datetime', '<=', $expireTime)
            ->orderBy('end_datetime')
            ->get();
    }
    public function getByDuration(string $operator, int $hours): Collection
    {
        return $this->model->with('menu')
            ->whereRaw("TIMESTAMPDIFF(HOUR, start_datetime, end_datetime) {$operator} ?", [$hours])
            ->orderBy('start_datetime', 'desc')
            ->get();
    }
    public function getMostBlockedMenus(int $limit = 10): array
    {
        return $this->model->with('menu')
            ->select('menu_id', DB::raw('COUNT(*) as block_count'))
            ->whereNotNull('menu_id')
            ->groupBy('menu_id')
            ->orderBy('block_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'menu_id' => $item->menu_id,
                    'menu_name' => $item->menu ? $item->menu->name : 'Unknown',
                    'block_count' => $item->block_count
                ];
            })
            ->toArray();
    }
    public function getOverlappingPeriods(): Collection
    {
        return $this->model->with('menu')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('blocked_periods as bp2')
                    ->whereRaw('bp2.id != blocked_periods.id')
                    ->where(function ($q) {
                        $q->where(function ($timeQuery) {
                            $timeQuery->whereBetween('bp2.start_datetime', [DB::raw('blocked_periods.start_datetime'), DB::raw('blocked_periods.end_datetime')])
                                ->orWhereBetween('bp2.end_datetime', [DB::raw('blocked_periods.start_datetime'), DB::raw('blocked_periods.end_datetime')])
                                ->orWhere(function ($subQuery) {
                                    $subQuery->where('bp2.start_datetime', '<=', DB::raw('blocked_periods.start_datetime'))
                                            ->where('bp2.end_datetime', '>=', DB::raw('blocked_periods.end_datetime'));
                                });
                        });
                    });
            })
            ->orderBy('start_datetime')
            ->get();
    }
}
