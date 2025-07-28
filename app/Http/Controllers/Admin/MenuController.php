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
            $data = $request->validated();
            $this->menuService->createMenu($data);
            return redirect()->route('admin.menu.index')
                ->with('success', 'Menu created successfully with English and Japanese support.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function show(string $locale,int $id)
    {
        $menu = $this->menuService->findMenu($id);
        if (!$menu) {
            return redirect()->route('admin.menu.index')
                ->with('error', 'Menu not found.');
        }
        return view('admin.menu.show', compact('menu'));
    }
    public function edit(string $locale, int $id)
    {
        $menu = $this->menuService->findMenu($id);
        if (!$menu) {
            return redirect()->route('admin.menu.index')
                            ->with('error', 'Menu not found.');
        }
        $menu->load('translations');
        return view('admin.menu.edit', compact('menu'));
    }
    public function update(MenuRequest $request, string $locale, int $id)
    {
        try {
            $data = $request->validated();
            $menu = $this->menuService->updateMenu($id, $data);
            if (!$menu) {
                return redirect()->route('admin.menu.index')
                    ->with('error', 'Menu not found.');
            }
            return redirect()->route('admin.menu.index')
                ->with('success', 'Menu updated successfully with multilingual support.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function destroy(string $locale,int $id): JsonResponse
    {
        try {
            $deleted = $this->menuService->deleteMenu($id);
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Menu not found.'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Menu deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    public function toggleStatus(string $locale,int $id)
    {
        try {
            $toggled = $this->menuService->toggleMenuStatus($id);
            if (!$toggled) {
                return response()->json(['error' => 'Menu not found.'], 404);
            }
            return response()->json(['success' => 'Menu status changed successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
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
                return response()->json(['error' => 'Failed to reorder menus.'], 500);
            }
            return response()->json(['success' => 'Menus reordered successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
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
                return response()->json(['error' => 'Menu not found.'], 404);
            }
            return response()->json(['end_time' => $endTime]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
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
                    'message' => 'No menus were successfully deleted.'
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message' => 'Menus successfully deleted in bulk.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}