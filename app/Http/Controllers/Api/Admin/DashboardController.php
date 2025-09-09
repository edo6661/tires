<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnnouncementServiceInterface;
use App\Services\ReservationServiceInterface;
use App\Services\ContactServiceInterface;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\ContactResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @tags Admin
 */
class DashboardController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AnnouncementServiceInterface $announcementService,
        protected ReservationServiceInterface $reservationService,
        protected ContactServiceInterface $contactService
    ) {
    }

    /**
     * Get admin dashboard data with locale-filtered translations
     *
     * @return JsonResponse Dashboard data with announcements, reservations, contacts, and statistics
     */
    public function index(): JsonResponse
    {
        try {
            // Get announcements
            $announcements = $this->announcementService->getActiveAnnouncements()->take(5);

            // Get today's reservations
            $todayReservations = $this->reservationService->getTodayReservations();

            // Get pending contacts
            $pendingContacts = $this->contactService->getPendingContacts()->take(5);
            $pendingContactsCount = $this->contactService->getPendingContacts()->count();

            // Get monthly reservations
            $currentMonth = Carbon::now()->format('Y-m');
            $monthlyReservations = $this->reservationService->getReservationsByDateRange(
                Carbon::now()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->endOfMonth()->format('Y-m-d')
            );

            // Get customer statistics
            $newCustomersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('role', 'customer')
                ->count();

            $onlineReservationsThisMonth = $monthlyReservations->count();
            $totalCustomers = User::where('role', 'customer')->count();

            // Customer limit calculation
            $customerLimit = 100;
            $customersUntilLimit = $customerLimit - $totalCustomers;

            // Get last login
            $lastLogin = Auth::user()->last_login_at ?? Carbon::now();

            $dashboardData = [
                'announcements' => AnnouncementResource::collection($announcements),
                'today_reservations' => ReservationResource::collection($todayReservations),
                'pending_contacts_count' => $pendingContactsCount,
                'pending_contacts' => ContactResource::collection($pendingContacts),
                'monthly_reservations' => ReservationResource::collection($monthlyReservations),
                'statistics' => [
                    'new_customers_this_month' => $newCustomersThisMonth,
                    'online_reservations_this_month' => $onlineReservationsThisMonth,
                    'total_customers' => $totalCustomers,
                    'customers_until_limit' => $customersUntilLimit,
                    'customer_limit' => $customerLimit,
                ],
                'last_login' => $lastLogin,
                'current_month' => $currentMonth,
            ];

            return $this->successResponse($dashboardData, 'Dashboard data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve dashboard data: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get dashboard statistics only
     *
     * @return JsonResponse Dashboard statistics without detailed data
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $newCustomersThisMonth = User::whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->where('role', 'customer')
                ->count();

            $totalCustomers = User::where('role', 'customer')->count();
            $customerLimit = 100;

            $monthlyReservations = $this->reservationService->getReservationsByDateRange(
                Carbon::now()->startOfMonth()->format('Y-m-d'),
                Carbon::now()->endOfMonth()->format('Y-m-d')
            );

            $pendingContactsCount = $this->contactService->getPendingContacts()->count();

            $statistics = [
                'new_customers_this_month' => $newCustomersThisMonth,
                'online_reservations_this_month' => $monthlyReservations->count(),
                'total_customers' => $totalCustomers,
                'customers_until_limit' => $customerLimit - $totalCustomers,
                'customer_limit' => $customerLimit,
                'pending_contacts_count' => $pendingContactsCount,
                'today_reservations_count' => $this->reservationService->getTodayReservations()->count(),
            ];

            return $this->successResponse($statistics, 'Dashboard statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve dashboard statistics: ' . $e->getMessage(), 500);
        }
    }
}
