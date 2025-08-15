<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Middleware\ApiSetLocale;
use App\Http\Resources\MenuResource;
use App\Services\MenuServiceInterface;
use App\Http\Requests\MenuIndexRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;

/**
 * @mixin \Illuminate\Http\Request
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
            $perPage = min($request->get('per_page', 15), 100);
            $locale = App::getLocale();

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor
                $menus = $this->menuService->getPaginatedMenus($perPage);
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
}
