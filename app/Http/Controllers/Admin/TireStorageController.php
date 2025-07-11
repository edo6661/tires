<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TireStorageRequest;
use App\Services\TireStorageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TireStorageController extends Controller
{
    protected $tireStorageService;

    public function __construct(TireStorageServiceInterface $tireStorageService)
    {
        $this->tireStorageService = $tireStorageService;
    }

    public function index(Request $request): View
    {
        $tireStorages = $this->tireStorageService->getPaginatedTireStorages(15);
        
        return view('admin.tire-storages.index', compact('tireStorages'));
    }

    public function create(): View
    {
        return view('admin.tire-storages.create');
    }

    public function store(TireStorageRequest $request): RedirectResponse
    {
        try {
            $this->tireStorageService->createTireStorage($request->validated());
            
            return redirect()->route('admin.tire-storages.index')
                ->with('success', 'Penyimpanan ban berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat penyimpanan ban: ' . $e->getMessage());
        }
    }

    public function show(int $id): View
    {
        $tireStorage = $this->tireStorageService->findTireStorage($id);
        
        if (!$tireStorage) {
            abort(404, 'Penyimpanan ban tidak ditemukan');
        }

        return view('admin.tire-storages.show', compact('tireStorage'));
    }

    public function edit(int $id): View
    {
        $tireStorage = $this->tireStorageService->findTireStorage($id);
        
        if (!$tireStorage) {
            abort(404, 'Penyimpanan ban tidak ditemukan');
        }

        return view('admin.tire-storages.edit', compact('tireStorage'));
    }

    public function update(TireStorageRequest $request, int $id): RedirectResponse
    {
        try {
            $tireStorage = $this->tireStorageService->updateTireStorage($id, $request->validated());
            
            if (!$tireStorage) {
                return redirect()->route('admin.tire-storages.index')
                    ->with('error', 'Penyimpanan ban tidak ditemukan');
            }

            return redirect()->route('admin.tire-storages.show', $id)
                ->with('success', 'Penyimpanan ban berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui penyimpanan ban: ' . $e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $success = $this->tireStorageService->deleteTireStorage($id);
            
            if (!$success) {
                return redirect()->route('admin.tire-storages.index')
                    ->with('error', 'Penyimpanan ban tidak ditemukan');
            }

            return redirect()->route('admin.tire-storages.index')
                ->with('success', 'Penyimpanan ban berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus penyimpanan ban: ' . $e->getMessage());
        }
    }

    public function end(int $id): JsonResponse
    {
        try {
            $success = $this->tireStorageService->endTireStorage($id);
            
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penyimpanan ban tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Penyimpanan ban berhasil diakhiri'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function calculateFee(int $id): JsonResponse
    {
        try {
            $fee = $this->tireStorageService->calculateStorageFee($id);
            
            return response()->json([
                'success' => true,
                'fee' => $fee,
                'formatted_fee' => 'Rp ' . number_format($fee, 0, ',', '.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // public function active(): View
    // {
    //     $tireStorages = $this->tireStorageService->getActiveTireStorages();
    //     $status = 'active';
        
    //     return view('admin.tire-storages.by-status', compact('tireStorages', 'status'));
    // }

    // public function ended(): View
    // {
    //     $tireStorages = $this->tireStorageService->getEndedTireStorages();
    //     $status = 'ended';
        
    //     return view('admin.tire-storages.by-status', compact('tireStorages', 'status'));
    // }

    // public function byStatus(string $status): View
    // {
    //     $allowedStatuses = ['active', 'ended'];
        
    //     if (!in_array($status, $allowedStatuses)) {
    //         abort(404, 'Status tidak valid');
    //     }

    //     $tireStorages = $this->tireStorageService->getTireStoragesByStatus($status);
        
    //     return view('admin.tire-storages.by-status', compact('tireStorages', 'status'));
    // }
}