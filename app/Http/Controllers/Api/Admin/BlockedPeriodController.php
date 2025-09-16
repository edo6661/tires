<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\BlockedPeriodServiceInterface;
use App\Services\MenuServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\BlockedPeriodRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

/**
 * @tags Admin - Blocked Period Management
 */
class BlockedPeriodController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected BlockedPeriodServiceInterface $blockedPeriodService,
        protected MenuServiceInterface $menuService
    ) {
    }

    /**
     * Get blocked period statistics overview
     *
     * Returns counts for total, active, upcoming, expired periods
     *
     * @return JsonResponse
     */
    public function getStatistics(Request $request): JsonResponse
    {
        try {
            $filters = [
                'menu_id' => $request->get('menu_id'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'status' => $request->get('status'),
                'all_menus' => $request->get('all_menus'),
                'search' => $request->get('search')
            ];

            // Remove null values from filters
            $filters = array_filter($filters, function($value) {
                return $value !== null && $value !== '';
            });

            $statistics = $this->blockedPeriodService->getStatistics($filters);

            return $this->successResponse([
                'statistics' => $statistics
            ], 'Blocked period statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve blocked period statistics',
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
     * Search blocked periods with enhanced filtering
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'search' => 'nullable|string|max:255',
                'menu_id' => 'nullable|integer|exists:menus,id',
                'status' => 'nullable|string|in:active,upcoming,expired,all',
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'all_menus' => 'nullable|boolean',
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1'
            ]);

            $filters = [];

            if ($request->filled('search')) {
                $filters['search'] = $validated['search'];
            }

            if ($request->filled('menu_id')) {
                $filters['menu_id'] = $validated['menu_id'];
            }

            if ($request->filled('status') && $validated['status'] !== 'all') {
                $filters['status'] = $validated['status'];
            }

            if ($request->filled('start_date')) {
                $filters['start_date'] = $validated['start_date'];
            }

            if ($request->filled('end_date')) {
                $filters['end_date'] = $validated['end_date'];
            }

            if ($request->has('all_menus')) {
                $filters['all_menus'] = $validated['all_menus'];
            }

            $perPage = $validated['per_page'] ?? 15;
            $blockedPeriods = $this->blockedPeriodService->getPaginatedBlockedPeriodsWithFilters($filters, $perPage);
            $statistics = $this->blockedPeriodService->getStatistics($filters);
            $menus = $this->menuService->getActiveMenus();

            return $this->successResponse([
                'blocked_periods' => $blockedPeriods,
                'statistics' => $statistics,
                'menus' => $menus,
                'filters' => $filters,
                'search_term' => $validated['search'] ?? null,
                'results_count' => $blockedPeriods->total(),
                'pagination_info' => [
                    'current_page' => $blockedPeriods->currentPage(),
                    'per_page' => $blockedPeriods->perPage(),
                    'total' => $blockedPeriods->total(),
                    'last_page' => $blockedPeriods->lastPage()
                ]
            ], 'Blocked period search completed successfully');
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
                'Blocked period search failed',
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

    /**
     * Display a listing of blocked periods
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'menu_id' => 'nullable|integer|exists:menus,id',
                'status' => 'nullable|string|in:active,upcoming,expired,all',
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                // 'all_menus' => 'nullable|boolean',
                'search' => 'nullable|string|max:255',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            $filters = array_filter($validated, function($value) {
                return $value !== null && $value !== '';
            });

            // Remove 'all' status as it means no filter
            if (isset($filters['status']) && $filters['status'] === 'all') {
                unset($filters['status']);
            }

            $perPage = $validated['per_page'] ?? 15;
            $blockedPeriods = $this->blockedPeriodService->getPaginatedBlockedPeriodsWithFilters($filters, $perPage);
            $statistics = $this->blockedPeriodService->getStatistics($filters);
            $menus = $this->menuService->getActiveMenus();

            return $this->successResponse([
                'blocked_periods' => $blockedPeriods,
                'statistics' => $statistics,
                'menus' => $menus,
                'filters' => $filters,
                'pagination_info' => [
                    'current_page' => $blockedPeriods->currentPage(),
                    'per_page' => $blockedPeriods->perPage(),
                    'total' => $blockedPeriods->total(),
                    'last_page' => $blockedPeriods->lastPage()
                ]
            ], 'Blocked periods retrieved successfully');
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
            return $this->errorResponse('Failed to retrieve blocked periods: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created blocked period
     */
    public function store(BlockedPeriodRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $blockedPeriod = $this->blockedPeriodService->createBlockedPeriod($validated);

            return $this->successResponse($blockedPeriod, 'Blocked period created successfully', 201);
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create blocked period: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified blocked period
     */
    public function show($id): JsonResponse
    {
        try {
            $blockedPeriod = $this->blockedPeriodService->findBlockedPeriod($id);

            if (!$blockedPeriod) {
                return $this->errorResponse('Blocked period not found', 404);
            }

            return $this->successResponse($blockedPeriod, 'Blocked period retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve blocked period: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified blocked period
     */
    public function update(BlockedPeriodRequest $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            if ($this->hasConflict($validated, $id)) {
                return $this->errorResponse('Schedule conflict detected with existing blocked periods', 409);
            }

            $blockedPeriod = $this->blockedPeriodService->updateBlockedPeriod($id, $validated);

            if (!$blockedPeriod) {
                return $this->errorResponse('Blocked period not found', 404);
            }

            return $this->successResponse($blockedPeriod, 'Blocked period updated successfully');
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse($e->getMessage(), 422);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update blocked period: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified blocked period
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->blockedPeriodService->deleteBlockedPeriod($id);

            if (!$deleted) {
                return $this->errorResponse('Blocked period not found', 404);
            }

            return $this->successResponse(null, 'Blocked period deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete blocked period: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Check for schedule conflicts
     */
    public function checkConflict(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'menu_id' => 'nullable|exists:menus,id',
                'start_datetime' => 'required|date',
                'end_datetime' => 'required|date|after:start_datetime',
                'all_menus' => 'boolean',
                'exclude_id' => 'nullable|integer'
            ]);

            $hasConflict = $this->hasConflict($request->all(), $request->get('exclude_id'));
            $conflictDetails = [];

            if ($hasConflict) {
                $conflictDetails = $this->blockedPeriodService->getConflictDetails(
                    $request->get('menu_id'),
                    $request->get('start_datetime'),
                    $request->get('end_datetime'),
                    $request->get('all_menus', false),
                    $request->get('exclude_id')
                );
            }

            return $this->successResponse([
                'has_conflict' => $hasConflict,
                'conflict_details' => $conflictDetails
            ], 'Conflict check completed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to check conflicts: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get calendar data
     */
    public function calendar(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end', now()->endOfMonth()->format('Y-m-d'));

            $blockedPeriods = $this->blockedPeriodService->getByDateRange($startDate, $endDate);

            $events = [];
            foreach ($blockedPeriods as $period) {
                $events[] = [
                    'id' => $period->id,
                    'title' => $period->all_menus ? 'All Menus Blocked' : $period->menu->name,
                    'start' => $period->start_datetime->toISOString(),
                    'end' => $period->end_datetime->toISOString(),
                    'backgroundColor' => $period->all_menus ? '#ef4444' : ($period->menu->color ?? '#3b82f6'),
                    'borderColor' => $period->all_menus ? '#dc2626' : ($period->menu->color ?? '#2563eb'),
                    'extendedProps' => [
                        'reason' => $period->reason,
                        'all_menus' => $period->all_menus,
                        'menu_name' => $period->menu ? $period->menu->name : null,
                        'duration' => $period->getDurationText()
                    ]
                ];
            }

            return $this->successResponse($events, 'Calendar data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve calendar data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get calendar with conflict indicators
     */
    public function calendarWithConflicts(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->get('end', now()->endOfMonth()->format('Y-m-d'));
            $menuId = $request->get('menu_id');
            $allMenus = $request->get('all_menus', false);

            $blockedPeriods = $this->blockedPeriodService->getByDateRange($startDate, $endDate);

            $events = [];
            $conflictDates = [];

            foreach ($blockedPeriods as $period) {
                $isRelevant = false;

                // Check if this period is relevant to current selection
                if ($allMenus) {
                    $isRelevant = true;
                } elseif ($menuId) {
                    $isRelevant = ($period->all_menus || $period->menu_id == $menuId);
                } else {
                    $isRelevant = true; // Show all if no specific selection
                }

                $backgroundColor = '#3b82f6'; // Default blue
                $borderColor = '#2563eb';

                if ($period->all_menus) {
                    $backgroundColor = '#ef4444'; // Red for all menus
                    $borderColor = '#dc2626';
                } elseif ($isRelevant) {
                    $backgroundColor = '#f59e0b'; // Orange for conflicts
                    $borderColor = '#d97706';
                }

                $events[] = [
                    'id' => $period->id,
                    'title' => $period->all_menus
                        ? 'All Menus Blocked'
                        : ($period->menu ? $period->menu->name : 'Unknown Menu'),
                    'start' => $period->start_datetime->toISOString(),
                    'end' => $period->end_datetime->toISOString(),
                    'backgroundColor' => $backgroundColor,
                    'borderColor' => $borderColor,
                    'classNames' => $isRelevant ? ['conflict-indicator'] : [],
                    'extendedProps' => [
                        'reason' => $period->reason,
                        'all_menus' => $period->all_menus,
                        'menu_name' => $period->menu ? $period->menu->name : null,
                        'duration' => $period->getDurationText(),
                        'is_conflict' => $isRelevant,
                        'conflict_type' => $period->all_menus ? 'all_menus' : 'specific_menu'
                    ]
                ];

                // Mark dates as having conflicts
                if ($isRelevant) {
                    $startDate = $period->start_datetime->format('Y-m-d');
                    $endDate = $period->end_datetime->format('Y-m-d');
                    $current = Carbon::parse($startDate);
                    $end = Carbon::parse($endDate);

                    while ($current->lte($end)) {
                        $conflictDates[] = $current->format('Y-m-d');
                        $current->addDay();
                    }
                }
            }

            return $this->successResponse([
                'events' => $events,
                'conflict_dates' => array_unique($conflictDates),
                'summary' => [
                    'total_periods' => count($blockedPeriods),
                    'conflict_periods' => count(array_filter($events, fn($e) => $e['extendedProps']['is_conflict'])),
                    'conflict_dates_count' => count(array_unique($conflictDates))
                ]
            ], 'Calendar with conflicts retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve calendar with conflicts: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get available time slots
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'date' => 'required|date',
                'menu_id' => 'nullable|exists:menus,id',
                'all_menus' => 'boolean'
            ]);

            $date = $request->get('date');
            $menuId = $request->get('menu_id');
            $allMenus = $request->get('all_menus', false);

            // Get blocked periods for the date
            $blockedPeriods = $this->blockedPeriodService->getBlockedPeriodsByDate($date);

            // Filter relevant blocked periods
            $relevantBlocks = $blockedPeriods->filter(function ($period) use ($menuId, $allMenus) {
                if ($allMenus) {
                    return true; // All periods are relevant when blocking all menus
                }
                return $period->all_menus || ($menuId && $period->menu_id == $menuId);
            });

            // Generate hourly slots (24 hours)
            $slots = [];
            for ($hour = 0; $hour < 24; $hour++) {
                $slotStart = Carbon::parse($date)->setHour($hour)->setMinute(0)->setSecond(0);
                $slotEnd = $slotStart->copy()->addHour();

                $isBlocked = $relevantBlocks->some(function ($period) use ($slotStart, $slotEnd) {
                    return $slotStart->lt($period->end_datetime) && $slotEnd->gt($period->start_datetime);
                });

                $slots[] = [
                    'hour' => $hour,
                    'time' => $slotStart->format('H:i'),
                    'display_time' => $slotStart->format('H:i') . ' - ' . $slotEnd->format('H:i'),
                    'is_blocked' => $isBlocked,
                    'is_available' => !$isBlocked,
                    'datetime' => $slotStart->toISOString()
                ];
            }

            return $this->successResponse([
                'date' => $date,
                'slots' => $slots,
                'blocked_periods' => $relevantBlocks->map(function ($period) {
                    return [
                        'id' => $period->id,
                        'start' => $period->start_datetime->format('H:i'),
                        'end' => $period->end_datetime->format('H:i'),
                        'reason' => $period->reason,
                        'menu_name' => $period->all_menus ? 'All Menus' : ($period->menu ? $period->menu->name : 'Unknown')
                    ];
                })->values(),
                'summary' => [
                    'total_slots' => count($slots),
                    'available_slots' => count(array_filter($slots, fn($s) => $s['is_available'])),
                    'blocked_slots' => count(array_filter($slots, fn($s) => $s['is_blocked'])),
                ]
            ], 'Available slots retrieved successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve available slots: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Batch check conflicts for multiple date ranges
     */
    public function batchCheckConflicts(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'periods' => 'required|array',
                'periods.*.menu_id' => 'nullable|exists:menus,id',
                'periods.*.start_datetime' => 'required|date',
                'periods.*.end_datetime' => 'required|date|after:periods.*.start_datetime',
                'periods.*.all_menus' => 'boolean'
            ]);

            $periods = $request->get('periods');
            $results = [];

            foreach ($periods as $index => $period) {
                $hasConflict = $this->blockedPeriodService->checkScheduleConflictWithExclusion(
                    $period['menu_id'] ?? null,
                    $period['start_datetime'],
                    $period['end_datetime'],
                    $period['all_menus'] ?? false
                );

                $conflictDetails = [];
                if ($hasConflict) {
                    $conflictDetails = $this->blockedPeriodService->getConflictDetails(
                        $period['menu_id'] ?? null,
                        $period['start_datetime'],
                        $period['end_datetime'],
                        $period['all_menus'] ?? false
                    );
                }

                $results[] = [
                    'index' => $index,
                    'has_conflict' => $hasConflict,
                    'conflict_details' => $conflictDetails,
                    'period' => $period
                ];
            }

            return $this->successResponse([
                'results' => $results,
                'summary' => [
                    'total_checked' => count($periods),
                    'conflicts_found' => count(array_filter($results, fn($r) => $r['has_conflict'])),
                    'no_conflicts' => count(array_filter($results, fn($r) => !$r['has_conflict']))
                ]
            ], 'Batch conflict check completed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to batch check conflicts: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Export blocked periods
     */
    public function export(Request $request): JsonResponse
    {
        try {
            $filters = [
                'menu_id' => $request->get('menu_id'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'status' => $request->get('status'),
                'all_menus' => $request->get('all_menus')
            ];

            $exportResult = $this->blockedPeriodService->exportBlockedPeriods($filters);

            return $this->successResponse($exportResult, 'Blocked periods exported successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to export blocked periods: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Bulk delete blocked periods
     */
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:blocked_periods,id'
            ]);

            $deletedCount = $this->blockedPeriodService->bulkDelete($request->get('ids'));

            return $this->successResponse([
                'deleted_count' => $deletedCount,
                'total_requested' => count($request->get('ids'))
            ], "Successfully deleted {$deletedCount} blocked periods");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to bulk delete blocked periods: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Check if there's a conflict with existing blocked periods
     */
    private function hasConflict(array $data, int $excludeId = null): bool
    {
        $menuId = $data['menu_id'] ?? null;
        $allMenus = $data['all_menus'] ?? false;
        $startDatetime = $data['start_datetime'];
        $endDatetime = $data['end_datetime'];

        return $this->blockedPeriodService->checkScheduleConflictWithExclusion(
            $menuId,
            $startDatetime,
            $endDatetime,
            $allMenus,
            $excludeId
        );
    }
}
