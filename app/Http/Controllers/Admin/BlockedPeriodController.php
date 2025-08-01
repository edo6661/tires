<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BlockedPeriodServiceInterface;
use App\Services\MenuServiceInterface;
use App\Http\Requests\BlockedPeriodRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BlockedPeriodController extends Controller
{
    public function __construct(
        protected BlockedPeriodServiceInterface $blockedPeriodService,
        protected MenuServiceInterface $menuService
    ) {}

    public function index(Request $request)
    {
        $filters = [
            'menu_id' => $request->get('menu_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'status' => $request->get('status'),
            'all_menus' => $request->get('all_menus'),
            'search' => $request->get('search')
        ];
        $blockedPeriods = $this->blockedPeriodService->getPaginatedBlockedPeriodsWithFilters($filters, 15);
        $menus = $this->menuService->getActiveMenus();
        return view('admin.blocked-period.index', compact('blockedPeriods', 'menus', 'filters'));
    }

    public function create()
    {
        $menus = $this->menuService->getActiveMenus();
        return view('admin.blocked-period.create', compact('menus'));
    }

    public function store(BlockedPeriodRequest $request)
    {
        try {
            $validated = $request->validated();
            $this->blockedPeriodService->createBlockedPeriod($validated);
            return redirect()->route('admin.blocked-period.index')
                ->with('success', __('admin/blocked-period/general.flash_messages.create_success'));
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('admin/blocked-period/general.flash_messages.create_error', ['message' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function show($locale, int $id)
    {
        $blockedPeriod = $this->blockedPeriodService->findBlockedPeriod($id);
        if (!$blockedPeriod) {
            return redirect()->route('admin.blocked-period.index')
                ->with('error', __('admin/blocked-period/general.flash_messages.not_found'));
        }
        return view('admin.blocked-period.show', compact('blockedPeriod'));
    }

    public function edit($locale, int $id)
    {
        $blockedPeriod = $this->blockedPeriodService->findBlockedPeriod($id);
        if (!$blockedPeriod) {
            return redirect()->route('admin.blocked-period.index')
                ->with('error', __('admin/blocked-period/general.flash_messages.not_found'));
        }
        $menus = $this->menuService->getActiveMenus();
        return view('admin.blocked-period.edit', compact('blockedPeriod', 'menus'));
    }

    public function update(BlockedPeriodRequest $request, $locale, int $id)
    {
        try {
            $validated = $request->validated();
            if ($this->hasConflict($validated, $id)) {
                return redirect()->back()
                    ->with('error', __('admin/blocked-period/general.flash_messages.conflict'))
                    ->withInput();
            }
            $blockedPeriod = $this->blockedPeriodService->updateBlockedPeriod($id, $validated);
            if (!$blockedPeriod) {
                return redirect()->route('admin.blocked-period.index')
                    ->with('error', __('admin/blocked-period/general.flash_messages.not_found'));
            }
            return redirect()->route('admin.blocked-period.index')
                ->with('success', __('admin/blocked-period/general.flash_messages.update_success'));
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('admin/blocked-period/general.flash_messages.update_error', ['message' => $e->getMessage()]))
                ->withInput();
        }
    }

    public function destroy($locale, int $id)
    {
        try {
            $deleted = $this->blockedPeriodService->deleteBlockedPeriod($id);
            if (!$deleted) {
                return redirect()->route('admin.blocked-period.index')
                    ->with('error', __('admin/blocked-period/general.flash_messages.not_found'));
            }
            return redirect()->route('admin.blocked-period.index')
                ->with('success', __('admin/blocked-period/general.flash_messages.delete_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('admin/blocked-period/general.flash_messages.delete_error', ['message' => $e->getMessage()]));
        }
    }

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

    public function checkConflict(Request $request)
    {
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
        return response()->json([
            'has_conflict' => $hasConflict,
            'conflict_details' => $conflictDetails
        ]);
    }

    public function calendar(Request $request)
    {
        $startDate = $request->get('start', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end', now()->endOfMonth()->format('Y-m-d'));
        $blockedPeriods = $this->blockedPeriodService->getByDateRange($startDate, $endDate);
        $events = [];
        foreach ($blockedPeriods as $period) {
            $events[] = [
                'id' => $period->id,
                'title' => $period->all_menus ? __('admin/blocked-period/general.calendar.all_menus_label') : $period->menu->name,
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
        return response()->json($events);
    }

    public function export(Request $request)
    {
        $filters = [
            'menu_id' => $request->get('menu_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'status' => $request->get('status'),
            'all_menus' => $request->get('all_menus')
        ];
        return $this->blockedPeriodService->exportBlockedPeriods($filters);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:blocked_periods,id'
        ]);
        try {
            $deletedCount = $this->blockedPeriodService->bulkDelete($request->get('ids'));
            return response()->json([
                'success' => true,
                'message' => __('admin/blocked-period/general.flash_messages.bulk_delete_success', ['count' => $deletedCount])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin/blocked-period/general.flash_messages.bulk_delete_error', ['message' => $e->getMessage()])
            ], 500);
        }
    }
    public function calendarWithConflicts(Request $request)
    {
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
                    ? __('admin/blocked-period/general.calendar.all_menus_label') 
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
        
        return response()->json([
            'events' => $events,
            'conflict_dates' => array_unique($conflictDates),
            'summary' => [
                'total_periods' => count($blockedPeriods),
                'conflict_periods' => count(array_filter($events, fn($e) => $e['extendedProps']['is_conflict'])),
                'conflict_dates_count' => count(array_unique($conflictDates))
            ]
        ]);
    }

    /**
     * Get available time slots for a specific date and menu
     */
    public function getAvailableSlots(Request $request)
    {
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
        
        return response()->json([
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
        ]);
    }

    /**
     * Batch check conflicts for multiple date ranges
     */
    public function batchCheckConflicts(Request $request)
    {
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
        
        return response()->json([
            'results' => $results,
            'summary' => [
                'total_checked' => count($periods),
                'conflicts_found' => count(array_filter($results, fn($r) => $r['has_conflict'])),
                'no_conflicts' => count(array_filter($results, fn($r) => !$r['has_conflict']))
            ]
        ]);
    }
}
