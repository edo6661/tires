<?php

namespace App\Http\Controllers\Api\Customer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Events\BookingCompleted;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Services\AuthServiceInterface;
use App\Services\MenuServiceInterface;
use App\Http\Requests\ReservationRequest;
use App\Services\ContactServiceInterface;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ReservationResource;
use App\Services\ReservationServiceInterface;
use App\Services\BlockedPeriodServiceInterface;

/**
 * @tags Customer - Booking
 */
class BookingController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ReservationServiceInterface $reservationService,
        protected AuthServiceInterface $authService,
        protected MenuServiceInterface $menuService,
        protected BlockedPeriodServiceInterface $blockedPeriodService,
    ) {}

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
}
