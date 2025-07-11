<?php

namespace App\Services;

use App\Models\TireStorage;
use App\Repositories\TireStorageRepositoryInterface;
use App\Services\TireStorageServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

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

    public function findTireStorage(int $id): ?TireStorage
    {
        return $this->tireStorageRepository->findById($id);
    }

    public function createTireStorage(array $data): TireStorage
    {
        // Validasi tanggal
        if (isset($data['storage_start_date']) && isset($data['planned_end_date'])) {
            $startDate = Carbon::parse($data['storage_start_date']);
            $endDate = Carbon::parse($data['planned_end_date']);
            
            if ($endDate->lte($startDate)) {
                throw new \InvalidArgumentException('Tanggal rencana selesai harus setelah tanggal mulai penyimpanan');
            }
        }

        // Set status default jika belum ada
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }

        // Hitung biaya penyimpanan otomatis jika belum ada
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

        // Validasi tanggal jika ada perubahan
        if (isset($data['storage_start_date']) || isset($data['planned_end_date'])) {
            $startDate = Carbon::parse($data['storage_start_date'] ?? $tireStorage->storage_start_date);
            $endDate = Carbon::parse($data['planned_end_date'] ?? $tireStorage->planned_end_date);
            
            if ($endDate->lte($startDate)) {
                throw new \InvalidArgumentException('Tanggal rencana selesai harus setelah tanggal mulai penyimpanan');
            }
        }

        // Hitung ulang biaya penyimpanan jika ada perubahan tanggal
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

        // Hanya bisa hapus jika belum aktif atau sudah selesai
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

        if ($tireStorage->status !== 'active') {
            throw new \Exception('Penyimpanan ban tidak aktif, status saat ini: ' . $tireStorage->status);
        }

        $updated = $this->tireStorageRepository->update($id, [
            'status' => 'ended',
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
            $months = 1; // Minimal 1 bulan
        }

        // Tarif per bulan (bisa disesuaikan atau diambil dari setting)
        $monthlyRate = 50000; // Rp 50.000 per bulan
        
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
            $months = 1; // Minimal 1 bulan
        }

        // Tarif per bulan (bisa disesuaikan atau diambil dari setting)
        $monthlyRate = 50000; // Rp 50.000 per bulan
        
        return $months * $monthlyRate;
    }
}