<?php

namespace App\Http\Controllers\Api\Customer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Events\BookingCompleted;
use App\Events\InquirySubmitted;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiResponseTrait;
use App\Services\AuthServiceInterface;
use App\Services\MenuServiceInterface;
use App\Services\UserServiceInterface;
use App\Http\Requests\ReservationRequest;
use App\Services\ContactServiceInterface;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\TireStorageResource;
use App\Services\ReservationServiceInterface;
use App\Services\TireStorageServiceInterface;
use App\Services\BlockedPeriodServiceInterface;

/**
 * @tags Customer
 */
class CustomerController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected UserServiceInterface $userService,
        protected ReservationServiceInterface $reservationService,
        protected TireStorageServiceInterface $tireStorageService,
        protected AuthServiceInterface $authService,
        protected MenuServiceInterface $menuService,
        protected BlockedPeriodServiceInterface $blockedPeriodService,
        protected ContactServiceInterface $contactService,
    ) {}

    /**
     * Get customer profile
     *
     * @tags Customer
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
     * @tags Customer
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
     * Get customer reservations
     *
     * @tags Customer
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

    /**
     * Create new tire storage entry
     */
    public function createTireStorage(Request $request): JsonResponse
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

            $validator = Validator::make($request->all(), [
                'tire_brand' => 'required|string|max:255',
                'tire_size' => 'required|string|max:100',
                'tire_type' => 'required|string|max:100',
                'quantity' => 'required|integer|min:1|max:10',
                'storage_period_months' => 'required|integer|min:1|max:24',
                'notes' => 'nullable|string|max:1000',
                'pickup_date' => 'nullable|date|after:today',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            $tireStorageData = $request->validated();
            $tireStorageData['user_id'] = $user->id;
            $tireStorageData['status'] = 'active';
            $tireStorageData['storage_start_date'] = now();

            if ($request->pickup_date) {
                $tireStorageData['storage_end_date'] = $request->pickup_date;
            }

            $tireStorage = $this->tireStorageService->createTireStorage($tireStorageData);

            return $this->successResponse(
                new TireStorageResource($tireStorage),
                'Tire storage entry created successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create tire storage',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'creation_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Tire storage creation failed'
                    ]
                ]
            );
        }
    }

    /**
     * Update tire storage entry
     */
    public function updateTireStorage(Request $request, string $id): JsonResponse
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

            $validator = Validator::make($request->all(), [
                'tire_brand' => 'sometimes|string|max:255',
                'tire_size' => 'sometimes|string|max:100',
                'tire_type' => 'sometimes|string|max:100',
                'quantity' => 'sometimes|integer|min:1|max:10',
                'storage_period_months' => 'sometimes|integer|min:1|max:24',
                'notes' => 'nullable|string|max:1000',
                'pickup_date' => 'nullable|date|after:today',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            $updateData = $request->validated();
            if ($request->has('pickup_date')) {
                $updateData['storage_end_date'] = $request->pickup_date;
            }

            $updatedTireStorage = $this->tireStorageService->updateTireStorage($tireStorageId, $updateData);

            return $this->successResponse(
                new TireStorageResource($updatedTireStorage),
                'Tire storage updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update tire storage',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'update_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Tire storage update failed'
                    ]
                ]
            );
        }
    }

    /**
     * Request tire pickup
     */
    public function requestTirePickup(Request $request, string $id): JsonResponse
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

            $validator = Validator::make($request->all(), [
                'pickup_date' => 'required|date|after:today',
                'pickup_notes' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            $updateData = [
                'status' => 'pickup_requested',
                'pickup_requested_date' => now(),
                'storage_end_date' => $request->pickup_date,
                'pickup_notes' => $request->pickup_notes,
            ];

            $updatedTireStorage = $this->tireStorageService->updateTireStorage($tireStorageId, $updateData);

            return $this->successResponse(
                new TireStorageResource($updatedTireStorage),
                'Tire pickup requested successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to request tire pickup',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'request_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Tire pickup request failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get tire storage summary/statistics
     */
    public function getTireStorageSummary(): JsonResponse
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
                'total_storage_entries' => $this->tireStorageService->getTireStorageCountByUser($user->id),
                'active_storage' => $this->tireStorageService->getTireStorageCountByUserAndStatus($user->id, 'active'),
                'pickup_requested' => $this->tireStorageService->getTireStorageCountByUserAndStatus($user->id, 'pickup_requested'),
                'completed_storage' => $this->tireStorageService->getTireStorageCountByUserAndStatus($user->id, 'completed'),
                'total_tires_stored' => $this->tireStorageService->getTotalTiresCountByUser($user->id),
            ];

            return $this->successResponse(
                $summary,
                'Tire storage summary retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve tire storage summary',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Tire storage summary retrieval failed'
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
     * Check availability for specific datetime
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'menu_id' => 'required|integer|exists:menus,id',
                'reservation_datetime' => 'required|date',
                'exclude_reservation_id' => 'nullable|integer|exists:reservations,id'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors()->toArray());
            }

            $available = $this->reservationService->checkAvailability(
                $request->menu_id,
                $request->reservation_datetime,
                $request->exclude_reservation_id
            );

            return $this->successResponse([
                'available' => $available,
                'message' => $available ? 'Time slot is available' : 'Time slot is not available'
            ], 'Availability check completed');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to check availability: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get availability data for date range
     */
    public function getAvailability(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'menu_id' => 'nullable|integer|exists:menus,id',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
                'exclude_reservation_id' => 'nullable|integer|exists:reservations,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $menuId = $request->menu_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $excludeReservationId = $request->exclude_reservation_id;

            $availabilityData = [];
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);

            // Get blocked periods
            $blockedPeriods = $this->blockedPeriodService->getByDateRange($startDate, $endDate);

            // Get existing reservations
            $existingReservations = [];
            if ($menuId) {
                $existingReservations = $this->reservationService->getReservationsByDateRangeAndMenu(
                    $startDate,
                    $endDate,
                    $menuId,
                    $excludeReservationId
                );
            }

            $current = $start->copy();
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                $availableHours = [];

                // Check hours from 8 AM to 8 PM
                for ($hour = 8; $hour <= 20; $hour++) {
                    $hourStr = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                    $isHourAvailable = true;
                    $blockedBy = null;

                    // Check against blocked periods
                    foreach ($blockedPeriods as $period) {
                        if ($period->all_menus || ($menuId && $period->menu_id == $menuId)) {
                            $periodStart = Carbon::parse($period->start_datetime);
                            $periodEnd = Carbon::parse($period->end_datetime);
                            $checkTime = Carbon::parse($dateStr . ' ' . $hourStr);

                            if ($checkTime->between($periodStart, $periodEnd)) {
                                $isHourAvailable = false;
                                $blockedBy = 'blocked_period';
                                break;
                            }
                        }
                    }

                    // Check against existing reservations
                    if ($isHourAvailable && $menuId) {
                        foreach ($existingReservations as $reservation) {
                            $reservationTime = Carbon::parse($reservation->reservation_datetime);
                            $checkTime = Carbon::parse($dateStr . ' ' . $hourStr);

                            if ($reservationTime->format('Y-m-d H:i') === $checkTime->format('Y-m-d H:i')) {
                                $isHourAvailable = false;
                                $blockedBy = 'existing_reservation';
                                break;
                            }
                        }
                    }

                    $availableHours[] = [
                        'hour' => $hourStr,
                        'available' => $isHourAvailable,
                        'blocked_by' => $blockedBy
                    ];
                }

                $availabilityData[] = [
                    'date' => $dateStr,
                    'available_hours' => $availableHours,
                    'has_available_slots' => collect($availableHours)->where('available', true)->count() > 0
                ];

                $current->addDay();
            }

            return response()->json([
                'status' => 'success',
                'data' => $availabilityData,
                'message' => 'Availability data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get availability: ' . $e->getMessage()
            ], 500);
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

    /**
     * BOOKING FUNCTIONALITY - API equivalents of web booking controller methods
     */

    /**
     * Get booking first step data (menu and calendar)
     */
    public function bookingFirstStep(string $menuId): JsonResponse
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
            $menuIdInt = (int) $menuId;
            if ($menuIdInt <= 0) {
                return $this->errorResponse(
                    'Invalid menu ID',
                    400,
                    [
                        [
                            'field' => 'menuId',
                            'tag' => 'invalid_format',
                            'value' => $menuId,
                            'message' => 'Menu ID must be a positive integer'
                        ]
                    ]
                );
            }

            $menu = $this->menuService->findMenu($menuIdInt);
            if (!$menu) {
                return $this->errorResponse(
                    'Menu not found',
                    404,
                    [
                        [
                            'field' => 'menuId',
                            'tag' => 'not_found',
                            'value' => $menuId,
                            'message' => 'Menu not found'
                        ]
                    ]
                );
            }

            $currentMonth = Carbon::now()->startOfMonth();
            $calendarData = $this->generateBookingCalendar($currentMonth, $menu->id);

            return $this->successResponse(
                [
                    'menu' => [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'required_time' => $menu->required_time,
                        'description' => $menu->description,
                        'price' => $menu->price,
                    ],
                    'calendar_data' => $calendarData,
                    'current_month' => $currentMonth->format('Y-m')
                ],
                'Booking first step data retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve booking data',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Booking data retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get calendar data for booking
     */
    public function getCalendarData(Request $request): JsonResponse
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

            $monthParam = $request->get('month', Carbon::now()->format('Y-m'));
            $menuId = $request->get('menu_id');

            if (!$menuId) {
                return $this->errorResponse(
                    'Menu ID is required',
                    400,
                    [
                        [
                            'field' => 'menu_id',
                            'tag' => 'required',
                            'value' => null,
                            'message' => 'Menu ID is required'
                        ]
                    ]
                );
            }

            try {
                $currentMonth = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
            } catch (\Exception $e) {
                return $this->errorResponse(
                    'Invalid month format',
                    400,
                    [
                        [
                            'field' => 'month',
                            'tag' => 'invalid_format',
                            'value' => $monthParam,
                            'message' => 'Month must be in Y-m format (e.g., 2024-01)'
                        ]
                    ]
                );
            }

            $calendarData = $this->generateBookingCalendar($currentMonth, (int) $menuId);

            return $this->successResponse([
                'current_month' => $currentMonth->format('Y-m'),
                'previous_month' => $currentMonth->copy()->subMonth()->format('Y-m'),
                'next_month' => $currentMonth->copy()->addMonth()->format('Y-m'),
                'days' => $calendarData['days'], // hasil dari generateBookingCalendar
            ], 'Calendar data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve calendar data',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Calendar data retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get available hours for a specific date and menu
     */
    public function getAvailableHours(Request $request): JsonResponse
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

            $date = $request->get('date');
            $menuId = $request->get('menu_id');

            if (!$date || !$menuId) {
                return $this->errorResponse(
                    'Date and menu_id are required',
                    400,
                    [
                        [
                            'field' => 'parameters',
                            'tag' => 'required',
                            'value' => compact('date', 'menuId'),
                            'message' => 'Date and menu_id are required parameters'
                        ]
                    ]
                );
            }

            try {
                $selectedDate = Carbon::parse($date);
            } catch (\Exception $e) {
                return $this->errorResponse(
                    'Invalid date format',
                    400,
                    [
                        [
                            'field' => 'date',
                            'tag' => 'invalid_format',
                            'value' => $date,
                            'message' => 'Date must be in valid format (Y-m-d)'
                        ]
                    ]
                );
            }

            $now = Carbon::now();
            if ($selectedDate->isBefore($now->startOfDay())) {
                return $this->successResponse(
                    [
                        'hours' => [],
                        'message' => 'Cannot book for past dates'
                    ],
                    'No available hours for past dates'
                );
            }

            $availableHours = $this->generateAvailableHours($selectedDate, (int) $menuId);

            return $this->successResponse(
                ['hours' => $availableHours],
                'Available hours retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve available hours',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Available hours retrieval failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get menu details for booking
     */
    public function getMenuDetails(string $menuId): JsonResponse
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
            $menuIdInt = (int) $menuId;
            if ($menuIdInt <= 0) {
                return $this->errorResponse(
                    'Invalid menu ID',
                    400,
                    [
                        [
                            'field' => 'menuId',
                            'tag' => 'invalid_format',
                            'value' => $menuId,
                            'message' => 'Menu ID must be a positive integer'
                        ]
                    ]
                );
            }

            $menu = $this->menuService->findMenu($menuIdInt);
            if (!$menu) {
                return $this->errorResponse(
                    'Menu not found',
                    404,
                    [
                        [
                            'field' => 'menuId',
                            'tag' => 'not_found',
                            'value' => $menuId,
                            'message' => 'Menu not found'
                        ]
                    ]
                );
            }

            return $this->successResponse(
                [
                    'menu' => [
                        'id' => $menu->id,
                        'name' => $menu->name,
                        'required_time' => $menu->required_time,
                        'description' => $menu->description,
                        'price' => $menu->price,
                    ]
                ],
                'Menu details retrieved successfully'
            );
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

    /**
     * Create a new reservation (booking)
     */
    public function createReservation(ReservationRequest $request): JsonResponse
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

            // Add user_id to the validated data to ensure reservation is linked to authenticated user
            $validatedData = $request->validated();
            $validatedData['user_id'] = $user->id;

            $reservation = $this->reservationService->createReservation($validatedData);

            // Dispatch booking completed event
            BookingCompleted::dispatch($reservation);

            return $this->successResponse(

                new ReservationResource($reservation->load('menu')),
                'Reservation created successfully'
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
                'Failed to create reservation',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'creation_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Reservation creation failed'
                    ]
                ]
            );
        }
    }

    /**
     * PRIVATE HELPER METHODS - Same as web booking controller
     */

    /**
     * Generate booking calendar
     */
    private function generateBookingCalendar(Carbon $currentMonth, int $menuId): array
    {
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();

        $reservations = $this->reservationService->getReservationsByDateRangeAndMenu(
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s'),
            $menuId
        );

        $reservationsByDate = $reservations->groupBy(function ($reservation) {
            return $reservation->reservation_datetime->format('Y-m-d');
        });

        $blockedHours = $this->blockedPeriodService->getBlockedHoursInRange(
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s')
        );

        $calendarDays = $this->generateCalendarDays(
            $currentMonth,
            $reservationsByDate,
            $blockedHours,
            $menuId
        );

        return [
            'days' => $calendarDays,
            'current_month' => $currentMonth,
            'previous_month' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'next_month' => $currentMonth->copy()->addMonth()->format('Y-m')
        ];
    }

    /**
     * Generate calendar days
     */
    private function generateCalendarDays(
        Carbon $currentMonth,
        $reservationsByDate,
        $blockedHours = null,
        ?int $menuId = null
    ): array {
        $calendarDays = [];
        $today = Carbon::now();
        $todayString = $today->format('Y-m-d');

        $startDate = $currentMonth->copy()->startOfMonth();
        $dayOfWeek = $startDate->dayOfWeek;

        if ($dayOfWeek !== 1) {
            $startDate->subDays($dayOfWeek === 0 ? 6 : $dayOfWeek - 1);
        }

        for ($i = 0; $i < 42; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');

            $isPastDate = $date->isBefore($today->startOfDay());
            $hasAvailableHours = !$isPastDate && $this->hasAvailableHoursForDate($date, $menuId, $blockedHours, $reservationsByDate);
            $bookingStatus = $this->getDateBookingStatus($date, $isPastDate, $hasAvailableHours);

            $calendarDays[] = [
                'date' => $dateString,
                'day' => $date->day,
                'isCurrentMonth' => $date->month === $currentMonth->month,
                'isToday' => $dateString === $todayString,
                'isPastDate' => $isPastDate,
                'hasAvailableHours' => $hasAvailableHours,
                'bookingStatus' => $bookingStatus,
                'blockedHours' => $blockedHours[$dateString] ?? [],
                'reservationCount' => $reservationsByDate->get($dateString, collect())->count()
            ];
        }

        return $calendarDays;
    }

    /**
     * Check if date has available hours
     */
    private function hasAvailableHoursForDate(Carbon $date, int $menuId, $blockedHours, $reservationsByDate): bool
    {
        $menu = $this->menuService->findMenu($menuId);
        $requiredTime = $menu->required_time;
        $dateString = $date->format('Y-m-d');
        $now = Carbon::now();

        $operatingHours = $this->getOperatingHours();
        $closingTime = Carbon::parse($dateString . ' 21:00:00');

        foreach ($operatingHours as $hour) {
            $dateTime = Carbon::parse($dateString . ' ' . $hour);

            if ($dateTime->isBefore($now)) {
                continue;
            }

            $endTime = $dateTime->copy()->addMinutes($requiredTime);
            if ($endTime->gt($closingTime)) {
                continue;
            }

            if (isset($blockedHours[$dateString]) && in_array($hour, $blockedHours[$dateString])) {
                continue;
            }

            $reservations = $reservationsByDate->get($dateString, collect());
            $hasReservationAtThisHour = $reservations->contains(function ($reservation) use ($hour) {
                return $reservation->reservation_datetime->format('H:i') === $hour;
            });

            if (!$hasReservationAtThisHour) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get date booking status
     */
    private function getDateBookingStatus(Carbon $date, bool $isPastDate, bool $hasAvailableHours): string
    {
        if ($isPastDate) {
            return 'past';
        }

        if (!$hasAvailableHours) {
            return 'full';
        }

        return 'available';
    }

    /**
     * Generate available hours for a date
     */
    private function generateAvailableHours(Carbon $selectedDate, int $menuId): array
    {
        $menu = $this->menuService->findMenu($menuId);
        $requiredTime = $menu->required_time;
        $dateString = $selectedDate->format('Y-m-d');
        $now = Carbon::now();

        $availableHours = [];

        $blockedHours = $this->blockedPeriodService->getBlockedHoursInRange(
            $selectedDate->format('Y-m-d H:i:s'),
            $selectedDate->format('Y-m-d H:i:s')
        );

        $reservations = $this->reservationService->getReservationsByDateRangeAndMenu(
            $selectedDate->format('Y-m-d H:i:s'),
            $selectedDate->format('Y-m-d H:i:s'),
            $menuId
        );

        $reservationsByHour = $reservations->groupBy(function ($reservation) {
            return $reservation->reservation_datetime->format('H:i');
        });

        $operatingHours = $this->getOperatingHours();
        $closingTime = Carbon::parse($dateString . ' 21:00:00');

        foreach ($operatingHours as $hour) {
            $dateTime = Carbon::parse($dateString . ' ' . $hour);

            if ($dateTime->isBefore($now)) {
                continue;
            }

            $endTime = $dateTime->copy()->addMinutes($requiredTime);
            if ($endTime->gt($closingTime)) {
                continue;
            }

            $isBlocked = isset($blockedHours[$dateString]) && in_array($hour, $blockedHours[$dateString]);
            $hasReservation = $reservationsByHour->has($hour);

            $status = 'available';
            $indicator = '';
            if ($isBlocked) {
                $status = 'blocked';
                $indicator = 'Blocked';
            } elseif ($hasReservation) {
                $status = 'reserved';
                $indicator = 'Reserved';
            }

            $availableHours[] = [
                'time' => $hour,
                'datetime' => $dateTime->format('Y-m-d H:i:s'),
                'status' => $status,
                'available' => $status === 'available',
                'indicator' => $indicator
            ];
        }

        return $availableHours;
    }

    /**
     * Get operating hours
     */
    private function getOperatingHours(): array
    {
        $hours = [];
        for ($i = 8; $i <= 20; $i++) {
            $hours[] = sprintf('%02d:00', $i);
        }
        return $hours;
    }


    public function submitInquiry(Request $request): JsonResponse
    {
        try {
            $user = Auth::user(); // Get authenticated user (if any)

            // Validation rules - make name, email optional if user is authenticated
            $rules = [
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
            ];

            // If user is not authenticated, require name and email
            if (!$user) {
                $rules['name'] = 'required|string|max:255';
                $rules['email'] = 'required|email|max:255';
            }

            // Phone is always optional
            $rules['phone'] = 'nullable|string|max:20';

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            // Prepare contact data with auto-fill for authenticated users
            $contactData = [
                'user_id' => $user ? $user->id : null,
                'full_name' => $user ? $user->full_name : $request->name,
                'email' => $user ? $user->email : $request->email,
                'phone_number' => $request->phone ?? ($user ? $user->phone_number : null),
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $contact = $this->contactService->createContact($contactData);
            event(new InquirySubmitted($contact));

            $responseData = [
                'inquiry_id' => $contact->id,
                'reference_number' => $contact->created_at->format('YmdHis') . $contact->id,
            ];

            // Add user info to response if authenticated
            if ($user) {
                $responseData['submitted_by'] = $user->full_name;
                $responseData['email'] = $user->email;
                $responseData['auto_filled'] = true;
            } else {
                $responseData['submitted_by'] = $request->name;
                $responseData['email'] = $request->email;
                $responseData['auto_filled'] = false;
            }

            return $this->successResponse(
                $responseData,
                'Thank you for your inquiry! We will get back to you soon.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to submit inquiry',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'submission_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Inquiry submission failed'
                    ]
                ]
            );
        }
    }

    /**
     * INQUIRY AND CONTACT FUNCTIONALITY
     */

    /**
     * Submit customer inquiry
     */
    // public function submitInquiry(Request $request): JsonResponse
    // {
    //     try {
    //         $user = $this->authService->getCurrentUser();

    //         // Ensure only customers can access this
    //         if (!$user->isCustomer()) {
    //             return $this->errorResponse(
    //                 'Access denied',
    //                 403,
    //                 [
    //                     [
    //                         'field' => 'role',
    //                         'tag' => 'access_denied',
    //                         'value' => $user->role,
    //                         'message' => 'Only customers can access this endpoint'
    //                     ]
    //                 ]
    //             );
    //         }

    //         $validator = Validator::make($request->all(), [
    //             'subject' => 'required|string|max:255',
    //             'message' => 'required|string|max:2000',
    //             'phone' => 'nullable|string|max:20',
    //         ]);

    //         if ($validator->fails()) {
    //             return $this->errorResponse(
    //                 'Validation failed',
    //                 422,
    //                 collect($validator->errors())->map(function ($messages, $field) {
    //                     return [
    //                         'field' => $field,
    //                         'tag' => 'validation_error',
    //                         'value' => request($field),
    //                         'message' => $messages[0]
    //                     ];
    //                 })->values()->toArray()
    //             );
    //         }

    //         $contactData = [
    //             'user_id' => $user->id,
    //             'full_name' => $user->full_name,
    //             'email' => $user->email,
    //             'phone_number' => $request->phone ?? $user->phone_number,
    //             'subject' => $request->subject,
    //             'message' => $request->message,
    //         ];

    //         $contact = $this->contactService->createContact($contactData);
    //         event(new InquirySubmitted($contact));

    //         return $this->successResponse(
    //             [
    //                 'inquiry_id' => $contact->id,
    //                 'reference_number' => $contact->created_at->format('YmdHis') . $contact->id,
    //                 'submitted_by' => $user->full_name,
    //                 'email' => $user->email
    //             ],
    //             'Your inquiry has been submitted successfully! We will get back to you soon.'
    //         );
    //     } catch (\Exception $e) {
    //         return $this->errorResponse(
    //             'Failed to submit inquiry',
    //             500,
    //             [
    //                 [
    //                     'field' => 'general',
    //                     'tag' => 'submission_failed',
    //                     'value' => $e->getMessage(),
    //                     'message' => 'Inquiry submission failed'
    //                 ]
    //             ]
    //         );
    //     }
    // }

    /**
     * Submit customer contact message
     */
    public function submitContact(Request $request): JsonResponse
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

            $validator = Validator::make($request->all(), [
                'subject' => 'required|string|max:255',
                'message' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse(
                    'Validation failed',
                    422,
                    collect($validator->errors())->map(function ($messages, $field) {
                        return [
                            'field' => $field,
                            'tag' => 'validation_error',
                            'value' => request($field),
                            'message' => $messages[0]
                        ];
                    })->values()->toArray()
                );
            }

            $contactData = [
                'user_id' => $user->id,
                'full_name' => $user->full_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            $contact = $this->contactService->createContact($contactData);
            event(new InquirySubmitted($contact));

            return $this->successResponse(
                [
                    'contact_id' => $contact->id,
                    'reference_number' => $contact->created_at->format('YmdHis') . $contact->id,
                    'submitted_by' => $user->full_name,
                    'email' => $user->email
                ],
                'Your message has been sent successfully! We will get back to you soon.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to submit contact message',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'submission_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Contact submission failed'
                    ]
                ]
            );
        }
    }

    /**
     * Get customer inquiry history
     */
    public function getInquiryHistory(Request $request): JsonResponse
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

            // Get customer's contact history
            $contacts = $this->contactService->getContactsByUser($user->id);

            return $this->successResponse(
                $contacts->map(function ($contact) {
                    return [
                        'id' => $contact->id,
                        'subject' => $contact->subject,
                        'message' => $contact->message,
                        'phone_number' => $contact->phone_number,
                        'status' => $contact->status ?? 'pending',
                        'reference_number' => $contact->created_at->format('YmdHis') . $contact->id,
                        'submitted_at' => $contact->created_at->format('Y-m-d H:i:s'),
                        'replied_at' => $contact->replied_at ? $contact->replied_at->format('Y-m-d H:i:s') : null,
                    ];
                }),
                'Customer inquiry history retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve inquiry history',
                500,
                [
                    [
                        'field' => 'general',
                        'tag' => 'retrieval_failed',
                        'value' => $e->getMessage(),
                        'message' => 'Inquiry history retrieval failed'
                    ]
                ]
            );
        }
    }
}
