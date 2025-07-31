<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Reservation;
use App\Models\TireStorage;
use App\Repositories\CustomerRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CustomerRepository implements CustomerRepositoryInterface
    {
    protected $userModel;
    protected $reservationModel;
    protected $tireStorageModel;

    public function __construct(User $userModel, Reservation $reservationModel, TireStorage $tireStorageModel)
    {
        $this->userModel = $userModel;
        $this->reservationModel = $reservationModel;
        $this->tireStorageModel = $tireStorageModel;
    }

    public function getPaginated(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = DB::table('reservations as r')
            ->select([
                DB::raw('COALESCE(r.user_id, CONCAT("guest_", r.id)) as customer_id'),
                DB::raw('COALESCE(u.full_name, r.full_name) as full_name'),
                DB::raw('COALESCE(u.full_name_kana, r.full_name_kana) as full_name_kana'),
                DB::raw('COALESCE(u.email, r.email) as email'),
                DB::raw('COALESCE(u.phone_number, r.phone_number) as phone_number'),
                DB::raw('CASE WHEN r.user_id IS NOT NULL THEN 1 ELSE 0 END as is_registered'),
                'r.user_id',
                DB::raw('COUNT(r.id) as reservation_count'),
                DB::raw('MAX(r.reservation_datetime) as latest_reservation'),
                DB::raw('COALESCE(SUM(r.amount), 0) as total_amount')
            ])
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->groupBy([
                'r.user_id', 
                'r.full_name', 
                'r.full_name_kana', 
                'r.email', 
                'r.phone_number', 
                'u.full_name', 
                'u.full_name_kana', 
                'u.email', 
                'u.phone_number'
            ]);

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function($q) use ($search) {
                $q->where('u.full_name', 'LIKE', $search)
                    ->orWhere('r.full_name', 'LIKE', $search)
                    ->orWhere('u.email', 'LIKE', $search)
                    ->orWhere('r.email', 'LIKE', $search)
                    ->orWhere('u.phone_number', 'LIKE', $search)
                    ->orWhere('r.phone_number', 'LIKE', $search);
            });
        }

        if (!empty($filters['customer_type'])) {
            switch ($filters['customer_type']) {
                case 'first_time':
                    $query->having('reservation_count', '=', 1);
                    break;
                case 'repeat':
                    $query->having('reservation_count', '>=', 3);
                    break;
                case 'dormant':
                    $query->having('latest_reservation', '<', Carbon::now()->subMonths(3));
                    break;
                case 'monthly_plan':
                    $query->whereNotNull('r.user_id');
                    break;
            }
        }

        return $query->orderBy('latest_reservation', 'desc')->paginate($perPage);
    }

    public function findById(int $id): ?array
    {
        // Cek apakah ini adalah user_id yang terdaftar atau guest reservation
        $customer = DB::table('reservations as r')
            ->select([
                DB::raw('COALESCE(r.user_id, CONCAT("guest_", r.id)) as customer_id'),
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
            ->where(function($query) use ($id) {
                // Jika ID adalah angka dan ada user dengan ID tersebut
                $query->where('r.user_id', $id)
                        // Atau jika ini adalah guest reservation dengan ID tertentu
                        ->orWhere(function($q) use ($id) {
                            // Untuk guest customer, gunakan reservation ID
                            if (strpos($id, 'guest_') === 0) {
                                $reservationId = str_replace('guest_', '', $id);
                                $q->where('r.id', $reservationId)->whereNull('r.user_id');
                            }
                        });
            })
            ->groupBy([
                'r.user_id', 
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
            ])
            ->first();

        return $customer ? (array) $customer : null;
    }

    public function getFirstTimeCustomers(): Collection
    {
        return DB::table('reservations as r')
            ->select([
                DB::raw('COALESCE(r.user_id, CONCAT("guest_", r.id)) as customer_id'),
                DB::raw('COALESCE(u.full_name, r.full_name) as full_name'),
                DB::raw('COALESCE(u.email, r.email) as email'),
                DB::raw('COUNT(r.id) as reservation_count')
            ])
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->groupBy([
                'r.user_id', 
                'r.full_name', 
                'r.email', 
                'u.full_name', 
                'u.email'
            ])
            ->having('reservation_count', '=', 1)
            ->get();
    }

    public function getRepeatCustomers(): Collection
    {
        return DB::table('reservations as r')
            ->select([
                DB::raw('COALESCE(r.user_id, CONCAT("guest_", r.id)) as customer_id'),
                DB::raw('COALESCE(u.full_name, r.full_name) as full_name'),
                DB::raw('COALESCE(u.email, r.email) as email'),
                DB::raw('COUNT(r.id) as reservation_count')
            ])
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->groupBy([
                'r.user_id', 
                'r.full_name', 
                'r.email', 
                'u.full_name', 
                'u.email'
            ])
            ->having('reservation_count', '>=', 3)
            ->get();
    }

    public function getDormantCustomers(): Collection
    {
        return DB::table('reservations as r')
            ->select([
                DB::raw('COALESCE(r.user_id, CONCAT("guest_", r.id)) as customer_id'),
                DB::raw('COALESCE(u.full_name, r.full_name) as full_name'),
                DB::raw('COALESCE(u.email, r.email) as email'),
                DB::raw('MAX(r.reservation_datetime) as latest_reservation')
            ])
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->groupBy([
                'r.user_id', 
                'r.full_name', 
                'r.email', 
                'u.full_name', 
                'u.email'
            ])
            ->having('latest_reservation', '<', Carbon::now()->subMonths(3))
            ->get();
    }

    public function getMonthlyPlanCustomers(): Collection
    {
        return $this->userModel->with(['reservations' => function($query) {
                $query->orderBy('reservation_datetime', 'desc');
            }])
            ->whereHas('reservations')
            ->get()
            ->map(function($user) {
                return (object) [
                    'customer_id' => $user->id,
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'reservation_count' => $user->reservations->count(),
                    'latest_reservation' => $user->reservations->first()?->reservation_datetime,
                    'total_amount' => $user->reservations->sum('amount')
                ];
            });
    }

    public function getCustomerReservationHistory(int $customerId, ?int $userId = null): Collection
    {
        $query = $this->reservationModel->with(['menu']);
        
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            // Untuk guest customer, gunakan ID reservasi
            if (strpos($customerId, 'guest_') === 0) {
                $reservationId = str_replace('guest_', '', $customerId);
                $query->where('id', $reservationId);
            } else {
                $query->where('user_id', $customerId);
            }
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
        $reservationQuery = $this->reservationModel->query();
        
        if ($userId) {
            $reservationQuery->where('user_id', $userId);
        } else {
            // Untuk guest customer
            if (strpos($customerId, 'guest_') === 0) {
                $reservationId = str_replace('guest_', '', $customerId);
                $reservationQuery->where('id', $reservationId);
            } else {
                $reservationQuery->where('user_id', $customerId);
            }
        }
        
        $reservationCount = $reservationQuery->count();
        $totalAmount = $reservationQuery->sum('amount') ?? 0;
        
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
        $searchTerm = '%' . $search . '%';
        
        return DB::table('reservations as r')
            ->select([
                DB::raw('COALESCE(r.user_id, CONCAT("guest_", r.id)) as customer_id'),
                DB::raw('COALESCE(u.full_name, r.full_name) as full_name'),
                DB::raw('COALESCE(u.email, r.email) as email'),
                DB::raw('COALESCE(u.phone_number, r.phone_number) as phone_number')
            ])
            ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
            ->where(function($q) use ($searchTerm) {
                $q->where('u.full_name', 'LIKE', $searchTerm)
                    ->orWhere('r.full_name', 'LIKE', $searchTerm)
                    ->orWhere('u.email', 'LIKE', $searchTerm)
                    ->orWhere('r.email', 'LIKE', $searchTerm)
                    ->orWhere('u.phone_number', 'LIKE', $searchTerm)
                    ->orWhere('r.phone_number', 'LIKE', $searchTerm);
            })
            ->groupBy([
                'r.user_id', 
                'r.full_name', 
                'r.email', 
                'r.phone_number', 
                'u.full_name', 
                'u.email', 
                'u.phone_number'
            ])
            ->get();
    }
}