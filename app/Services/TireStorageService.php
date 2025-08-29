<?php

namespace App\Services;

use App\Enums\TireStorageStatus;
use App\Models\TireStorage;
use App\Repositories\TireStorageRepositoryInterface;
use App\Services\TireStorageServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use Illuminate\Pagination\CursorPaginator;

class TireStorageService implements TireStorageServiceInterface
{
    protected $tireStorageRepository;

    public function __construct(TireStorageRepositoryInterface $tireStorageRepository)
    {
        $this->tireStorageRepository = $tireStorageRepository;
    }

    public function getAllTireStorages(): Collection
    {
        return $this->tireStorageRepository->getAll();
    }

    public function getPaginatedTireStorages(int $perPage = 15): LengthAwarePaginator
    {
        return $this->tireStorageRepository->getPaginated($perPage);
    }

    public function getPaginatedTireStoragesWithCursor(int $perPage = 15, ?string $cursor = null): CursorPaginator
    {
        return $this->tireStorageRepository->getPaginatedWithCursor($perPage, $cursor);
    }

    public function findTireStorage(int $id): ?TireStorage
    {
        return $this->tireStorageRepository->findById($id);
    }

    public function createTireStorage(array $data): TireStorage
    {

        if (isset($data['storage_start_date']) && isset($data['planned_end_date'])) {
            $startDate = Carbon::parse($data['storage_start_date']);
            $endDate = Carbon::parse($data['planned_end_date']);

            if ($endDate->lte($startDate)) {
                throw new \InvalidArgumentException('Tanggal rencana selesai harus setelah tanggal mulai penyimpanan');
            }
        }


        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }


        if (!isset($data['storage_fee'])) {
            $data['storage_fee'] = $this->calculateStorageFeeForNewStorage($data);
        }

        return $this->tireStorageRepository->create($data);
    }

    public function updateTireStorage(int $id, array $data): ?TireStorage
    {
        $tireStorage = $this->findTireStorage($id);
        if (!$tireStorage) {
            return null;
        }


        if (isset($data['storage_start_date']) || isset($data['planned_end_date'])) {
            $startDate = Carbon::parse($data['storage_start_date'] ?? $tireStorage->storage_start_date);
            $endDate = Carbon::parse($data['planned_end_date'] ?? $tireStorage->planned_end_date);

            if ($endDate->lte($startDate)) {
                throw new \InvalidArgumentException('Tanggal rencana selesai harus setelah tanggal mulai penyimpanan');
            }
        }


        if (isset($data['storage_start_date']) || isset($data['planned_end_date'])) {
            $updatedData = array_merge($tireStorage->toArray(), $data);
            $data['storage_fee'] = $this->calculateStorageFeeForNewStorage($updatedData);
        }

        return $this->tireStorageRepository->update($id, $data);
    }

    public function deleteTireStorage(int $id): bool
    {
        $tireStorage = $this->findTireStorage($id);
        if (!$tireStorage) {
            return false;
        }


        if ($tireStorage->status === 'active') {
            throw new \Exception('Tidak bisa menghapus penyimpanan ban yang masih aktif');
        }

        return $this->tireStorageRepository->delete($id);
    }

    public function getTireStoragesByUser(int $userId): Collection
    {
        return $this->tireStorageRepository->getByUserId($userId);
    }

    public function getActiveTireStorages(): Collection
    {
        return $this->tireStorageRepository->getActiveStorages();
    }

    public function getEndedTireStorages(): Collection
    {
        return $this->tireStorageRepository->getEndedStorages();
    }

    public function getTireStoragesByStatus(string $status): Collection
    {
        $allowedStatuses = ['active', 'ended'];
        if (!in_array($status, $allowedStatuses)) {
            throw new \InvalidArgumentException('Status tidak valid: ' . $status);
        }

        return $this->tireStorageRepository->getByStatus($status);
    }

    public function getActiveTireStoragesByUser(int $userId): Collection
    {
        return $this->tireStorageRepository->getActiveByUserId($userId);
    }

    public function getEndedTireStoragesByUser(int $userId): Collection
    {
        return $this->tireStorageRepository->getEndedByUserId($userId);
    }

    public function endTireStorage(int $id): bool
    {
        $tireStorage = $this->findTireStorage($id);
        if (!$tireStorage) {
            return false;
        }

        $updated = $this->tireStorageRepository->update($id, [
            'status' => TireStorageStatus::ENDED,
            'planned_end_date' => Carbon::now()->toDateString()
        ]);

        return $updated !== null;
    }

    public function calculateStorageFee(int $id): float
    {
        $tireStorage = $this->findTireStorage($id);
        if (!$tireStorage) {
            throw new \Exception('Penyimpanan ban tidak ditemukan');
        }

        return $this->calculateStorageFeeForStorage($tireStorage);
    }

    /**
     * Hitung biaya penyimpanan untuk data baru
     */
    private function calculateStorageFeeForNewStorage(array $data): float
    {
        if (!isset($data['storage_start_date']) || !isset($data['planned_end_date'])) {
            return 0.0;
        }

        $startDate = Carbon::parse($data['storage_start_date']);
        $endDate = Carbon::parse($data['planned_end_date']);

        $months = $startDate->diffInMonths($endDate);
        if ($months < 1) {
            $months = 1;
        }


        $monthlyRate = 50000;

        return $months * $monthlyRate;
    }

    /**
     * Hitung biaya penyimpanan untuk model yang sudah ada
     */
    private function calculateStorageFeeForStorage(TireStorage $tireStorage): float
    {
        $startDate = Carbon::parse($tireStorage->storage_start_date);
        $endDate = $tireStorage->status === 'ended'
            ? Carbon::parse($tireStorage->planned_end_date)
            : Carbon::now();

        $months = $startDate->diffInMonths($endDate);
        if ($months < 1) {
            $months = 1;
        }


        $monthlyRate = 50000;

        return $months * $monthlyRate;
    }
    public function getPaginatedTireStoragesWithFilters(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->tireStorageRepository->getPaginatedWithFilters($perPage, $filters);
    }

    // Customer-specific methods
    // public function getTireStorageByUser(int $userId): Collection
    // {
    //     return $this->tireStorageRepository->getByUserId($userId);
    // }

    // public function getCustomerTireStorageWithCursor(int $userId, int $perPage = 15, ?string $cursor = null): CursorPaginator
    // {
    //     return $this->tireStorageRepository->getByUserIdWithCursor($userId, $perPage, $cursor);
    // }

    // public function getActiveTireStorageCountByUser(int $userId): int
    // {
    //     return $this->tireStorageRepository->getActiveCountByUserId($userId);
    // }

    // public function getRecentTireStorageByUser(int $userId, int $limit = 5): Collection
    // {
    //     return $this->tireStorageRepository->getRecentByUserId($userId, $limit);
    // }
}
