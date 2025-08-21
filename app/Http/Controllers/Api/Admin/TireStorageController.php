<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TireStorageRequest;
use App\Services\TireStorageServiceInterface;
use Illuminate\Http\JsonResponse;

class TireStorageController extends Controller
{
    public function __construct(
        protected TireStorageServiceInterface $tireStorageService,
    ) {
        $this->middleware(['auth:sanctum', 'admin']);
    }

    /**
     *  List semua penyimpanan ban dengan filter & pagination
     */
    public function index(Request $request): JsonResponse
    {
        $filters = array_filter([
            'status' => $request->input('status'),
            'tire_brand' => $request->input('tire_brand'),
            'tire_size' => $request->input('tire_size'),
            'customer_name' => $request->input('customer_name'),
        ]);

        $tireStorages = $this->tireStorageService->getPaginatedTireStoragesWithFilters(
            $request->get('per_page', 15),
            $filters
        );

        return response()->json([
            'success' => true,
            'data' => $tireStorages
        ]);
    }

    /**
     *  Buat penyimpanan ban
     */
    public function store(TireStorageRequest $request): JsonResponse
    {
        try {
            $tireStorage = $this->tireStorageService->createTireStorage($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Penyimpanan ban berhasil dibuat',
                'data' => $tireStorage
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat penyimpanan ban: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     *  Detail penyimpanan ban
     */
    public function show(int $id): JsonResponse
    {
        $tireStorage = $this->tireStorageService->findTireStorage($id);

        if (!$tireStorage) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tireStorage
        ]);
    }

    /**
     *  Update penyimpanan ban
     */
    public function update(TireStorageRequest $request, int $id): JsonResponse
    {
        try {
            $tireStorage = $this->tireStorageService->updateTireStorage($id, $request->validated());

            if (!$tireStorage) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Penyimpanan ban berhasil diperbarui',
                'data' => $tireStorage
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui penyimpanan ban: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     *  Hapus penyimpanan ban
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->tireStorageService->deleteTireStorage($id);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Penyimpanan ban berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus penyimpanan ban: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     *  Akhiri penyimpanan ban
     */
    public function end(int $id): JsonResponse
    {
        try {
            $success = $this->tireStorageService->endTireStorage($id);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Penyimpanan ban berhasil diakhiri'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     *  Bulk hapus penyimpanan ban
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih'
            ], 400);
        }

        $deletedCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $success = $this->tireStorageService->deleteTireStorage($id);
                if ($success) {
                    $deletedCount++;
                } else {
                    $errors[] = "ID {$id} tidak ditemukan";
                }
            } catch (\Exception $e) {
                $errors[] = "Gagal hapus ID {$id}: {$e->getMessage()}";
            }
        }

        return response()->json([
            'success' => $deletedCount > 0,
            'message' => $deletedCount > 0
                ? "Berhasil menghapus {$deletedCount} data" . (!empty($errors) ? " (dengan error: " . implode(', ', $errors) . ")" : '')
                : "Gagal menghapus data: " . implode(', ', $errors),
            'deleted_count' => $deletedCount,
            'errors' => $errors
        ], $deletedCount > 0 ? 200 : 400);
    }

    /**
     *  Bulk akhiri penyimpanan ban
     */
    public function bulkEnd(Request $request): JsonResponse
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dipilih'
            ], 400);
        }

        $endedCount = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $success = $this->tireStorageService->endTireStorage($id);
                if ($success) {
                    $endedCount++;
                } else {
                    $errors[] = "ID {$id} tidak ditemukan";
                }
            } catch (\Exception $e) {
                $errors[] = "Gagal akhiri ID {$id}: {$e->getMessage()}";
            }
        }

        return response()->json([
            'success' => $endedCount > 0,
            'message' => $endedCount > 0
                ? "Berhasil mengakhiri {$endedCount} data" . (!empty($errors) ? " (dengan error: " . implode(', ', $errors) . ")" : '')
                : "Gagal mengakhiri data: " . implode(', ', $errors),
            'ended_count' => $endedCount,
            'errors' => $errors
        ], $endedCount > 0 ? 200 : 400);
    }
}
