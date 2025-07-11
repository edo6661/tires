<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?User
    {
        $user = $this->findById($id);
        if ($user) {
            $user->update($data);
            return $user;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $user = $this->findById($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function getCustomers(): Collection
    {
        return $this->model->where('role', 'customer')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getAdmins(): Collection
    {
        return $this->model->where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getWithTireStorage(): Collection
    {
        return $this->model->whereHas('tireStorage')
            ->with('tireStorage')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getFirstTimeCustomers(): Collection
    {
        return $this->model->whereHas('reservations', function ($query) {
            $query->where('status', 'completed');
        }, '=', 1)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getRepeatCustomers(): Collection
    {
        return $this->model->whereHas('reservations', function ($query) {
            $query->where('status', 'completed');
        }, '>', 1)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getDormantCustomers(): Collection
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        
        return $this->model->whereHas('reservations', function ($query) use ($sixMonthsAgo) {
            $query->where('reservation_datetime', '<', $sixMonthsAgo)
                ->where('status', 'completed');
        })
            ->whereDoesntHave('reservations', function ($query) use ($sixMonthsAgo) {
                $query->where('reservation_datetime', '>=', $sixMonthsAgo);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function search(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('full_name', 'like', "%{$query}%")
                ->orWhere('full_name_kana', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone_number', 'like', "%{$query}%");
        })
            ->orderBy('created_at', 'desc')
            ->get();
    }
    public function getByRole(string $role): Collection
    {
        $allowedRoles = ['customer', 'admin'];
        if (!in_array($role, $allowedRoles)) {
            throw new \InvalidArgumentException('Role tidak valid');
        }

        return $this->model->where('role', $role)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}