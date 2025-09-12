<?php

namespace App\Http\Controllers\Api\Customer;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\AuthServiceInterface;
use App\Services\UserServiceInterface;
use Illuminate\Validation\Rules\Password;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\TireStorageResource;
use App\Services\ReservationServiceInterface;
use App\Services\TireStorageServiceInterface;

/**
 * @tags Customer - Profile
 */
class ProfileController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected UserServiceInterface $userService,
        protected AuthServiceInterface $authService,
        protected TireStorageServiceInterface $tireStorageService,
        protected ReservationServiceInterface $reservationService,
    ) {}


    /**
     * Get customer profile
     *
     * @tags Customer - Profile
     * @summary Get current customer profile information
     * @description Retrieve the profile information of the currently authenticated customer
     * @response 200 {"status": "success", "message": "Customer profile retrieved successfully", "data": {"id": 1, "full_name": "John Doe", "email": "john@example.com"}}
     * @response 403 {"status": "error", "message": "Access denied", "errors": []}
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
     *
     * @tags Customer - Profile
     * @summary Update customer profile information
     * @description Update the profile information of the currently authenticated customer
     * @bodyParam full_name string required Customer's full name
     * @bodyParam full_name_kana string required Customer's full name in Katakana
     * @bodyParam phone_number string required Customer's phone number
     * @bodyParam email string required Customer's email address
     * @bodyParam company_name string optional Company name
     * @bodyParam department string optional Department
     * @bodyParam company_address string optional Company address
     * @bodyParam home_address string optional Home address
     * @bodyParam date_of_birth date optional Date of birth
     * @bodyParam gender string optional Gender (male, female, other)
     * @response 200 {"status": "success", "message": "Customer profile updated successfully", "data": {}}
     * @response 422 {"status": "error", "message": "Validation failed", "errors": []}
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
     * Get customer reservations
     *
     * @tags Customer - Booking
     * @summary Get customer reservations list
     * @description Retrieve all reservations for the currently authenticated customer with optional pagination
     * @queryParam per_page integer optional Number of items per page (max 100)
     * @queryParam cursor string optional Cursor for pagination
     * @queryParam paginate string optional Enable pagination (default: true)
     * @response 200 {"status": "success", "message": "Customer reservations retrieved successfully", "data": []}
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
    public function tireStorage(Request $request): JsonResponse
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
                $tireStorages = $this->tireStorageService->getCustomerTireStorageWithCursor($user->id, $perPage, $cursor);
                $collection = TireStorageResource::collection($tireStorages);

                $cursorInfo = $this->generateCursor($tireStorages);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Customer tire storage retrieved successfully'
                );
            } else {
                $tireStorages = $this->tireStorageService->getTireStorageByUser($user->id);
                $collection = TireStorageResource::collection($tireStorages);

                return $this->successResponse(
                    $collection->resolve(),
                    'Customer tire storage retrieved successfully'
                );
            }
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

    /** Get specific customer tire storage */
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
}
