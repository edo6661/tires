<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MenuServiceInterface;
use App\Http\Requests\MenuRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MenuController extends Controller
{
    public function __construct(protected MenuServiceInterface $menuService)
    {
        
    }

    public function index()
    {
        $menus = $this->menuService->getPaginatedMenus(15);
        return view('admin.menu.index', compact('menus'));
    }

    public function create()
    {
        return view('admin.menu.create');
    }

    public function store(MenuRequest $request)
    {
        try {
            $this->menuService->createMenu($request->validated());
            return redirect()->route('admin.menu.index')
                ->with('success', 'Menu berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(int $id)
    {
        $menu = $this->menuService->findMenu($id);
        if (!$menu) {
            return redirect()->route('admin.menu.index')
                ->with('error', 'Menu tidak ditemukan.');
        }
        return view('admin.menu.show', compact('menu'));
    }

    public function edit(int $id)
    {
        $menu = $this->menuService->findMenu($id);
        if (!$menu) {
            return redirect()->route('admin.menu.index')
                ->with('error', 'Menu tidak ditemukan.');
        }
        return view('admin.menu.edit', compact('menu'));
    }

    public function update(MenuRequest $request, int $id)
    {
        try {
            $menu = $this->menuService->updateMenu($id, $request->validated());
            if (!$menu) {
                return redirect()->route('admin.menu.index')
                    ->with('error', 'Menu tidak ditemukan.');
            }
            return redirect()->route('admin.menu.index')
                ->with('success', 'Menu berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->menuService->deleteMenu($id);
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu tidak ditemukan.'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function toggleStatus(int $id)
    {
        try {
            $toggled = $this->menuService->toggleMenuStatus($id);
            if (!$toggled) {
                return response()->json(['error' => 'Menu tidak ditemukan.'], 404);
            }
            return response()->json(['success' => 'Status menu berhasil diubah.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function reorder(Request $request)
    {
        try {
            $request->validate([
                'order' => 'required|array',
                'order.*.id' => 'required|integer',
                'order.*.display_order' => 'required|integer',
            ]);

            $reordered = $this->menuService->reorderMenus($request->input('order'));
            if (!$reordered) {
                return response()->json(['error' => 'Gagal mengurutkan menu.'], 500);
            }
            return response()->json(['success' => 'Menu berhasil diurutkan.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function calculateEndTime(Request $request)
    {
        try {
            $request->validate([
                'menu_id' => 'required|integer|exists:menus,id',
                'start_time' => 'required|date',
            ]);

            $endTime = $this->menuService->calculateMenuEndTime(
                $request->input('menu_id'),
                $request->input('start_time')
            );

            if (!$endTime) {
                return response()->json(['error' => 'Menu tidak ditemukan.'], 404);
            }

            return response()->json(['end_time' => $endTime]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ids' => 'required|array',
                'ids.*' => 'integer|exists:menus,id',
            ]);
            

            $success = $this->menuService->bulkDeleteMenus($request->ids);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada menu yang berhasil dihapus.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Menu berhasil dihapus secara bulk.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}