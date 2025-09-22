<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Middleware\ApiSetLocale;
use App\Http\Resources\MenuResource;
use App\Services\MenuServiceInterface;
use App\Http\Requests\MenuIndexRequest;
use App\Http\Requests\MenuRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

/**
 * @mixin \Illuminate\Http\Request
 * @tags Public
 */
class MenuController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected MenuServiceInterface $menuService
    ) {}

    public function index(MenuIndexRequest $request): JsonResponse
    {
        try {
            // request validation
            $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'cursor' => 'nullable|string',
                'paginate' => 'sometimes|in:true,false',
                'locale' => 'sometimes|string|in:en,ja'
            ]);

            $perPage = min($request->get('per_page', 5), 100);
            $locale = App::getLocale();

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                $cursor = $request->get('cursor');
                // Paginated response with cursor
                $menus = $this->menuService->getPaginatedMenusWithCursor($perPage, $cursor);
                $collection = MenuResource::collection($menus);

                // Generate cursor info
                $cursor = $this->generateCursor($menus);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursor,
                    'Menus retrieved successfully'
                );
            } else {
                // Simple response without pagination
                $menus = $this->menuService->getActiveMenus();
                $collection = MenuResource::collection($menus);

                return $this->successResponse(
                    $collection->resolve(),
                    'Menus retrieved successfully'
                );
            }
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve menus',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'server_error',
                        'value' => $e->getMessage(),
                        'message' => 'An unexpected error occurred'
                    ]
                ],
                500
            );
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $menu = $this->menuService->findMenu($id);

            if (!$menu) {
                return $this->errorResponse(
                    'Menu not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'Menu with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new MenuResource($menu),
                'Menu retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve menu',
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
     * Get menu details for booking
     */
    public function getMenuDetails(int $id): JsonResponse
    {
        try {
            $menu = $this->menuService->findMenu($id);

            if (!$menu) {
                return $this->errorResponse(
                    'Menu not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'Menu with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse([
                'id' => $menu->id,
                'name' => $menu->name,
                'description' => $menu->description,
                'required_time' => $menu->required_time,
                'price' => $menu->price,
                'color' => $menu->color,
                'photo_url' => $menu->photo_path ? asset('storage/' . $menu->photo_path) : null,
            ], 'Menu details retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve menu details',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Menu details retrieval failed'
                    ]
                ]
            );
        }
    }
}
