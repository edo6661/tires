<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\TireStorageRequest;
use App\Http\Resources\TireStorageResource;
use App\Services\TireStorageServiceInterface;
use App\Http\Traits\ApiResponseTrait;

class TireStorageController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TireStorageServiceInterface $tireStorageService,
    ) {

    }

    /**
     * List semua penyimpanan ban dengan filter & pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);

            if ($request->boolean('paginate', true)) {
                $tireStorages = $this->tireStorageService->getPaginatedTireStoragesWithCursor($perPage);
                $collection = TireStorageResource::collection($tireStorages);

                $cursor = $this->generateCursor($tireStorages);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursor,
                    'Tire storages retrieved successfully'
                );
            }

            $tireStorages = $this->tireStorageService->getActiveTireStorages();
            $collection = TireStorageResource::collection($tireStorages);

            return $this->successResponse(
                $collection->resolve(),
                'Tire storages retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve tire storages',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'server_error',
                        'value' => $e->getMessage(),
                        'message' => 'An unexpected error occurred'
                    ]
                ]
            );
        }
    }

    /**
     * Buat penyimpanan ban
     */
    public function store(TireStorageRequest $request): JsonResponse
    {
        try {
            $tireStorage = $this->tireStorageService->createTireStorage($request->validated());

            return $this->successResponse(
                new TireStorageResource($tireStorage),
                'Penyimpanan ban berhasil dibuat',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Gagal membuat penyimpanan ban',
                400,
                [['field' => 'general', 'message' => $e->getMessage()]]
            );
        }
    }

    /**
     * Detail penyimpanan ban
     */
    public function show(int $id): JsonResponse
    {
        $tireStorage = $this->tireStorageService->findTireStorage($id);

        if (!$tireStorage) {
            return $this->errorResponse('Data tidak ditemukan', 404, [
                ['field' => 'general', 'message' => 'Penyimpanan ban tidak ditemukan']
            ]);
        }

        return $this->successResponse(new TireStorageResource($tireStorage));
    }

    /**
     * Update penyimpanan ban
     */
    public function update(TireStorageRequest $request, int $id): JsonResponse
    {
        try {
            $tireStorage = $this->tireStorageService->updateTireStorage($id, $request->validated());

            if (!$tireStorage) {
                return $this->errorResponse('Data tidak ditemukan', 404, [
                    ['field' => 'general', 'message' => 'Penyimpanan ban tidak ditemukan']
                ]);
            }

            return $this->successResponse(
                new TireStorageResource($tireStorage),
                'Penyimpanan ban berhasil diperbarui'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Gagal memperbarui penyimpanan ban',
                400,
                [['field' => 'general', 'message' => $e->getMessage()]]
            );
        }
    }

    /**
     * Hapus penyimpanan ban
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->tireStorageService->deleteTireStorage($id);

            if (!$success) {
                return $this->errorResponse('Data tidak ditemukan', 404, [
                    ['field' => 'general', 'message' => 'Penyimpanan ban tidak ditemukan']
                ]);
            }

            return $this->successResponse(null, 'Penyimpanan ban berhasil dihapus');
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus penyimpanan ban', 400, [
                ['field' => 'general', 'message' => $e->getMessage()]
            ]);
        }
    }

    /**
     * Akhiri penyimpanan ban
     */
    public function end(int $id): JsonResponse
    {
        try {
            $success = $this->tireStorageService->endTireStorage($id);

            if (!$success) {
                return $this->errorResponse('Data tidak ditemukan', 404, [
                    ['field' => 'general', 'message' => 'Penyimpanan ban tidak ditemukan']
                ]);
            }

            return $this->successResponse(null, 'Penyimpanan ban berhasil diakhiri');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400, [
                ['field' => 'general', 'message' => 'Gagal mengakhiri penyimpanan ban']
                ]);
        }
    }

    /**
     * Bulk hapus penyimpanan ban
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return $this->errorResponse('Tidak ada data yang dipilih', 400, [
                ['field' => 'general', 'message' => 'Pilih setidaknya satu penyimpanan ban']
            ]);
        }

        $deletedCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $success = $this->tireStorageService->deleteTireStorage($id);
                $success ? $deletedCount++ : $errors[] = "ID {$id} tidak ditemukan";
            } catch (\Exception $e) {
                $errors[] = "Gagal hapus ID {$id}: {$e->getMessage()}";
            }
        }

        return $this->successResponse(
            [
                'deleted_count' => $deletedCount,
                'errors' => $errors,
            ],
            $deletedCount > 0
                ? "Berhasil menghapus {$deletedCount} data"
                : "Gagal menghapus data"
        );
    }

    /**
     * Bulk akhiri penyimpanan ban
     */
    public function bulkEnd(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return $this->errorResponse('Tidak ada data yang dipilih', 400, [
                ['field' => 'general', 'message' => 'Pilih setidaknya satu penyimpanan ban']
            ]);
        }

        $endedCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $success = $this->tireStorageService->endTireStorage($id);
                $success ? $endedCount++ : $errors[] = "ID {$id} tidak ditemukan";
            } catch (\Exception $e) {
                $errors[] = "Gagal akhiri ID {$id}: {$e->getMessage()}";
            }
        }

        return $this->successResponse(
            [
                'ended_count' => $endedCount,
                'errors' => $errors,
            ],
            $endedCount > 0
                ? "Berhasil mengakhiri {$endedCount} data"
                : "Gagal mengakhiri data"
        );
    }
}
