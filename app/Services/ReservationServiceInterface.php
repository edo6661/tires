<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ReservationServiceInterface
{
    public function getAllReservations(): Collection;
    public function getPaginatedReservations(int $perPage = 15): LengthAwarePaginator;
    public function findReservation(int $id): ?Reservation;
    public function createReservation(array $data): Reservation;
    public function updateReservation(int $id, array $data): ?Reservation;
    public function deleteReservation(int $id): bool;
    public function getReservationsByUser(int $userId): Collection;
    public function getReservationsByMenu(int $menuId): Collection;
    public function getReservationsByStatus(string $status): Collection;
    public function getReservationsByDateRange(string $startDate, string $endDate): Collection;
    public function getUpcomingReservations(): Collection;
    public function getCompletedReservations(): Collection;
    public function getCancelledReservations(): Collection;
    public function getTodayReservations(): Collection;
    public function checkAvailability(int $menuId, string $datetime, ?int $excludeReservationId = null): bool;
    public function confirmReservation(int $id): bool;
    public function cancelReservation(int $id): bool;
    public function completeReservation(int $id): bool;
    public function bulkUpdateReservationStatus(array $ids, string $status): bool;
    public function generateReservationNumber(): string;
    public function getReservationsByDateRangeAndMenu(string $startDate, string $endDate, int $menuId,?int $excludeReservationId = null): Collection;
}