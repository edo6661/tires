<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TireStorageRequest;
use App\Services\TireStorageServiceInterface;
use App\Services\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TireStorageController extends Controller
{

    public function __construct(
        protected TireStorageServiceInterface $tireStorageService,
        protected UserServiceInterface $userService,
        )
    {
    }

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->input('status'),
            'tire_brand' => $request->input('tire_brand'),
            'tire_size' => $request->input('tire_size'),
            'customer_name' => $request->input('customer_name'),
        ];

        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        $tireStorages = $this->tireStorageService->getPaginatedTireStoragesWithFilters(15, $filters);
        
        return view('admin.tire-storages.index', compact('tireStorages'));
    }

    public function create(): View
    {
        $users = $this->userService->getCustomers();
        return view('admin.tire-storages.create', compact('users'));
    }

    public function store(TireStorageRequest $request): RedirectResponse
    {
        try {
            $this->tireStorageService->createTireStorage($request->validated());
            
            return redirect()->route('admin.tire-storage.index')
                ->with('success', 'Penyimpanan ban berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat penyimpanan ban: ' . $e->getMessage());
        }
    }

    public function show($locale, int $id): View
    {
        $tireStorage = $this->tireStorageService->findTireStorage($id);
        
        if (!$tireStorage) {
            abort(404, 'Penyimpanan ban tidak ditemukan');
        }

        return view('admin.tire-storages.show', compact('tireStorage'));
    }

    public function edit($locale, int $id): View
    {
        $tireStorage = $this->tireStorageService->findTireStorage($id);
        $users = $this->userService->getCustomers();

        
        if (!$tireStorage) {
            abort(404, 'Penyimpanan ban tidak ditemukan');
        }

        return view('admin.tire-storages.edit', compact('tireStorage', 'users'));
    }

    public function update(TireStorageRequest $request, $locale, int $id): RedirectResponse
    {
        try {
            $tireStorage = $this->tireStorageService->updateTireStorage($id, $request->validated());
            
            if (!$tireStorage) {
                return redirect()->route('admin.tire-storage.index')
                    ->with('error', 'Penyimpanan ban tidak ditemukan');
            }

            return redirect()->route('admin.tire-storage.show', $id)
                ->with('success', 'Penyimpanan ban berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui penyimpanan ban: ' . $e->getMessage());
        }
    }

    public function destroy($locale, int $id): RedirectResponse
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

    public function end($locale, int $id): JsonResponse
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
    // public function active(): View
    // {
    //     $tireStorages = $this->tireStorageService->getActiveTireStorages();
    //     $status = 'active';
        
    //     return view('admin.tire-storages.by-status', compact('tireStorages', 'status'));
    // }
    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih'
                ], 400);
            }

            $deletedCount = 0;
            $errors = [];

            foreach ($ids as $id) {
                try {
                    $success = $this->tireStorageService->deleteTireStorage($id);
                    if ($success) {
                        $deletedCount++;
                    } else {
                        $errors[] = "Penyimpanan ban ID {$id} tidak ditemukan";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error menghapus ID {$id}: " . $e->getMessage();
                }
            }

            if ($deletedCount > 0) {
                $message = "{$deletedCount} penyimpanan ban berhasil dihapus";
                if (!empty($errors)) {
                    $message .= ". Namun ada beberapa error: " . implode(', ', $errors);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang berhasil dihapus. Errors: ' . implode(', ', $errors)
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle bulk end operation
     */
    public function bulkEnd(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang dipilih'
                ], 400);
            }

            $endedCount = 0;
            $errors = [];

            foreach ($ids as $id) {
                try {
                    $success = $this->tireStorageService->endTireStorage($id);
                    if ($success) {
                        $endedCount++;
                    } else {
                        $errors[] = "Penyimpanan ban ID {$id} tidak ditemukan";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error mengakhiri ID {$id}: " . $e->getMessage();
                }
            }

            if ($endedCount > 0) {
                $message = "{$endedCount} penyimpanan ban berhasil diakhiri";
                if (!empty($errors)) {
                    $message .= ". Namun ada beberapa error: " . implode(', ', $errors);
                }
                
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'ended_count' => $endedCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data yang berhasil diakhiri. Errors: ' . implode(', ', $errors)
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengakhiri penyimpanan: ' . $e->getMessage()
            ], 500);
        }
    }
}