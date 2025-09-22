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
 * @tags Admin - Announcement Management
 */
class AnnouncementController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AnnouncementServiceInterface $announcementService
    ) {}

    /**
     * List All Announcement (paginate / non-paginate) with filtering and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate query parameters
            $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'paginate' => 'sometimes|in:true,false',
                'cursor' => 'nullable|string',
                'status' => 'nullable|in:active,inactive',
                'published_at' => 'nullable|in:asc,desc',
                'search' => 'nullable|string|max:255'
            ]);

            $perPage = min($request->get('per_page', 15), 100);
            $locale = App::getLocale();

            // Get filters from request
            $filters = [
                'status' => $request->get('status'),
                'published_at' => $request->get('published_at'),
                'search' => $request->get('search')
            ];

            // Remove empty filters
            $filters = array_filter($filters);

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor and filters
                $cursor = $request->get('cursor');
                $announcements = $this->announcementService->getPaginatedAnnouncementsWithCursor($perPage, $cursor, $filters);
                $collection = AnnouncementResource::collection($announcements);

                $cursorInfo = $this->generateCursor($announcements);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Announcements retrieved successfully'
                );
            } else {
                // Simple response without pagination but with filters
                if (!empty($filters)) {
                    $announcements = $this->announcementService->getFilteredAnnouncements($filters, $perPage);
                } else {
                    $announcements = $this->announcementService->getActiveAnnouncements()->take($perPage);
                }
                $collection = AnnouncementResource::collection($announcements);

                return $this->successResponse(
                    [
                        $collection->resolve(),
                        'filters_applied' => $filters,
                        'total_filtered' => $announcements->count()
                    ],
                    'Announcements retrieved successfully'
                );
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
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
     * Create New Announcement
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
     * Get Announcement Detail
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
     * Update Announcement
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
     * Delete Announcement
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

    /**
     * Get announcement statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->announcementService->getAnnouncementStatistics();

            return $this->successResponse(
                $stats,
                'Announcement statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve announcement statistics',
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
     * Toggle announcement status (active/inactive)
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
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

            $newStatus = $announcement->status === 'active' ? 'inactive' : 'active';
            $updatedAnnouncement = $this->announcementService->updateAnnouncement($id, ['status' => $newStatus]);

            return $this->successResponse(
                new AnnouncementResource($updatedAnnouncement),
                "Announcement status changed to {$newStatus} successfully"
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to toggle announcement status',
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
     * Bulk toggle announcement status
     */
    public function bulkToggleStatus(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:announcements,id',
                'status' => 'required|in:active,inactive'
            ]);

            $ids = $request->get('ids');
            $status = $request->get('status');
            $updatedCount = 0;
            $errors = [];

            foreach ($ids as $id) {
                try {
                    $announcement = $this->announcementService->updateAnnouncement($id, ['status' => $status]);
                    if ($announcement) {
                        $updatedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'id' => $id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return $this->successResponse([
                'updated_count' => $updatedCount,
                'total_requested' => count($ids),
                'errors' => $errors,
                'status' => $status
            ], "Successfully updated {$updatedCount} announcements to {$status} status");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to bulk toggle announcement status',
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
     * Bulk delete announcements
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:announcements,id'
            ]);

            $ids = $request->get('ids');
            $deletedCount = 0;
            $errors = [];

            foreach ($ids as $id) {
                try {
                    $deleted = $this->announcementService->deleteAnnouncement($id);
                    if ($deleted) {
                        $deletedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = [
                        'id' => $id,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return $this->successResponse([
                'deleted_count' => $deletedCount,
                'total_requested' => count($ids),
                'errors' => $errors
            ], "Successfully deleted {$deletedCount} announcements");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to bulk delete announcements',
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
