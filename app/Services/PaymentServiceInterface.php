<?php
namespace App\Services;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentServiceInterface
{
    public function getAllPayments(): Collection;
    public function getPaginatedPayments(int $perPage = 15): LengthAwarePaginator;
    public function findPayment(int $id): ?Payment;
    public function createPayment(array $data): Payment;
    public function updatePayment(int $id, array $data): ?Payment;
    public function deletePayment(int $id): bool;
    public function getPaymentsByUser(int $userId): Collection;
    public function getPaymentsByReservation(int $reservationId): Collection;
    public function getPaymentsByStatus(string $status): Collection;
    public function getPaymentHistory(int $userId): Collection;
    public function getTotalRevenue(): float;
    public function processPayment(int $paymentId, array $paymentData): bool;
}