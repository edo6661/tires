<?php

namespace App\Http\Controllers\Api\Customer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\ApiResponseTrait;
use App\Services\AuthServiceInterface;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\TireStorageResource;
use App\Services\ReservationServiceInterface;

/**
 * Get customer  reservations
 *
 * @tags Customer - Reservation
 */

class ReservationController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ReservationServiceInterface $reservationService,
        protected AuthServiceInterface $authService,
    ) {}


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
}
