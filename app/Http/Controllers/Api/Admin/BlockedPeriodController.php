<?php

namespace App\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Requests\BlockedPeriodRequest;
use App\Http\Resources\BlockedPeriodResource;
use App\Services\BlockedPeriodServiceInterface;

/**
 * @mixin \Illuminate\Http\Request
 * @tags Admin - Blocked Period Management
 */
class BlockedPeriodController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected BlockedPeriodServiceInterface $blockedPeriodService
    ) {}

    /**
     * List All Blocked Periods (paginate / non-paginate) with filtering and search
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate query parameters
            $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'paginate' => 'nullable|string',
                'cursor' => 'nullable|string',
                'status' => 'nullable|in:active,upcoming,expired,all',
                'menu_id' => 'nullable|integer|exists:menus,id',
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'all_menus' => 'nullable|boolean',
                'search' => 'nullable|string|max:255'
            ]);

            $perPage = min($request->get('per_page', 15), 100);
            $locale = App::getLocale();

            // Get filters from request
            $filters = [
                'status' => $request->get('status'),
                'menu_id' => $request->get('menu_id'),
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date'),
                'all_menus' => $request->get('all_menus'),
                'search' => $request->get('search')
            ];

            // Remove empty filters
            $filters = array_filter($filters, function ($value) {
                return $value !== null && $value !== '' && $value !== 'all';
            });

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor and filters
                $cursor = $request->get('cursor');
                $blockedPeriods = $this->blockedPeriodService->getPaginatedBlockedPeriodsWithCursor($perPage, $cursor, $filters);
                $collection = BlockedPeriodResource::collection($blockedPeriods);

                $cursorInfo = $this->generateCursor($blockedPeriods);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Blocked periods retrieved successfully'
                );
            } else {
                // Simple response without pagination but with filters
                if (!empty($filters)) {
                    $blockedPeriods = $this->blockedPeriodService->getPaginatedBlockedPeriodsWithFilters($filters, $perPage);
                } else {
                    $blockedPeriods = $this->blockedPeriodService->getAllBlockedPeriods()->take($perPage);
                }
                $collection = BlockedPeriodResource::collection($blockedPeriods);

                return $this->successResponse(
                    $collection->resolve(),
                    'Blocked periods retrieved successfully'
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
                'Failed to retrieve blocked periods',
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
     * Create New Blocked Period
     */
    public function store(BlockedPeriodRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $blockedPeriod = $this->blockedPeriodService->createBlockedPeriod($data);

            return $this->successResponse(
                new BlockedPeriodResource($blockedPeriod),
                'Blocked period created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create blocked period',
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
     * Get Blocked Period Detail
     */
    public function show(int $id): JsonResponse
    {
        $blockedPeriod = $this->blockedPeriodService->findBlockedPeriod($id);

        if (!$blockedPeriod) {
            return $this->errorResponse('Blocked period not found', 404, [
                [
                    'field' => 'general',
                    'tag' => 'not_found',
                    'value' => null,
                    'message' => 'Blocked period not found'
                ]
            ]);
        }

        return $this->successResponse(
            new BlockedPeriodResource($blockedPeriod),
            'Blocked period retrieved successfully'
        );
    }

    /**
     * Update Blocked Period
     */
    public function update(BlockedPeriodRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $blockedPeriod = $this->blockedPeriodService->updateBlockedPeriod($id, $data);

            if (!$blockedPeriod) {
                return $this->errorResponse('Blocked period not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'Blocked period not found'
                    ]
                ]);
            }

            return $this->successResponse(
                new BlockedPeriodResource($blockedPeriod),
                'Blocked period updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update blocked period',
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
     * Delete Blocked Period
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->blockedPeriodService->deleteBlockedPeriod($id);

            if (!$deleted) {
                return $this->errorResponse('Blocked period not found', 404, [
                    [
                        'field' => 'general',
                        'tag' => 'not_found',
                        'value' => null,
                        'message' => 'Blocked period not found'
                    ]
                ]);
            }

            return $this->successResponse(null, 'Blocked period deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete blocked period',
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
     * Get blocked period statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $stats = $this->blockedPeriodService->getBlockedPeriodStatistics();

            return $this->successResponse(
                $stats,
                'Blocked period statistics retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve blocked period statistics',
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
