<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\UserServiceInterface;
use Illuminate\Contracts\Pagination\CursorPaginator;

/**
 * 
 */
class UserController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    /**
     * Get all users with cursor pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 15), 100);

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor
                $cursor = $request->get('cursor');
                $users = $this->userService->getPaginatedUsersWithCursor($perPage, $cursor);
                $collection = UserResource::collection($users);

                $cursorInfo = $this->generateCursor($users);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Users retrieved successfully'
                );
            } else {
                // Simple response without pagination
                $users = $this->userService->getAllUsers();
                $collection = UserResource::collection($users);

                return $this->successResponse(
                    $collection->resolve(),
                    'Users retrieved successfully'
                );
            }
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve users',
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
     * Store a newly created user
     */
    public function store(UserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return $this->successResponse(
                new UserResource($user),
                'User created successfully',
                201
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create user',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'creation_failed',
                        'value' => $e->getMessage(),
                        'message' => 'User creation failed'
                    ]
                ]
            );
        }
    }

    /**
     * Display the specified user
     */
    public function show($id): JsonResponse
    {
        try {
            $user = $this->userService->findUser($id);

            if (!$user) {
                return $this->errorResponse(
                    'User not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'User with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new UserResource($user),
                'User retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve user',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'User retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Update the specified user
     */
    public function update(UserRequest $request, int $id): JsonResponse
    {
        try {
            $user = $this->userService->updateUser($id, $request->validated());

            if (!$user) {
                return $this->errorResponse(
                    'User not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'User with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new UserResource($user),
                'User updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update user',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'update_failed',
                        'value' => $e->getMessage(),
                        'message' => 'User update failed'
                    ]
                ]
            );
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->userService->deleteUser($id);

            if (!$success) {
                return $this->errorResponse(
                    'User not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'User with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                null,
                'User deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete user',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'deletion_failed',
                        'value' => $e->getMessage(),
                        'message' => 'User deletion failed'
                    ]
                ]
            );
        }
    }

    /**
     * Search users - UPDATED
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'q' => 'required|string|min:1|max:255',
                'per_page' => 'sometimes|integer|min:1|max:100',
                'cursor' => 'sometimes|string'
            ]);

            $query = $validated['q'];
            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;

            $users = $this->userService->searchUsersWithCursor($query, $perPage, $cursor);
            $collection = UserResource::collection($users);

            $cursorInfo = $this->generateCursor($users);
            return $this->successResponseWithCursor(
                $collection->resolve(),
                $cursorInfo,
                'User search completed successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Search failed',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'search_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Search operation failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get users by role
     */
    public function byRole(Request $request, string $role): JsonResponse
    {
        try {
            $allowedRoles = ['customer', 'admin'];

            if (!in_array($role, $allowedRoles)) {
                return $this->errorResponse(
                    'Invalid role',
                    400,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'invalid_role',
                            'value' => $role,
                            'message' => 'Role must be one of: ' . implode(', ', $allowedRoles)
                        ]
                    ]
                );
            }

            $perPage = min($request->get('per_page', 15), 100);
            $cursor = $request->get('cursor');

            $users = $this->userService->getUsersByRoleWithCursor($role, $perPage, $cursor);
            $collection = UserResource::collection($users);

            $cursorInfo = $this->generateCursor($users);
            return $this->successResponseWithCursor(
                $collection->resolve(),
                $cursorInfo,
                "Users with role '{$role}' retrieved successfully"
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve users by role',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Failed to retrieve users'
                    ]
                ]
            );
        }
    }

    /**
     * Get customers only
     */
    public function customers(Request $request): JsonResponse
    {
        return $this->byRole($request, 'customer');
    }

    /**
     * Get admins only
     */
    public function admins(Request $request): JsonResponse
    {
        return $this->byRole($request, 'admin');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'new_password' => 'required|string|min:8|confirmed'
            ]);

            $success = $this->userService->resetPassword($id, $validated['new_password']);

            if (!$success) {
                return $this->errorResponse(
                    'User not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'User with given ID does not exist'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                null,
                'Password reset successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to reset password',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'reset_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Password reset failed'
                    ]
                ]
            );
        }
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed'
            ]);

            $success = $this->userService->changePassword(
                $id,
                $validated['current_password'],
                $validated['new_password']
            );

            if (!$success) {
                return $this->errorResponse(
                    'Invalid current password or user not found',
                    400,
                    [
                        [
                            'field' => 'current_password',
                            'tag' => 'invalid_password',
                            'value' => null,
                            'message' => 'Current password is incorrect or user not found'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                null,
                'Password changed successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->errorResponse(
                'Validation failed',
                422,
                collect($e->errors())->map(function ($messages, $field) {
                    return [
                        'field' => $field,
                        'tag' => 'validation_error',
                        'value' => request($field),
                        'message' => $messages[0]
                    ];
                })->values()->toArray()
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to change password',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'change_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Password change failed'
                    ]
                ]
            );
        }
    }
}
