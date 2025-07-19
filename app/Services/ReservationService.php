<?php

namespace App\Services;

use App\Models\Reservation;
use App\Repositories\ReservationRepositoryInterface;
use App\Services\ReservationServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ReservationService implements ReservationServiceInterface
{
    protected $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function getAllReservations(): Collection
    {
        return $this->reservationRepository->getAll();
    }

    public function getPaginatedReservations(int $perPage = 15): LengthAwarePaginator
    {
        return $this->reservationRepository->getPaginated($perPage);
    }

    public function findReservation(int $id): ?Reservation
    {
        return $this->reservationRepository->findById($id);
    }

    public function createReservation(array $data): Reservation
    {
        if (!$this->checkAvailability($data['menu_id'], $data['reservation_datetime'])) {
            throw new \Exception('Waktu reservasi tidak tersedia');
        }

        if (!isset($data['reservation_number'])) {
            $data['reservation_number'] = $this->generateReservationNumber();
        }

        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        return $this->reservationRepository->create($data);
    }

    public function updateReservation(int $id, array $data): ?Reservation
    {
        $reservation = $this->findReservation($id);
        if (!$reservation) {
            return null;
        }

        if (isset($data['reservation_datetime']) || isset($data['menu_id'])) {
            $menuId = $data['menu_id'] ?? $reservation->menu_id;
            $datetime = $data['reservation_datetime'] ?? $reservation->reservation_datetime;
            
            if (!$this->checkAvailability($menuId, $datetime, $id)) {
                throw new \Exception('Waktu reservasi tidak tersedia');
            }
        }

        return $this->reservationRepository->update($id, $data);
    }

    public function deleteReservation(int $id): bool
    {
        return $this->reservationRepository->delete($id);
    }

    public function getReservationsByUser(int $userId): Collection
    {
        return $this->reservationRepository->getByUserId($userId);
    }

    public function getReservationsByMenu(int $menuId): Collection
    {
        return $this->reservationRepository->getByMenuId($menuId);
    }

    public function getReservationsByStatus(string $status): Collection
    {
        return $this->reservationRepository->getByStatus($status);
    }

    public function getReservationsByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->reservationRepository->getByDateRange($startDate, $endDate);
    }

    public function getUpcomingReservations(): Collection
    {
        return $this->reservationRepository->getUpcoming();
    }

    public function getCompletedReservations(): Collection
    {
        return $this->reservationRepository->getCompleted();
    }

    public function getCancelledReservations(): Collection
    {
        return $this->reservationRepository->getCancelled();
    }

    public function getTodayReservations(): Collection
    {
        return $this->reservationRepository->getTodayReservations();
    }

    public function checkAvailability(int $menuId, string $datetime, ?int $excludeReservationId = null): bool
    {
        return $this->reservationRepository->checkAvailability($menuId, $datetime, $excludeReservationId);
    }

    public function confirmReservation(int $id): bool
    {
        $reservation = $this->findReservation($id);
        if (!$reservation) {
            return false;
        }

        if ($reservation->status !== 'pending') {
            throw new \Exception('Reservasi tidak bisa dikonfirmasi karena status saat ini: ' . $reservation->status);
        }

        $updated = $this->reservationRepository->update($id, ['status' => 'confirmed']);
        return $updated !== null;
    }

    public function cancelReservation(int $id): bool
    {
        $reservation = $this->findReservation($id);
        if (!$reservation) {
            return false;
        }

        if (in_array($reservation->status, ['completed', 'cancelled'])) {
            throw new \Exception('Reservasi tidak bisa dibatalkan karena status saat ini: ' . $reservation->status);
        }

        $updated = $this->reservationRepository->update($id, ['status' => 'cancelled']);
        return $updated !== null;
    }

    public function completeReservation(int $id): bool
    {
        $reservation = $this->findReservation($id);
        if (!$reservation) {
            return false;
        }

        if ($reservation->status !== 'confirmed') {
            throw new \Exception('Reservasi tidak bisa diselesaikan karena status saat ini: ' . $reservation->status);
        }

        $updated = $this->reservationRepository->update($id, ['status' => 'completed']);
        return $updated !== null;
    }

    public function bulkUpdateReservationStatus(array $ids, string $status): bool
    {
        $allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException('Status tidak valid: ' . $status);
        }

        return $this->reservationRepository->bulkUpdateStatus($ids, $status);
    }

    public function generateReservationNumber(): string
    {
        $prefix = 'RES';
        $date = Carbon::now()->format('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $date . $random;
    }
    public function getReservationsByDateRangeAndMenu(string $startDate, string $endDate, int $menuId): Collection
    {
        return $this->reservationRepository->getByDateRangeAndMenu($startDate, $endDate, $menuId);
    }
}