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
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\TireStorageResource;
use App\Services\ReservationServiceInterface;

/**
 * @tags Customer - Dashboard
 */
class CustomerController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        // protected UserServiceInterface $userService,
        protected ReservationServiceInterface $reservationService,
        protected AuthServiceInterface $authService,
    ) {}


    /**
     * Get customer dashboard summary
     *
     * @tags Customer - Dashboard
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
