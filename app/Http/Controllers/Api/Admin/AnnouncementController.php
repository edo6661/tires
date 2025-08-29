<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\AnnouncementResource;
use App\Services\AnnouncementServiceInterface;
use App\Http\Requests\AnnouncementRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

/**
 * @mixin \Illuminate\Http\Request
 */
class AnnouncementController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AnnouncementServiceInterface $announcementService
    ) {}

    /**
     * List semua pengumuman (paginate / non-paginate)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);
            $locale = App::getLocale();

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor
                $announcements = $this->announcementService->getPaginatedAnnouncementsWithCursor($perPage);
                $collection = AnnouncementResource::collection($announcements);

                $cursor = $this->generateCursor($announcements);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursor,
                    'Announcements retrieved successfully'
                );
            } else {
                // Simple response without pagination
                $announcements = $this->announcementService->getActiveAnnouncements();
                $collection = AnnouncementResource::collection($announcements);

                return $this->successResponse(
                    $collection->resolve(),
                    'Announcements retrieved successfully'
                );
            }
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve announcements',
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
     * Simpan pengumuman baru
     */
    public function store(AnnouncementRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $announcement = $this->announcementService->createAnnouncement($data);

            return $this->successResponse(
                new AnnouncementResource($announcement),
                'Announcement created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create announcement',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }

    /**
     * Detail pengumuman
     */
    public function show(int $id): JsonResponse
    {
        $announcement = $this->announcementService->findAnnouncement($id);

        if (!$announcement) {
            return $this->errorResponse('Announcement not found', 404, [
                [
                    'field' => 'general',
                    'tag' => 'not_found',
                    'value' => null,
                    'message' => 'Announcement not found'
                ]
            ]);
        }

        return $this->successResponse(
            new AnnouncementResource($announcement),
            'Announcement retrieved successfully'
        );
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
                return $this->errorResponse('Announcement not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'Announcement not found'
                    ]
                ]);
            }

            return $this->successResponse(
                new AnnouncementResource($announcement),
                'Announcement updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update announcement',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
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

                return $this->errorResponse('Announcement not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'Announcement not found'
                    ]
                ]);
            }

            return $this->successResponse(null, 'Announcement deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete announcement',
                500,
                [[
                    'field' => 'general',
                    'tag' => 'server_error',
                    'value' => $e->getMessage(),
                    'message' => 'An unexpected error occurred'
                ]]
            );
        }
    }
}
