<?php

namespace App\Services;

use App\Models\Payment;
use App\Repositories\PaymentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentService implements PaymentServiceInterface
{
    protected $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function getAllPayments(): Collection
    {
        return $this->paymentRepository->getAll();
    }

    public function getPaginatedPayments(int $perPage = 15): LengthAwarePaginator
    {
        return $this->paymentRepository->getPaginated($perPage);
    }

    public function findPayment(int $id): ?Payment
    {
        return $this->paymentRepository->findById($id);
    }

    public function createPayment(array $data): Payment
    {
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        return $this->paymentRepository->create($data);
    }

    public function updatePayment(int $id, array $data): ?Payment
    {
        return $this->paymentRepository->update($id, $data);
    }

    public function deletePayment(int $id): bool
    {
        return $this->paymentRepository->delete($id);
    }

    public function getPaymentsByUser(int $userId): Collection
    {
        return $this->paymentRepository->getByUserId($userId);
    }

    public function getPaymentsByReservation(int $reservationId): Collection
    {
        return $this->paymentRepository->getByReservationId($reservationId);
    }

    public function getPaymentsByStatus(string $status): Collection
    {
        return $this->paymentRepository->getByStatus($status);
    }

    public function getPaymentHistory(int $userId): Collection
    {
        return $this->paymentRepository->getPaymentHistory($userId);
    }

    public function getTotalRevenue(): float
    {
        return $this->paymentRepository->getTotalRevenue();
    }

    public function processPayment(int $paymentId, array $paymentData): bool
    {
        $payment = $this->findPayment($paymentId);
        if (!$payment) {
            return false;
        }

        $updateData = [
            'status' => 'completed',
            'paid_at' => now(),
            'payment_details' => $paymentData
        ];

        if (isset($paymentData['transaction_id'])) {
            $updateData['transaction_id'] = $paymentData['transaction_id'];
        }

        return $this->updatePayment($paymentId, $updateData) !== null;
    }
}
