<?php

namespace App\Http\Controllers\Api;

use App\Events\BookingCompleted;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Http\Resources\ReservationResource;
use App\Services\ReservationServiceInterface;
use App\Services\BlockedPeriodService;
use App\Services\MenuServiceInterface;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\ApiResponseTrait;

class ReservationController extends Controller
{
    use ApiResponseTrait;
    public function __construct(
        protected ReservationServiceInterface $reservationService,
        protected BlockedPeriodService $blockedPeriodService,
        protected MenuServiceInterface $menuService,
        protected UserService $userService,
    ) {}

    /**
     * Get all reservations with cursor pagination (like MenuController)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->get('per_page', 10), 100);

            if ($request->has('paginate') && $request->get('paginate') !== 'false') {
                // Paginated response with cursor
                $cursor = $request->get('cursor');
                $reservations = $this->reservationService->getPaginatedReservationsCursor($perPage, $cursor);
                $collection = ReservationResource::collection($reservations);

                // Generate cursor info using existing method from MenuController pattern
                $cursorInfo = $this->generateCursor($reservations);

                return $this->successResponseWithCursor(
                    $collection->resolve(),
                    $cursorInfo,
                    'Reservations retrieved successfully'
                );
            } else {
                // Simple response without pagination
                $reservations = $this->reservationService->getAllReservations();
                $collection = ReservationResource::collection($reservations);

                return $this->successResponse(
                    $collection->resolve(),
                    'Reservations retrieved successfully'
                );
            }
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve reservations',
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
     * Store a newly created reservation
     */
    public function store(ReservationRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // Remove customer_type from validated data as it's not stored in the database
            unset($validatedData['customer_type']);

            // Auto-assign authenticated user if no user_id is provided and user is authenticated
            if (!isset($validatedData['user_id']) && $request->user()) {
                $validatedData['user_id'] = $request->user()->id;
            }

            $reservation = $this->reservationService->createReservation($validatedData);

            // Load user relationship if reservation has user_id
            if ($reservation->user_id) {
                $reservation->load('user');
            }

            BookingCompleted::dispatch($reservation);

            return $this->successResponse(
                new ReservationResource($reservation),
                'Reservation created successfully',
                201
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create reservation: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified reservation
     */
    public function show(int $id): JsonResponse
    {
        try {
            $reservation = $this->reservationService->findReservation($id);

            if (!$reservation) {
                return $this->errorResponse('Reservation not found', 404);
            }

            // Load user relationship if reservation has user_id
            if ($reservation->user_id) {
                $reservation->load('user');
            }

            return $this->successResponse(
                new ReservationResource($reservation),
                'Reservation retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve reservation: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Update the specified reservation
     */
    public function update(ReservationRequest $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();

            // Remove customer_type from validated data as it's not stored in the database
            unset($validatedData['customer_type']);

            $reservation = $this->reservationService->updateReservation($id, $validatedData);

            if (!$reservation) {
                return $this->errorResponse('Reservation not found', 404);
            }

            // Load user relationship if reservation has user_id
            if ($reservation->user_id) {
                $reservation->load('user');
            }

            return $this->successResponse(
                new ReservationResource($reservation),
                'Reservation updated successfully'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update reservation: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified reservation
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->deleteReservation($id);

            if (!$success) {
                return $this->errorResponse('Reservation not found', 404);
            }

            return $this->successResponse(
                null,
                'Reservation deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete reservation: ' . $e->getMessage(),
                500
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
     * Get calendar data for booking
     */
    public function getCalendarData(Request $request): JsonResponse
    {
        try {
            $monthParam = $request->get('month', Carbon::now()->format('Y-m'));
            $menuId = $request->get('menu_id');

            if (!$menuId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Menu ID is required'
                ], 400);
            }

            $currentMonth = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
            $calendarData = $this->generateOptimizedBookingCalendar($currentMonth, $menuId);

            return response()->json([
                'status' => 'success',
                'data' => $calendarData,
                    // 'current_month' => $currentMonth->format('F Y'),
                    // 'previous_month' => $currentMonth->copy()->subMonth()->format('Y-m'),
                    // 'next_month' => $currentMonth->copy()->addMonth()->format('Y-m')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get calendar data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available hours for specific date
     */
    public function getAvailableHours(Request $request): JsonResponse
    {
        try {
            $date = $request->get('date');
            $menuId = $request->get('menu_id');

            if (!$date || !$menuId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Date and menu_id are required'
                ], 400);
            }

            $selectedDate = Carbon::parse($date);
            $now = Carbon::now();

            if ($selectedDate->isBefore($now->startOfDay())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot book for past dates',
                    'hours' => []
                ]);
            }

            $availableHours = $this->generateAvailableHours($selectedDate, $menuId);

            return response()->json([
                'status' => 'success',
                'hours' => $availableHours
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get available hours: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirm reservation
     */
    public function confirm(int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->confirmReservation($id);

            if (!$success) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reservation not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Reservation confirmed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to confirm reservation: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Cancel reservation
     */
    public function cancel(int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->cancelReservation($id);

            if (!$success) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reservation not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Reservation cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to cancel reservation: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Complete reservation
     */
    public function complete(int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->completeReservation($id);

            if (!$success) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reservation not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Reservation completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to complete reservation: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Bulk update reservation status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:reservations,id',
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $success = $this->reservationService->bulkUpdateReservationStatus(
                $request->ids,
                $request->status
            );

            if (!$success) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update reservations'
                ], 400);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Reservations updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to bulk update reservations: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Helper methods (same as in original controllers)
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
                'date' => $date->format('Y-m-d'),
                'day' => $date->day,
                'is_current_month' => $date->month === $currentMonth->month,
                'is_today' => $dateString === $todayString,
                'is_past_date' => $isPastDate,
                'has_available_hours' => $hasAvailableHours,
                'booking_status' => $bookingStatus,
                'blocked_hours' => $blockedHours[$dateString] ?? [],
                'reservation_count' => $reservationsByDate->get($dateString, collect())->count()
            ];
        }

        return $calendarDays;
    }

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

    private function generateAvailableHours(Carbon $selectedDate, int $menuId): array
    {
        $menu = $this->menuService->findMenu($menuId);
        $requiredTime = $menu->required_time;
        $dateString = $selectedDate->format('Y-m-d');
        $now = Carbon::now();

        $availableHours = [];

        $blockedHours = $this->blockedPeriodService->getBlockedHoursInRange(
            $selectedDate->format('Y-m-d H:i:s'),
            $selectedDate->format('Y-m-d H:i:s'),
            $menuId
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
            if ($isBlocked) {
                $status = 'blocked';
            } elseif ($hasReservation) {
                $status = 'reserved';
            }

            $availableHours[] = [
                'time' => $hour,
                'datetime' => $dateTime->format('Y-m-d H:i:s'),
                'status' => $status,
                'available' => $status === 'available'
            ];
        }

        return $availableHours;
    }

    private function getOperatingHours(): array
    {
        $hours = [];
        for ($i = 8; $i <= 20; $i++) {
            $hours[] = sprintf('%02d:00', $i);
        }
        return $hours;
    }

    /**
     * Generate optimized booking calendar - MUCH FASTER
     */
    private function generateOptimizedBookingCalendar(Carbon $currentMonth, int $menuId): array
    {
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();

        // Get menu once
        $menu = $this->menuService->findMenu($menuId);
        if (!$menu) {
            throw new \Exception('Menu not found');
        }

        // Get all reservations for the month in ONE query
        $reservations = $this->reservationService->getReservationsByDateRangeAndMenu(
            $startDate->format('Y-m-d') . ' 00:00:00',
            $endDate->format('Y-m-d') . ' 23:59:59',
            $menuId
        );

        // Group reservations by date for faster lookup
        $reservationsByDate = $reservations->groupBy(function ($reservation) {
            return $reservation->reservation_datetime->format('Y-m-d');
        });

        // Get blocked periods in ONE query
        $blockedPeriods = $this->blockedPeriodService->getByDateRange(
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        );

        // Pre-process blocked hours by date
        $blockedHoursByDate = $this->preprocessBlockedHours($blockedPeriods, $menuId, $startDate, $endDate);

        $calendarDays = $this->generateOptimizedCalendarDays(
            $currentMonth,
            $reservationsByDate,
            $blockedHoursByDate,
            $menu
        );

        return [
            'days' => $calendarDays,
            'current_month' => $currentMonth,
            'previous_month' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'next_month' => $currentMonth->copy()->addMonth()->format('Y-m')
        ];
    }

    /**
     * Pre-process blocked hours to avoid repeated calculations
     */
    private function preprocessBlockedHours($blockedPeriods, int $menuId, Carbon $startDate, Carbon $endDate): array
    {
        $blockedHoursByDate = [];

        foreach ($blockedPeriods as $period) {
            // Skip if this blocked period doesn't apply to our menu
            if (!$period->all_menus && $period->menu_id != $menuId) {
                continue;
            }

            $periodStart = Carbon::parse($period->start_datetime);
            $periodEnd = Carbon::parse($period->end_datetime);

            // Generate blocked hours for each day in the period
            $current = $periodStart->copy()->startOfDay();
            while ($current <= $periodEnd && $current <= $endDate) {
                if ($current >= $startDate) {
                    $dateStr = $current->format('Y-m-d');

                    if (!isset($blockedHoursByDate[$dateStr])) {
                        $blockedHoursByDate[$dateStr] = [];
                    }

                    // Add hours that are blocked on this date
                    $dayStart = max($periodStart, $current->copy()->setTime(8, 0, 0));
                    $dayEnd = min($periodEnd, $current->copy()->setTime(20, 59, 59));

                    if ($dayStart <= $dayEnd) {
                        $hourStart = $dayStart->hour;
                        $hourEnd = $dayEnd->hour;

                        for ($h = $hourStart; $h <= $hourEnd; $h++) {
                            $hourStr = sprintf('%02d:00', $h);
                            if (!in_array($hourStr, $blockedHoursByDate[$dateStr])) {
                                $blockedHoursByDate[$dateStr][] = $hourStr;
                            }
                        }
                    }
                }
                $current->addDay();
            }
        }

        return $blockedHoursByDate;
    }

    /**
     * Generate calendar days with optimized logic
     */
    private function generateOptimizedCalendarDays(
        Carbon $currentMonth,
        $reservationsByDate,
        array $blockedHoursByDate,
        $menu
    ): array {
        $calendarDays = [];
        $today = Carbon::now();
        $todayString = $today->format('Y-m-d');

        $startDate = $currentMonth->copy()->startOfMonth();
        $dayOfWeek = $startDate->dayOfWeek;

        if ($dayOfWeek !== 1) {
            $startDate->subDays($dayOfWeek === 0 ? 6 : $dayOfWeek - 1);
        }

        // Pre-generate operating hours
        $operatingHours = $this->getOperatingHours();
        $requiredTime = $menu->required_time;

        for ($i = 0; $i < 42; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');

            $isPastDate = $date->isBefore($today->startOfDay());

            // Fast availability check
            $hasAvailableHours = false;
            if (!$isPastDate) {
                $hasAvailableHours = $this->quickAvailabilityCheck(
                    $date,
                    $operatingHours,
                    $requiredTime,
                    $blockedHoursByDate[$dateString] ?? [],
                    $reservationsByDate->get($dateString, collect()),
                    $today
                );
            }

            $bookingStatus = $this->getDateBookingStatus($date, $isPastDate, $hasAvailableHours);

            $calendarDays[] = [
                'date' => $dateString,
                'day' => $date->day,
                'is_current_month' => $date->month === $currentMonth->month,
                'is_today' => $dateString === $todayString,
                'is_past_date' => $isPastDate,
                'has_available_hours' => $hasAvailableHours,
                'booking_status' => $bookingStatus,
                'blocked_hours' => $blockedHoursByDate[$dateString] ?? [],
                'reservation_count' => $reservationsByDate->get($dateString, collect())->count()
            ];
        }

        return $calendarDays;
    }

    /**
     * Quick availability check without heavy calculations
     */
    private function quickAvailabilityCheck(
        Carbon $date,
        array $operatingHours,
        int $requiredTime,
        array $blockedHours,
        $reservations,
        Carbon $now
    ): bool {
        $dateString = $date->format('Y-m-d');
        $closingTime = Carbon::parse($dateString . ' 21:00:00');

        // Create a quick lookup for reservation times
        $reservationTimes = $reservations->pluck('reservation_datetime')
            ->map(fn($dt) => $dt->format('H:i'))
            ->toArray();

        foreach ($operatingHours as $hour) {
            $dateTime = Carbon::parse($dateString . ' ' . $hour);

            // Skip past times
            if ($dateTime->isBefore($now)) {
                continue;
            }

            // Check if service would finish before closing
            $endTime = $dateTime->copy()->addMinutes($requiredTime);
            if ($endTime->gt($closingTime)) {
                continue;
            }

            // Quick blocked check
            if (in_array($hour, $blockedHours)) {
                continue;
            }

            // Quick reservation check
            if (!in_array($hour, $reservationTimes)) {
                return true; // Found available slot
            }
        }

        return false;
    }
}
