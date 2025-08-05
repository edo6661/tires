<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ApiSetLocale;
use App\Http\Resources\MenuResource;
use App\Services\MenuServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;

class MenuController extends Controller
{
    public function __construct(
        protected MenuServiceInterface $menuService
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 100); // Max 100 items per page
            $locale = App::getLocale();
            
            // Get menus with translations
            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                $menus = $this->menuService->getPaginatedMenus($perPage);
            } else {
                $menus = $this->menuService->getActiveMenus();
            }

            $collection = MenuResource::collection($menus);
            
            // Add metadata to response
            $data = $collection->additional([
                'meta' => [
                    'locale' => $locale,
                    'supported_locales' => ApiSetLocale::getSupportedLocales(),
                    'timestamp' => now()->toISOString()
                ]
            ]);
            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => [
                    'message' => 'Failed to retrieve menus',
                    'code' => 'MENU_RETRIEVAL_ERROR'
                ],
                'meta' => [
                    'locale' => App::getLocale(),
                    'timestamp' => now()->toISOString()
                ]
            ], 500);
        }
    }

}
