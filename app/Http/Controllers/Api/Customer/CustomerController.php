<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\TireStorageResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\UserServiceInterface;
use App\Services\ReservationServiceInterface;
use App\Services\TireStorageServiceInterface;
use App\Services\AuthServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected UserServiceInterface $userService,
        protected ReservationServiceInterface $reservationService,
        protected TireStorageServiceInterface $tireStorageService,
        protected AuthServiceInterface $authService,
    ) {}

    /**
     * Get customer profile
     */
    public function profile(): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new UserResource($user),
                'Customer profile retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve profile',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Profile retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'full_name_kana' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'company_name' => 'nullable|string|max:255',
                'department' => 'nullable|string|max:255',
                'company_address' => 'nullable|string|max:500',
                'home_address' => 'nullable|string|max:500',
                'date_of_birth' => 'nullable|date|before:today',
                'gender' => 'nullable|in:male,female,other',
            ]);

            $updatedUser = $this->userService->updateUser($user->id, $validated);

            return $this->successResponse(
                new UserResource($updatedUser),
                'Customer profile updated successfully'
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
                'Failed to update profile',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'update_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Profile update failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get customer reservations
     */
    public function reservations(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            $perPage = min($request->get('per_page', 10), 100);

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                $cursor = $request->get('cursor');
                $reservations = $this->reservationService->getCustomerReservationsWithCursor($user->id, $perPage, $cursor);
                $collection = ReservationResource::collection($reservations);

                $cursorInfo = $this->generateCursor($reservations);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Customer reservations retrieved successfully'
                );
            } else {
                $reservations = $this->reservationService->getReservationsByUser($user->id);
                $collection = ReservationResource::collection($reservations);

                return $this->successResponse(
                    $collection->resolve(),
                    'Customer reservations retrieved successfully'
                );
            }
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve reservations',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Reservations retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get specific customer reservation
     */
    public function reservation(string $id): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            // Convert string to integer and validate
            $reservationId = (int) $id;
            if ($reservationId <= 0) {
                return $this->errorResponse(
                    'Invalid reservation ID',
                    400,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'invalid_format',
                            'value' => $id,
                            'message' => 'Reservation ID must be a positive integer'
                        ]
                    ]
                );
            }

            $reservation = $this->reservationService->findReservation($reservationId);

            if (!$reservation || $reservation->user_id !== $user->id) {
                return $this->errorResponse(
                    'Reservation not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'Reservation not found or access denied'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new ReservationResource($reservation),
                'Reservation retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve reservation',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Reservation retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get customer tire storage
     */
    // public function tireStorage(Request $request ): JsonResponse
    // {
    //     try {
    //         $user = $this->authService->getCurrentUser();

    //         // Ensure only customers can access this
    //         if (!$user->isCustomer()) {
    //             return $this->errorResponse(
    //                 'Access denied',
    //                 403,
    //                  [
    //                      [
    //                       'field'  => 'role',
    //                         'tag' => 'access_denied',
    //                         'value' => $user->role,
    //                         'message' => 'Only customers can access this endpoint'
    //                     ]
    //                 ]
    //             );
    //         }

    //         $perPage = min($request->get('per_page', 10), 100);

    //         if ($request->has('paginate') && $request->get('paginate') !== 'false') {
    //             $cursor = $request->get('cursor');
    //             $tireStorages = $this->tireStorageService->getCustomerTireStorageWithCursor($user->id, $perPage, $cursor);
    //             $collection = TireStorageResource::collection($tireStorages);

    //            $cursorInfo = $this->generateCursor($tireStorages);

    //             return $this->successResponseWithCursor(
    //                 $collection->resolve(),
    //                  $cursorInfo,
    //                 'C ustomer tire storage retrieved successfully'
    //             );
    //         } else {
    //             $tireStorages = $this->tireStorageService->getTireStorageByUser($user->id) ;
    //             $collection = TireStorageResource::collection($tireStorages);

    //              return $this->successResponse(
    //                 $collection->resolve(),
    //                 'Customer tire storage retrieved successfully'
    //              );
    //         }
    //     } catch (\Exception $e) {
    //         return $this->errorResponse(
    //             ' Failed to retrieve tir e storage',
    //              500,
    //              [
    //                 [
    //                     'field'  => 'general',
    //                     'tag' => 'retrieval _faile d',
    //                      'value' => $e->getMessage(),
    //                      'message' => 'Tire storage retrieval failed '
    //                 ]
    //              ]
    //         );
    //     }
    // }

    // /** Get specific customer tire storage */
    public function tireStorageItem(string $id): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            // Convert string to integer and validate
            $tireStorageId = (int) $id;
            if ($tireStorageId <= 0) {
                return $this->errorResponse(
                    'Invalid tire storage ID',
                    400,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'invalid_format',
                            'value' => $id,
                            'message' => 'Tire storage ID must be a positive integer'
                        ]
                    ]
                );
            }

            $tireStorage = $this->tireStorageService->findTireStorage($tireStorageId);

            if (!$tireStorage || $tireStorage->user_id !== $user->id) {
                return $this->errorResponse(
                    'Tire storage not found',
                    404,
                    [
                        [
                            'field' => 'id',
                            'tag' => 'not_found',
                            'value' => $id,
                            'message' => 'Tire storage not found or access denied'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                new TireStorageResource($tireStorage),
                'Tire storage retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve tire storage',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Tire storage retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Change customer password
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            $validated = $request->validate([
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed'
            ]);

            $success = $this->userService->changePassword(
                $user->id,
                $validated['current_password'],
                $validated['new_password']
            );

            if (!$success) {
                return $this->errorResponse(
                    'Invalid current password',
                    400,
                    [
                        [
                            'field' => 'current_password',
                            'tag' => 'invalid_password',
                            'value' => null,
                            'message' => 'Current password is incorrect'
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

    /**
     * Delete customer account
     */
    public function deleteAccount(): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            // Revoke all tokens before deleting account
            $user->tokens()->delete();

            $success = $this->userService->deleteUser($user->id);

            if (!$success) {
                return $this->errorResponse(
                    'Failed to delete account',
                    500,
                    [
                        [
                            'field' => 'general',
                            'tag' => 'deletion_failed',
                            'value' => null,
                            'message' => 'Account deletion failed'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                null,
                'Account deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete account',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'deletion_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Account deletion failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get customer reservations by status
     */
    public function reservationsByStatus(Request $request, string $status): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            // Validate status parameter
            $allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
            if (!in_array($status, $allowedStatuses)) {
                return $this->errorResponse(
                    'Invalid status',
                    400,
                    [
                        [
                            'field' => 'status',
                            'tag' => 'invalid_value',
                            'value' => $status,
                            'message' => 'Status must be one of: ' . implode(', ', $allowedStatuses)
                        ]
                    ]
                );
            }

            $perPage = min($request->get('per_page', 10), 100);

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                $cursor = $request->get('cursor');
                $reservations = $this->reservationService->getCustomerReservationsByStatusWithCursor($user->id, $status, $perPage, $cursor);
                $collection = ReservationResource::collection($reservations);

                $cursorInfo = $this->generateCursor($reservations);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Customer reservations with status ' . $status . ' retrieved successfully'
                );
            } else {
                $reservations = $this->reservationService->getCustomerReservationsByStatus($user->id, $status);
                $collection = ReservationResource::collection($reservations);

                return $this->successResponse(
                    $collection->resolve(),
                    'Customer reservations with status ' . $status . ' retrieved successfully'
                );
            }
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse(
                'Invalid status parameter',
                400,
                [
                    [
                        'field' => 'status',
                        'tag' => 'invalid_argument',
                        'value' => $status,
                        'message' => $e->getMessage()
                    ]
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve reservations by status',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Reservations retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get customer pending reservations
     */
    public function pendingReservations(Request $request): JsonResponse
    {
        return $this->reservationsByStatus($request, 'pending');
    }

    /**
     * Get customer completed reservations
     */
    public function completedReservations(Request $request): JsonResponse
    {
        return $this->reservationsByStatus($request, 'completed');
    }

    /**
     * Get customer reservations summary by status
     */
    public function reservationsSummary(): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            $summary = [
                'total' => $this->reservationService->getReservationCountByUser($user->id),
                'pending' => $this->reservationService->getReservationCountByUserAndStatus($user->id, 'pending'),
                'confirmed' => $this->reservationService->getReservationCountByUserAndStatus($user->id, 'confirmed'),
                'completed' => $this->reservationService->getReservationCountByUserAndStatus($user->id, 'completed'),
                'cancelled' => $this->reservationService->getReservationCountByUserAndStatus($user->id, 'cancelled'),
            ];

            return $this->successResponse(
                $summary,
                'Customer reservations summary retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve reservations summary',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Reservations summary retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get customer dashboard summary
     */
    public function dashboard(): JsonResponse
    {
        try {
            $user = $this->authService->getCurrentUser();

            // Ensure only customers can access this
            if (!$user->isCustomer()) {
                return $this->errorResponse(
                    'Access denied',
                    403,
                    [
                        [
                            'field' => 'role',
                            'tag' => 'access_denied',
                            'value' => $user->role,
                            'message' => 'Only customers can access this endpoint'
                        ]
                    ]
                );
            }

            // Get summary data
            $totalReservations = $this->reservationService->getReservationCountByUser($user->id);
            // $activeTireStorage = $this->tireStorageService->getActiveTireStorageCountByUser($user->id);
            $recentReservations = $this->reservationService->getRecentReservationsByUser($user->id, 3);
            // $recentTireStorage = $this->tireStorageService->getRecentTireStorageByUser($user->id, 3);

            $dashboardData = [
                'summary' => [
                    'total_reservations' => $totalReservations,
                    'pending_reservations' => $this->reservationService->getReservationCountByUserAndStatus($user->id, 'pending'),
                    'completed_reservations' => $this->reservationService->getReservationCountByUserAndStatus($user->id, 'completed'),
                    // 'active_tire_storage' => $activeTireStorage,
                ],
                'recent_reservations' => ReservationResource::collection($recentReservations)->resolve(),
                // 'recent_tire_storage' => TireStorageResource::collection($recentTireStorage)->resolve(),
            ];

            return $this->successResponse(
                $dashboardData,
                'Customer dashboard retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve dashboard',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Dashboard retrieval failed'
                    ]
                ]
            );
        }
    }
}
