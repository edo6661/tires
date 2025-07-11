<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BlockedPeriodServiceInterface;
use App\Http\Requests\BlockedPeriodRequest;
use Illuminate\Http\Request;

class BlockedPeriodController extends Controller
{

    public function __construct(protected BlockedPeriodServiceInterface $blockedPeriodService)
    {
    }

    public function index()
    {
        $blockedPeriods = $this->blockedPeriodService->getPaginatedBlockedPeriods(15);
        return view('admin.blocked-period.index', compact('blockedPeriods'));
    }

    public function create()
    {
        return view('admin.blocked-period.create');
    }

    public function store(BlockedPeriodRequest $request)
    {
        try {
            $this->blockedPeriodService->createBlockedPeriod($request->validated());
            return redirect()->route('admin.blocked-period.index')
                ->with('success', 'Periode blokir berhasil dibuat.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(int $id)
    {
        $blockedPeriod = $this->blockedPeriodService->findBlockedPeriod($id);
        if (!$blockedPeriod) {
            return redirect()->route('admin.blocked-period.index')
                ->with('error', 'Periode blokir tidak ditemukan.');
        }
        return view('admin.blocked-period.show', compact('blockedPeriod'));
    }

    public function edit(int $id)
    {
        $blockedPeriod = $this->blockedPeriodService->findBlockedPeriod($id);
        if (!$blockedPeriod) {
            return redirect()->route('admin.blocked-period.index')
                ->with('error', 'Periode blokir tidak ditemukan.');
        }
        return view('admin.blocked-period.edit', compact('blockedPeriod'));
    }

    public function update(BlockedPeriodRequest $request, int $id)
    {
        try {
            $blockedPeriod = $this->blockedPeriodService->updateBlockedPeriod($id, $request->validated());
            if (!$blockedPeriod) {
                return redirect()->route('admin.blocked-period.index')
                    ->with('error', 'Periode blokir tidak ditemukan.');
            }
            return redirect()->route('admin.blocked-period.index')
                ->with('success', 'Periode blokir berhasil diperbarui.');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $deleted = $this->blockedPeriodService->deleteBlockedPeriod($id);
            if (!$deleted) {
                return redirect()->route('admin.blocked-period.index')
                    ->with('error', 'Periode blokir tidak ditemukan.');
            }
            return redirect()->route('admin.blocked-period.index')
                ->with('success', 'Periode blokir berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function active()
    {
        $activeBlockedPeriods = $this->blockedPeriodService->getActiveBlockedPeriods();
        return view('admin.blocked-period.active', compact('activeBlockedPeriods'));
    }

    public function checkConflict(Request $request)
    {
        $request->validate([
            'menu_id' => 'required|integer',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);

        $hasConflict = $this->blockedPeriodService->checkScheduleConflict(
            $request->menu_id,
            $request->start_datetime,
            $request->end_datetime
        );

        return view('admin.blocked-period.conflict-check', compact('hasConflict'));
    }
}
