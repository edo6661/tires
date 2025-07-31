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
    ) {
    }

    public function index(Request $request): View
    {
        $filters = [
            'status' => $request->input('status'),
            'tire_brand' => $request->input('tire_brand'),
            'tire_size' => $request->input('tire_size'),
            'customer_name' => $request->input('customer_name'),
        ];

        $filters = array_filter($filters, function ($value) {
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
                ->with('success', __('admin/tire-storage/general.notifications.create_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('admin/tire-storage/general.notifications.create_error', ['error' => $e->getMessage()]));
        }
    }

    public function show($locale, int $id): View
    {
        $tireStorage = $this->tireStorageService->findTireStorage($id);

        if (!$tireStorage) {
            abort(404, __('admin/tire-storage/general.notifications.not_found'));
        }

        return view('admin.tire-storages.show', compact('tireStorage'));
    }

    public function edit($locale, int $id): View
    {
        $tireStorage = $this->tireStorageService->findTireStorage($id);
        $users = $this->userService->getCustomers();


        if (!$tireStorage) {
            abort(404, __('admin/tire-storage/general.notifications.not_found'));
        }

        return view('admin.tire-storages.edit', compact('tireStorage', 'users'));
    }

    public function update(TireStorageRequest $request, $locale, int $id): RedirectResponse
    {
        try {
            $tireStorage = $this->tireStorageService->updateTireStorage($id, $request->validated());

            if (!$tireStorage) {
                return redirect()->route('admin.tire-storage.index')
                    ->with('error', __('admin/tire-storage/general.notifications.not_found'));
            }

            return redirect()->route('admin.tire-storage.show', ['locale' => $locale, 'id' => $id])
                ->with('success', __('admin/tire-storage/general.notifications.update_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('admin/tire-storage/general.notifications.update_error', ['error' => $e->getMessage()]));
        }
    }

    public function destroy($locale, int $id): RedirectResponse
    {
        try {
            $success = $this->tireStorageService->deleteTireStorage($id);

            if (!$success) {
                return redirect()->route('admin.tire-storage.index')
                    ->with('error', __('admin/tire-storage/general.notifications.not_found'));
            }

            return redirect()->route('admin.tire-storage.index')
                ->with('success', __('admin/tire-storage/general.notifications.delete_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('admin/tire-storage/general.notifications.delete_error', ['error' => $e->getMessage()]));
        }
    }

    public function end($locale, int $id): JsonResponse
    {
        try {
            $success = $this->tireStorageService->endTireStorage($id);

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/tire-storage/general.notifications.not_found')
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('admin/tire-storage/general.notifications.end_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/tire-storage/general.notifications.no_data_selected')
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
                        $errors[] = __('admin/tire-storage/general.notifications.id_not_found', ['id' => $id]);
                    }
                } catch (\Exception $e) {
                    $errors[] = __('admin/tire-storage/general.notifications.id_delete_error', ['id' => $id, 'error' => $e->getMessage()]);
                }
            }

            if ($deletedCount > 0) {
                $message = __('admin/tire-storage/general.notifications.bulk_delete_success', ['count' => $deletedCount]);
                if (!empty($errors)) {
                    $message = __('admin/tire-storage/general.notifications.bulk_delete_partial', [
                        'count' => $deletedCount,
                        'errors' => implode(', ', $errors)
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'deleted_count' => $deletedCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/tire-storage/general.notifications.bulk_delete_error', ['errors' => implode(', ', $errors)])
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin/tire-storage/general.notifications.bulk_delete_generic_error', ['error' => $e->getMessage()])
            ], 500);
        }
    }

    public function bulkEnd(Request $request): JsonResponse
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/tire-storage/general.notifications.no_data_selected')
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
                        $errors[] = __('admin/tire-storage/general.notifications.id_not_found', ['id' => $id]);
                    }
                } catch (\Exception $e) {
                    $errors[] = __('admin/tire-storage/general.notifications.id_end_error', ['id' => $id, 'error' => $e->getMessage()]);
                }
            }

            if ($endedCount > 0) {
                $message = __('admin/tire-storage/general.notifications.bulk_end_success', ['count' => $endedCount]);
                if (!empty($errors)) {
                    $message = __('admin/tire-storage/general.notifications.bulk_end_partial', [
                        'count' => $endedCount,
                        'errors' => implode(', ', $errors)
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'ended_count' => $endedCount
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/tire-storage/general.notifications.bulk_end_error', ['errors' => implode(', ', $errors)])
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('admin/tire-storage/general.notifications.bulk_end_generic_error', ['error' => $e->getMessage()])
            ], 500);
        }
    }
}
