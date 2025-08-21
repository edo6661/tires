<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnnouncementServiceInterface;
use App\Http\Requests\AnnouncementRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class AnnouncementController extends Controller
{
    public function __construct(
        protected AnnouncementServiceInterface $announcementService
    ) {}

    /**
     * List semua pengumuman (paginate)
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $announcements = $this->announcementService->getPaginatedAnnouncements($perPage);

        return response()->json($announcements);
    }

    /**
     * Simpan pengumuman baru
     */
    public function store(AnnouncementRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $announcement = $this->announcementService->createAnnouncement($data);
            foreach ($data['translations'] as $trans) {
                $announcement->translations()->create($trans);
            }
            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil dibuat.',
                'data' => $announcement->load('translations')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Detail pengumuman
     */
    public function show(int $id): JsonResponse
    {
        $announcement = $this->announcementService->findAnnouncement($id);

        if (!$announcement) {
            return response()->json([
                'success' => false,
                'message' => 'Pengumuman tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $announcement
        ]);
    }

    /**
     * Update pengumuman
     */
    public function update(AnnouncementRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $announcement = $this->announcementService->updateAnnouncement($id, $data);

            if (!$announcement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengumuman tidak ditemukan.'
                ], 404);
            }
            if (!empty($data['translations'])) {
                foreach ($data['translations'] as $trans) {
                    $announcement->translations()->updateOrCreate(
                        ['locale' => $trans['locale']], // cek per bahasa
                        ['title' => $trans['title'], 'content' => $trans['content']]
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil diperbarui.',
                'data' => $announcement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus pengumuman
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->announcementService->deleteAnnouncement($id);

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengumuman tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pengumuman berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle status aktif/tidak
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $toggled = $this->announcementService->toggleAnnouncementStatus($id);

            if (!$toggled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengumuman tidak ditemukan.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status pengumuman berhasil diubah.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk ubah status aktif/tidak
     */
    public function bulkToggleStatus(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:announcements,id',
            'status' => 'required|boolean',
        ]);

        try {
            foreach ($request->ids as $id) {
                $announcement = $this->announcementService->findAnnouncement($id);
                if ($announcement) {
                    $announcement->is_active = $request->status;
                    $announcement->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Status beberapa pengumuman berhasil diubah.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk hapus pengumuman
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:announcements,id',
        ]);

        try {
            $success = $this->announcementService->bulkDeleteAnnouncements($request->ids);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada pengumuman yang dihapus.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Beberapa pengumuman berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
