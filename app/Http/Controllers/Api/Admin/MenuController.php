<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Middleware\ApiSetLocale;
use App\Http\Resources\MenuResource;
use App\Services\MenuServiceInterface;
use App\Http\Requests\MenuIndexRequest;
use App\Http\Requests\MenuRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

/**
 * @mixin \Illuminate\Http\Request
 * @tags Admin - Menu Management
 */
class MenuController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected MenuServiceInterface $menuService
    ) {}

    public function index(MenuIndexRequest $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 5), 100);
            $locale = App::getLocale();

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                $cursor = $request->get('cursor');
                // Paginated response with cursor
                $menus = $this->menuService->getPaginatedMenusWithCursor($perPage, $cursor);
                $collection = MenuResource::collection($menus);

                // Generate cursor info
                $cursor = $this->generateCursor($menus);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursor,
                    'Menus retrieved successfully'
                );
            } else {
                // Simple response without pagination
                $menus = $this->menuService->getActiveMenus();
                $collection = MenuResource::collection($menus);

                return $this->successResponse(
                    $collection->resolve(),
                    'Menus retrieved successfully'
                );
            }
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve menus',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'server_error',
                        'value' => $e->getMessage(),
                        'message' => 'An unexpected error occurred'
                    ]
                ],
                500
            );
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $menu = $this->menuService->findMenu($id);

            if (!$menu) {
                return $this->errorResponse(
                    'Menu not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'Menu with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new MenuResource($menu),
                'Menu retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve menu',
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

    public function store(MenuRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $menu = $this->menuService->createMenu($data);

            return $this->successResponse(
                new MenuResource($menu->load('translations')),
                'Menu created successfully with English and Japanese support.',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create menu',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'creation_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Menu creation failed'
                    ]
                ]
            );
        }
    }

    public function update(MenuRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $menu = $this->menuService->updateMenu($id, $data);

            if (!$menu) {
                return $this->errorResponse(
                    'Menu not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'Menu with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new MenuResource($menu->load('translations')),
                'Menu updated successfully with multilingual support.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update menu',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'update_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Menu update failed'
                    ]
                ]
            );
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->menuService->deleteMenu($id);

            if (!$deleted) {
                return $this->errorResponse(
                    'Menu not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'Menu with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                null,
                'Menu deleted successfully.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete menu',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'deletion_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Menu deletion failed'
                    ]
                ]
            );
        }
    }

    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $menu = $this->menuService->toggleMenuStatus($id);

            if (!$menu) {
                return $this->errorResponse(
                    'Menu not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'Menu with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new MenuResource($menu),
                'Menu status changed successfully.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to toggle menu status',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'status_toggle_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Failed to toggle menu status'
                    ]
                ]
            );
        }
    }

    /**
     * Reorder menus (admin only)
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'order' => 'required|array',
                'order.*.id' => 'required|integer|exists:menus,id',
                'order.*.display_order' => 'required|integer',
            ]);

            $reordered = $this->menuService->reorderMenus($validated['order']);

            if (!$reordered) {
                return $this->errorResponse(
                    'Failed to reorder menus',
                    500,
                    [
                        [
                            'field' => 'order',
                            'tag' => 'reorder_failed',
                            'value' => $validated['order'],
                            'message' => 'Could not reorder the specified menus'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                ['reordered_count' => count($validated['order'])],
                'Menus reordered successfully'
            );
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
                'Failed to reorder menus',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'reorder_error',
                        'value' => $e->getMessage(),
                        'message' => 'Reorder operation failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get menu details for booking
     */
    public function getMenuDetails(int $id): JsonResponse
    {
        try {
            $menu = $this->menuService->findMenu($id);

            if (!$menu) {
                return $this->errorResponse(
                    'Menu not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'Menu with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse([
                'id' => $menu->id,
                'name' => $menu->name,
                'description' => $menu->description,
                'required_time' => $menu->required_time,
                'price' => $menu->price,
                'color' => $menu->color,
                'photo_url' => $menu->photo_path ? asset('storage/' . $menu->photo_path) : null,
            ], 'Menu details retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve menu details',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Menu details retrieval failed'
                    ]
                ]
            );
        }
    }

    public function calculateEndTime(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'menu_id' => 'required|integer|exists:menus,id',
                'start_time' => 'required|date_format:Y-m-d H:i:s',
            ]);

            $endTime = $this->menuService->calculateMenuEndTime(
                $validated['menu_id'],
                $validated['start_time']
            );

            if (!$endTime) {
                return $this->errorResponse(
                    'Failed to calculate end time',
                    400,
                    [
                        [
                            'field' => 'menu_id',
                            'tag' => 'calculation_failed',
                            'value' => $validated['menu_id'],
                            'message' => 'Could not calculate end time for the given menu'
                        ]
                    ]
                );
            }

            return $this->successResponse([
                'menu_id' => $validated['menu_id'],
                'start_time' => $validated['start_time'],
                'end_time' => $endTime,
            ], 'End time calculated successfully');
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
                'Error calculating end time',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'calculation_error',
                        'value' => $e->getMessage(),
                        'message' => 'End time calculation failed'
                    ]
                ]
            );
        }
    }

    public function getAvailableSlots(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'menu_id' => 'required|integer|exists:menus,id',
                'date' => 'required|date_format:Y-m-d',
            ]);

            $slots = $this->menuService->getAvailableTimeSlots(
                $validated['menu_id'],
                $validated['date']
            );

            return $this->successResponse([
                'menu_id' => $validated['menu_id'],
                'date' => $validated['date'],
                'slots' => $slots,
                'total_slots' => count($slots)
            ], 'Available time slots retrieved successfully');
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
                'Error retrieving time slots',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'slots_retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Failed to retrieve available time slots'
                    ]
                ]
            );
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:menus,id',
            ]);

            $success = $this->menuService->bulkDeleteMenus($validated['ids']);

            if (!$success) {
                return $this->errorResponse(
                    'No menus were successfully deleted',
                    400,
                    [
                        [
                            'field' => 'ids',
                            'tag' => 'bulk_delete_failed',
                            'value' => $validated['ids'],
                            'message' => 'Failed to delete any of the specified menus'
                        ]
                    ]
                );
            }

            return $this->successResponse([
                'deleted_ids' => $validated['ids'],
                'deleted_count' => count($validated['ids'])
            ], 'Menus successfully deleted in bulk.');
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
                'Failed to bulk delete menus',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'bulk_delete_error',
                        'value' => $e->getMessage(),
                        'message' => 'Bulk delete operation failed'
                    ]
                ]
            );
        }
    }

    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:menus,id',
                'status' => 'required|boolean'
            ]);

            $updated = $this->menuService->bulkUpdateMenuStatus(
                $validated['ids'],
                $validated['status']
            );

            if (!$updated) {
                return $this->errorResponse(
                    'Failed to update menu status',
                    500,
                    [
                        [
                            'field' => 'ids',
                            'tag' => 'bulk_status_update_failed',
                            'value' => $validated['ids'],
                            'message' => 'Could not update status for the specified menus'
                        ]
                    ]
                );
            }

            return $this->successResponse([
                'updated_ids' => $validated['ids'],
                'updated_count' => count($validated['ids']),
                'new_status' => $validated['status']
            ], 'Menu status updated successfully for all selected menus.');
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
                'Failed to bulk update status',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'bulk_status_error',
                        'value' => $e->getMessage(),
                        'message' => 'Bulk status update operation failed'
                    ]
                ]
            );
        }
    }

    /**
     * Helper method to generate cursor information
     */
    private function generateCursorInfo(array $result, int $limit): array
    {
        return [
            'limit' => $limit, // Ubah dari per_page ke limit
            'has_more' => $result['has_more'] ?? false,
            'next_cursor' => $result['next_cursor'] ?? null,
            'count' => count($result['data']),
        ];
    }

    /**
     * Create cursor from timestamp (and optionally ID)
     */
    private function createCursor($timestamp, $id = null): string
    {
        // Format: timestamp atau timestamp:id
        $cursorData = $id ? "{$timestamp}:{$id}" : $timestamp;
        return base64_encode($cursorData);
    }

    /**
     * Parse cursor to get timestamp and ID
     */
    private function parseCursor(?string $cursor): ?array
    {
        if (!$cursor) {
            return null;
        }

        try {
            $decoded = base64_decode($cursor);

            // Check if cursor contains ID (format: timestamp:id)
            if (strpos($decoded, ':') !== false) {
                [$timestamp, $id] = explode(':', $decoded, 2);
                return [
                    'timestamp' => $timestamp,
                    'id' => (int) $id
                ];
            }

            // Only timestamp
            return [
                'timestamp' => $decoded,
                'id' => null
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate cursor from Carbon instance
     */
    private function generateCursorFromCarbon(Carbon $date, $id = null): string
    {
        $timestamp = $date->toISOString(); // Format: 2025-08-22T09:10:31Z
        return $this->createCursor($timestamp, $id);
    }
}
