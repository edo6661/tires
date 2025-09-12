<?php

namespace App\Http\Controllers\Api\Customer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiResponseTrait;
use App\Services\AuthServiceInterface;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TireStorageResource;
use App\Services\TireStorageServiceInterface;

/**
 * @tags Customer - TireStorage
 */
class TireStorageController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected TireStorageServiceInterface $tireStorageService,
        protected AuthServiceInterface $authService,
    ) {}
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
}
