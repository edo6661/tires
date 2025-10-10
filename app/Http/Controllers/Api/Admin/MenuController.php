<?php

namespace App\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\MenuRequest;
use App\Http\Requests\BulkDeleteMenuRequest;
use App\Http\Requests\BulkUpdateStatusRequest;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Middleware\ApiSetLocale;
use App\Http\Traits\ApiResponseTrait;
use App\Services\MenuServiceInterface;
use App\Http\Requests\MenuIndexRequest;


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

    /**
     * Get menu statistics overview
     *
     * Returns counts for total, active, inactive menus and average price
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->menuService->getMenuStatistics();

            return $this->successResponse([
                'statistics' => $statistics
            ], 'Menu statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve menu statistics',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'statistics_error',
                        'value' => $e->getMessage(),
                        'message' => 'Statistics retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Search menus with enhanced filtering
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|string|in:active,inactive,all',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1'
            ]);

            $filters = [];

            if ($request->filled('search')) {
                $filters['search'] = $validated['search'];
            }

            if ($request->filled('status') && $validated['status'] !== 'all') {
                $filters['status'] = $validated['status'] === 'active';
            }

            if ($request->filled('min_price')) {
                $filters['min_price'] = $validated['min_price'];
            }

            if ($request->filled('max_price')) {
                $filters['max_price'] = $validated['max_price'];
            }

            $perPage = $validated['per_page'] ?? 15;
            $menus = $this->menuService->searchMenusWithFilters($filters, $perPage);

            return $this->successResponse([
                'menus' => MenuResource::collection($menus),
                'filters' => $filters,
                'search_term' => $validated['search'] ?? null,
                'results_count' => $menus->total(),
                'pagination_info' => [
                    'current_page' => $menus->currentPage(),
                    'per_page' => $menus->perPage(),
                    'total' => $menus->total(),
                    'last_page' => $menus->lastPage()
                ]
            ], 'Menu search completed successfully');
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
                'Menu search failed',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'search_error',
                        'value' => $e->getMessage(),
                        'message' => 'Search operation failed'
                    ]
                ]
            );
        }
    }



    public function index(MenuIndexRequest $request): JsonResponse
    {
        try {
            // Validate query parameters
            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|in:active,inactive,all',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'per_page' => 'sometimes|integer|min:1|max:100',
                'paginate' => 'sometimes|in:true,false',
                'cursor' => 'sometimes|string',
                'locale' => 'sometimes|string|in:en,ja',
                'sort_by' => 'nullable|in:display_order,name,price,created_at,updated_at',
                'sort_order' => 'nullable|in:asc,desc'
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            // Handle locale
            $requestedLocale = $request->get('locale');
            if ($requestedLocale) {
                $request->headers->set('X-Locale', $requestedLocale);
                app()->setLocale($requestedLocale);
            }

            // Extract validated parameters
            $search = $request->get('search');
            $status = $request->get('status');
            $minPrice = $request->get('min_price');
            $maxPrice = $request->get('max_price');
            $perPage = min($request->get('per_page', 15), 100);
            $cursor = $request->get('cursor');
            $sortBy = $request->get('sort_by', 'display_order');
            $sortOrder = $request->get('sort_order', 'asc');

            // Build query with filters
            $query = \App\Models\Menu::with(['translations'])
                ->when($search, function ($q) use ($search) {
                    $q->whereHas('translations', function ($translationQuery) use ($search) {
                        $translationQuery->where('name', 'ILIKE', "%{$search}%")
                            ->orWhere('description', 'ILIKE', "%{$search}%");
                    });
                })
                ->when($status && $status !== 'all', function ($q) use ($status) {
                    $q->where('is_active', $status === 'active');
                })
                ->when($minPrice, function ($q) use ($minPrice) {
                    $q->where('price', '>=', $minPrice);
                })
                ->when($maxPrice, function ($q) use ($maxPrice) {
                    $q->where('price', '<=', $maxPrice);
                });

            // Apply sorting
            $query->orderBy($sortBy, $sortOrder);

            // Add secondary sort by id for consistent cursor pagination
            $query->orderBy('id', $sortOrder);

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor
                $menus = $query->cursorPaginate($perPage, ['*'], 'cursor', $cursor);

                // Transform data using MenuResource
                $transformedData = $menus->getCollection()->map(function ($menu) use ($request) {
                    return (new MenuResource($menu))->toArray($request);
                })->values();

                // Generate cursor info
                $cursorInfo = $this->generateCursor($menus);

                // Get filter options for UI
                $filterOptions = $this->getFilterOptions();

                return $this->successResponseWithCursor(
                    $transformedData->toArray(),
                    $cursorInfo,
                    'Menus retrieved successfully',
                    200,
                    [
                        'filters' => [
                            'current' => [
                                'search' => $search,
                                'status' => $status,
                                'min_price' => $minPrice,
                                'max_price' => $maxPrice,
                                'sort_by' => $sortBy,
                                'sort_order' => $sortOrder
                            ],
                            'options' => $filterOptions
                        ],
                        'statistics' => [
                            'total_results' => $menus->count(),
                            'showing' => $menus->count(),
                            'from' => 1,
                            'to' => $menus->count()
                        ]
                    ]
                );
            } else {
                // Simple response without pagination
                $menus = $query->get();

                // Transform data using MenuResource
                $transformedData = $menus->map(function ($menu) use ($request) {
                    return (new MenuResource($menu))->toArray($request);
                })->values();

                // Get filter options for UI
                $filterOptions = $this->getFilterOptions();

                return $this->successResponse(
                    $transformedData->toArray(),
                    'Menus retrieved successfully',
                    200,
                    [
                        'filters' => [
                            'current' => [
                                'search' => $search,
                                'status' => $status,
                                'min_price' => $minPrice,
                                'max_price' => $maxPrice,
                                'sort_by' => $sortBy,
                                'sort_order' => $sortOrder
                            ],
                            'options' => $filterOptions
                        ],
                        'statistics' => [
                            'total_results' => $menus->count(),
                            'showing' => $menus->count(),
                            'from' => 1,
                            'to' => $menus->count()
                        ]
                    ]
                );
            }
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve menus',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Menus retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get filter options for menu list
     */
    private function getFilterOptions(): array
    {
        return [
            'statuses' => [
                ['value' => 'all', 'label' => 'All Statuses'],
                ['value' => 'active', 'label' => 'Active'],
                ['value' => 'inactive', 'label' => 'Inactive']
            ],
            'sort_options' => [
                ['value' => 'display_order', 'label' => 'Display Order'],
                ['value' => 'name', 'label' => 'Name'],
                ['value' => 'price', 'label' => 'Price'],
                ['value' => 'created_at', 'label' => 'Created Date'],
                ['value' => 'updated_at', 'label' => 'Updated Date']
            ],
            'price_ranges' => [
                ['label' => 'Under $1,000', 'min' => 0, 'max' => 1000],
                ['label' => '$1,000 - $3,000', 'min' => 1000, 'max' => 3000],
                ['label' => '$3,000 - $5,000', 'min' => 3000, 'max' => 5000],
                ['label' => 'Over $5,000', 'min' => 5000, 'max' => null]
            ]
        ];
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

    public function destroy($id): JsonResponse
    {
        try {
            $id = (int) $id;
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
            // Explicit validation for bulk delete to avoid MenuRequest validation
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:menus,id',
            ], [
                'ids.required' => 'The IDs field is required.',
                'ids.array' => 'The IDs must be an array.',
                'ids.*.integer' => 'Each ID must be an integer.',
                'ids.*.exists' => 'One or more selected menus do not exist.',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            $validated = $validator->validated();
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
            // Explicit validation for bulk update status to avoid MenuRequest validation
            $validator = Validator::make($request->all(), [
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:menus,id',
                'status' => 'required|boolean'
            ], [
                'ids.required' => 'The IDs field is required.',
                'ids.array' => 'The IDs must be an array.',
                'ids.*.integer' => 'Each ID must be an integer.',
                'ids.*.exists' => 'One or more selected menus do not exist.',
                'status.required' => 'The status field is required.',
                'status.boolean' => 'The status must be true or false.',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            $validated = $validator->validated();
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
    // private function generateCursorInfo(array $result, int $limit): array
    // {
    //     return [
    //         'limit' => $limit, // Ubah dari per_page ke limit
    //         'has_more' => $result['has_more'] ?? false,
    //         'next_cursor' => $result['next_cursor'] ?? null,
    //         'count' => count($result['data']),
    //     ];
    // }

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
