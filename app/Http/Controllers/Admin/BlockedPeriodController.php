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
            // Assuming you have a 'create.php' lang file as well
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
}
