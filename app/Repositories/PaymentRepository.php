<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentRepository implements PaymentRepositoryInterface
{
    protected $model;

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }

    public function getAll(): Collection
    {
        return $this->model->with(['user', 'reservation'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['user', 'reservation'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Payment
    {
        return $this->model->with(['user', 'reservation'])->find($id);
    }

    public function create(array $data): Payment
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): ?Payment
    {
        $payment = $this->findById($id);
        if ($payment) {
            $payment->update($data);
            return $payment;
        }
        return null;
    }

    public function delete(int $id): bool
    {
        $payment = $this->findById($id);
        if ($payment) {
            return $payment->delete();
        }
        return false;
    }

    public function getByUserId(int $userId): Collection
    {
        return $this->model->with('reservation')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByReservationId(int $reservationId): Collection
    {
        return $this->model->where('reservation_id', $reservationId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getByStatus(string $status): Collection
    {
        return $this->model->with(['user', 'reservation'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getPaymentHistory(int $userId): Collection
    {
        return $this->model->with('reservation')
            ->where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('paid_at', 'desc')
            ->get();
    }

    public function getTotalRevenue(): float
    {
        return $this->model->where('status', 'completed')
            ->sum('amount');
    }
}