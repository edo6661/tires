<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Reservation;
use App\Models\TireStorage;
use App\Repositories\CustomerRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerRepository implements CustomerRepositoryInterface
{
    protected $reservationModel;
    protected $userModel;
    protected $tireStorageModel;

    public function __construct(Reservation $reservationModel, User $userModel, TireStorage $tireStorageModel)
    {
        $this->reservationModel = $reservationModel;
        $this->userModel = $userModel;
        $this->tireStorageModel = $tireStorageModel;
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->buildCustomerQuery();
        $this->applyFilters($query, $filters);
        return $query->paginate($perPage);
    }

    public function getPaginatedWithCursor(array $filters = [], int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        $query = $this->buildCustomerQuery();
        $this->applyFilters($query, $filters);
        return $query->orderBy('latest_reservation', 'desc')
            ->cursorPaginate($perPage, ['*'], 'cursor', $cursor);
    }

    private function buildCustomerQuery()
    {
        // base query dengan group by & aggregate
        $base = DB::table('reservations as r')
            ->select([
                DB::raw("CASE WHEN r.user_id IS NOT NULL
                        THEN CAST(r.user_id AS TEXT)
                        ELSE CONCAT('guest_', CAST(r.id AS TEXT)) END as customer_id"),
                DB::raw('COALESCE(u.full_name, r.full_name) as full_name'),
                DB::raw('COALESCE(u.full_name_kana, r.full_name_kana) as full_name_kana'),
                DB::raw('COALESCE(u.email, r.email) as email'),
                DB::raw('COALESCE(u.phone_number, r.phone_number) as phone_number'),
                DB::raw('CASE WHEN r.user_id IS NOT NULL THEN 1 ELSE 0 END as is_registered'),
                'r.user_id',
                'u.company_name',
                'u.department',
                'u.company_address',
                'u.home_address',
                'u.date_of_birth',
                'u.gender',
                DB::raw('COUNT(r.id) as reservation_count'),
                DB::raw('MAX(r.reservation_datetime) as latest_reservation'),
                DB::raw('COALESCE(SUM(r.amount), 0) as total_amount')
            ])
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->groupBy([
                'r.user_id',
                'r.id',
                'r.full_name',
                'r.full_name_kana',
                'r.email',
                'r.phone_number',
                'u.full_name',
                'u.full_name_kana',
                'u.email',
                'u.phone_number',
                'u.company_name',
                'u.department',
                'u.company_address',
                'u.home_address',
                'u.date_of_birth',
                'u.gender'
            ]);

        // jadikan subquery biar bisa pakai alias seperti latest_reservation di WHERE
        return DB::query()->fromSub($base, 'customer_base');
    }


    private function applyFilters($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'LIKE', $search)
                    ->orWhere('email', 'LIKE', $search)
                    ->orWhere('phone_number', 'LIKE', $search);
            });
        }

        if (!empty($filters['customer_type'])) {
            switch ($filters['customer_type']) {
                case 'first_time':
                    $firstTimeUserIds = $this->getFirstTimeCustomerUserIds();
                    $query->whereIn('user_id', $firstTimeUserIds);
                    break;

                case 'repeat':
                    $repeatUserIds = $this->getRepeatCustomerUserIds();
                    $query->whereIn('user_id', $repeatUserIds);
                    break;

                case 'dormant':
                    $query->where('latest_reservation', '<', Carbon::now()->subMonths(3));
                    break;
            }
        }
    }


    public function findById(int $id): ?array
    {
        $customer = $this->buildCustomerQuery()
            ->where(function ($query) use ($id) {
                $query->where('user_id', $id)
                    ->orWhere('customer_id', 'guest_' . $id);
            })
            ->first();

        return $customer ? (array) $customer : null;
    }

    public function getFirstTimeCustomers(): Collection
    {
        $firstTimeUserIds = $this->getFirstTimeCustomerUserIds();
        return $this->buildCustomerQuery()
            ->whereIn('user_id', $firstTimeUserIds)
            ->get();
    }

    public function getRepeatCustomers(): Collection
    {
        $repeatUserIds = $this->getRepeatCustomerUserIds();
        return $this->buildCustomerQuery()
            ->whereIn('user_id', $repeatUserIds)
            ->get();
    }

    public function getDormantCustomers(): Collection
    {
        $dormantUserIds = $this->getDormantCustomerUserIds();
        return $this->buildCustomerQuery()
            ->whereIn('user_id', $dormantUserIds)
            ->get();
    }

    private function getFirstTimeCustomerUserIds(): Collection
    {
        return $this->reservationModel
            ->selectRaw('user_id')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) = 1')
            ->pluck('user_id');
    }

    private function getRepeatCustomerUserIds(): Collection
    {
        return $this->reservationModel
            ->selectRaw('user_id')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) >= 3')
            ->pluck('user_id');
    }

    private function getDormantCustomerUserIds(): Collection
    {
        return $this->reservationModel
            ->selectRaw('user_id')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->havingRaw('MAX(reservation_datetime) < ?', [Carbon::now()->subMonths(3)])
            ->pluck('user_id');
    }

    public function getFirstTimeCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->getPaginatedWithCursor(['customer_type' => 'first_time'], $perPage, $cursor);
    }

    public function getRepeatCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->getPaginatedWithCursor(['customer_type' => 'repeat'], $perPage, $cursor);
    }

    public function getDormantCustomersWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->getPaginatedWithCursor(['customer_type' => 'dormant'], $perPage, $cursor);
    }

    public function getCustomerReservationHistory(int $customerId, ?int $userId = null): Collection
    {
        $query = $this->reservationModel->with(['menu']);

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('id', $customerId);
        }

        return $query->orderBy('reservation_datetime', 'desc')->get();
    }

    public function getCustomerTireStorage(int $customerId, ?int $userId = null): Collection
    {
        if (!$userId) {
            return collect();
        }

        return $this->tireStorageModel->where('user_id', $userId)
            ->orderBy('storage_start_date', 'desc')
            ->get();
    }

    public function getCustomerStats(int $customerId, ?int $userId = null): array
    {
        $query = $this->reservationModel->query();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('id', $customerId);
        }

        $reservationCount = $query->count();
        $totalAmount = $query->sum('amount') ?? 0;

        $tireStorageCount = 0;
        if ($userId) {
            $tireStorageCount = $this->tireStorageModel->where('user_id', $userId)->count();
        }

        return [
            'reservation_count' => $reservationCount,
            'total_amount' => $totalAmount,
            'tire_storage_count' => $tireStorageCount
        ];
    }

    public function searchCustomers(string $search): Collection
    {
        return $this->buildCustomerQuery()
            ->where(function ($q) use ($search) {
                $searchTerm = '%' . $search . '%';
                $q->where('full_name', 'LIKE', $searchTerm)
                    ->orWhere('email', 'LIKE', $searchTerm)
                    ->orWhere('phone_number', 'LIKE', $searchTerm);
            })
            ->get();
    }

    public function count(): int
    {
        return $this->buildCustomerQuery()->count();
    }

    public function countByType(string $type): int
    {
        switch ($type) {
            case 'first_time':
                return $this->getFirstTimeCustomers()->count();
            case 'repeat':
                return $this->getRepeatCustomers()->count();
            case 'dormant':
                return $this->getDormantCustomers()->count();
            default:
                return 0;
        }
    }

    public function countTodayCustomers(): int
    {
        return $this->reservationModel
            ->whereDate('created_at', today())
            ->distinct('user_id')
            ->count('user_id');
    }

    public function getFiltered(array $filters, int $perPage = null): Collection
    {
        $query = $this->buildCustomerQuery();
        $this->applyFilters($query, $filters);

        $query->orderBy('latest_reservation', 'desc');

        if ($perPage) {
            return $query->take($perPage)->get();
        }

        return $query->get();
    }

    public function search(string $searchQuery, int $perPage = 15): Collection
    {
        return $this->buildCustomerQuery()
            ->where(function ($q) use ($searchQuery) {
                $search = '%' . $searchQuery . '%';
                $q->where('full_name', 'LIKE', $search)
                    ->orWhere('email', 'LIKE', $search)
                    ->orWhere('phone_number', 'LIKE', $search);
            })
            ->orderBy('latest_reservation', 'desc')
            ->take($perPage)
            ->get();
    }
}
