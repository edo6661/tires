<?php

namespace App\Http\Controllers\Api\Admin;

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

/**
 * @tags Admin - Reservation Management
 */
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
     * Get all reservations with cursor pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Validate query parameters
            $request->validate([
                'per_page' => 'nullable|integer|min:1|max:100',
                'cursor' => 'nullable|string',
                'paginate' => 'sometimes|in:true,false',
            ]);

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
     * Get reservations for calendar view
     */
    public function getCalendarReservations(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'month' => 'nullable|date_format:Y-m',
                'view' => 'nullable|in:month,week,day',
                'menu_id' => 'nullable|integer|exists:menus,id',
                'status' => 'nullable|in:pending,confirmed,completed,cancelled'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors()->toArray());
            }

            $month = $request->get('month', Carbon::now()->format('Y-m'));
            $view = $request->get('view', 'month');
            $menuId = $request->get('menu_id');
            $status = $request->get('status');

            $currentMonth = Carbon::createFromFormat('Y-m', $month);

            // Determine date range based on view
            switch ($view) {
                case 'week':
                    $startDate = $currentMonth->copy()->startOfWeek();
                    $endDate = $currentMonth->copy()->endOfWeek();
                    break;
                case 'day':
                    $startDate = $currentMonth->copy()->startOfDay();
                    $endDate = $currentMonth->copy()->endOfDay();
                    break;
                default: // month
                    $startDate = $currentMonth->copy()->startOfMonth();
                    $endDate = $currentMonth->copy()->endOfMonth();
                    break;
            }

            // Get reservations for the date range
            $reservations = $this->reservationService->getReservationsByDateRange(
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s')
            );

            // Apply additional filters
            if ($menuId) {
                $reservations = $reservations->where('menu_id', $menuId);
            }
            if ($status) {
                $reservations = $reservations->where('status', $status);
            }

            // Group reservations by date for calendar display
            $calendarData = [];
            $current = $startDate->copy();

            while ($current <= $endDate) {
                $dateStr = $current->format('Y-m-d');
                $dayReservations = $reservations->filter(function ($reservation) use ($dateStr) {
                    return $reservation->reservation_datetime->format('Y-m-d') === $dateStr;
                });

                $calendarData[] = [
                    'date' => $dateStr,
                    'day' => $current->day,
                    'is_current_month' => $current->month === $currentMonth->month,
                    'is_today' => $current->isToday(),
                    'day_name' => $current->format('l'),
                    'reservations' => $dayReservations->map(function ($reservation) {
                        return [
                            'id' => $reservation->id,
                            'reservation_number' => $reservation->reservation_number,
                            'customer_name' => $reservation->user ? $reservation->user->full_name : $reservation->full_name,
                            'time' => $reservation->reservation_datetime->format('H:i'),
                            'end_time' => $reservation->reservation_datetime->copy()->addMinutes($reservation->menu->required_time ?? 60)->format('H:i'),
                            'menu_name' => $reservation->menu->name ?? 'Unknown Menu',
                            'menu_color' => $reservation->menu->color ?? '#3B82F6',
                            'status' => $reservation->status,
                            'people_count' => $reservation->number_of_people,
                            'amount' => $reservation->amount
                        ];
                    })->values(),
                    'total_reservations' => $dayReservations->count()
                ];

                $current->addDay();
            }

            return $this->successResponse([
                'view' => $view,
                'current_period' => [
                    'month' => $currentMonth->format('F Y'),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d')
                ],
                'navigation' => [
                    'previous_month' => $currentMonth->copy()->subMonth()->format('Y-m'),
                    'next_month' => $currentMonth->copy()->addMonth()->format('Y-m'),
                    'current_month' => $currentMonth->format('Y-m')
                ],
                'calendar_data' => $calendarData,
                'statistics' => [
                    'total_reservations' => $reservations->count(),
                    'pending' => $reservations->where('status', 'pending')->count(),
                    'confirmed' => $reservations->where('status', 'confirmed')->count(),
                    'completed' => $reservations->where('status', 'completed')->count(),
                    'cancelled' => $reservations->where('status', 'cancelled')->count()
                ]
            ], 'Calendar reservations retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve calendar reservations: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get reservations for list view with filtering and pagination
     */
    public function getListReservations(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'search' => 'nullable|string|max:255',
                'menu_id' => 'nullable|integer|exists:menus,id',
                'status' => 'nullable|in:pending,confirmed,completed,cancelled',
                'date_from' => 'nullable|date_format:Y-m-d',
                'date_to' => 'nullable|date_format:Y-m-d|after_or_equal:date_from',
                'per_page' => 'nullable|integer|min:5|max:100',
                'page' => 'nullable|integer|min:1',
                'sort_by' => 'nullable|in:reservation_datetime,created_at,customer_name,status',
                'sort_order' => 'nullable|in:asc,desc'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors()->toArray());
            }

            $search = $request->get('search');
            $menuId = $request->get('menu_id');
            $status = $request->get('status');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');
            $perPage = min($request->get('per_page', 15), 100);
            $sortBy = $request->get('sort_by', 'reservation_datetime');
            $sortOrder = $request->get('sort_order', 'desc');

            // Build query with filters
            $query = \App\Models\Reservation::with(['user', 'menu'])
                ->when($search, function ($q) use ($search) {
                    $q->where(function ($subQuery) use ($search) {
                        $subQuery->where('reservation_number', 'like', "%{$search}%")
                            ->orWhere('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%")
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('full_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone_number', 'like', "%{$search}%");
                            });
                    });
                })
                ->when($menuId, function ($q) use ($menuId) {
                    $q->where('menu_id', $menuId);
                })
                ->when($status, function ($q) use ($status) {
                    $q->where('status', $status);
                })
                ->when($dateFrom, function ($q) use ($dateFrom) {
                    $q->whereDate('reservation_datetime', '>=', $dateFrom);
                })
                ->when($dateTo, function ($q) use ($dateTo) {
                    $q->whereDate('reservation_datetime', '<=', $dateTo);
                });

            // Apply sorting
            if ($sortBy === 'customer_name') {
                $query->orderByRaw('COALESCE(full_name, (SELECT full_name FROM users WHERE users.id = reservations.user_id)) ' . $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            $reservations = $query->paginate($perPage);

            // Transform data for list view
            $transformedData = $reservations->getCollection()->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'reservation_number' => $reservation->reservation_number,
                    'customer' => [
                        'name' => $reservation->user ? $reservation->user->full_name : $reservation->full_name,
                        'email' => $reservation->user ? $reservation->user->email : $reservation->email,
                        'phone' => $reservation->user ? $reservation->user->phone_number : $reservation->phone_number,
                        'type' => $reservation->user ? 'registered' : 'guest'
                    ],
                    'date_time' => [
                        'date' => $reservation->reservation_datetime->format('M d, Y'),
                        'time' => $reservation->reservation_datetime->format('H:i'),
                        'datetime' => $reservation->reservation_datetime->format('Y-m-d H:i:s'),
                        'day_name' => $reservation->reservation_datetime->format('l')
                    ],
                    'menu' => [
                        'id' => $reservation->menu_id,
                        'name' => $reservation->menu->name ?? 'Unknown Menu',
                        'required_time' => $reservation->menu->required_time ?? 60,
                        'color' => $reservation->menu->color ?? '#3B82F6'
                    ],
                    'people_count' => $reservation->number_of_people,
                    'amount' => $reservation->amount,
                    'status' => $reservation->status,
                    'notes' => $reservation->notes,
                    'created_at' => $reservation->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $reservation->updated_at->format('Y-m-d H:i:s')
                ];
            });

            // Get filter options
            $filterOptions = [
                'menus' => $this->menuService->getActiveMenus()->map(function ($menu) {
                    return [
                        'id' => $menu->id,
                        'name' => $menu->name
                    ];
                }),
                'statuses' => [
                    ['value' => 'pending', 'label' => 'Pending'],
                    ['value' => 'confirmed', 'label' => 'Confirmed'],
                    ['value' => 'completed', 'label' => 'Completed'],
                    ['value' => 'cancelled', 'label' => 'Cancelled']
                ]
            ];

            return $this->successResponse([
                'reservations' => $transformedData,
                'pagination' => [
                    'current_page' => $reservations->currentPage(),
                    'per_page' => $reservations->perPage(),
                    'total' => $reservations->total(),
                    'last_page' => $reservations->lastPage(),
                    'from' => $reservations->firstItem(),
                    'to' => $reservations->lastItem(),
                    'has_more_pages' => $reservations->hasMorePages()
                ],
                'filters' => [
                    'current' => [
                        'search' => $search,
                        'menu_id' => $menuId,
                        'status' => $status,
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                        'sort_by' => $sortBy,
                        'sort_order' => $sortOrder
                    ],
                    'options' => $filterOptions
                ],
                'statistics' => [
                    'total_results' => $reservations->total(),
                    'showing' => $reservations->count(),
                    'from' => $reservations->firstItem() ?? 0,
                    'to' => $reservations->lastItem() ?? 0
                ]
            ], 'List reservations retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve list reservations: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get comprehensive availability check for admin interface
     */
    public function getAvailabilityCheck(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'date' => 'required|date_format:Y-m-d',
                'menu_id' => 'nullable|integer|exists:menus,id'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors()->toArray());
            }

            $date = $request->get('date');
            $menuId = $request->get('menu_id');
            $selectedDate = Carbon::parse($date);
            $now = Carbon::now();

            // Get all menus if no specific menu selected
            if (!$menuId) {
                $menus = $this->menuService->getAllMenus();
                return $this->successResponse([
                    'date' => $date,
                    'date_formatted' => $selectedDate->format('F j, Y'),
                    'day_name' => $selectedDate->format('l'),
                    'current_time' => $now->format('H:i'),
                    'menu_required' => true,
                    'available_menus' => $menus->where('is_active', true)->values()
                ], 'Please select a menu to check availability');
            }

            // Get menu details
            $menu = $this->menuService->findMenu($menuId);
            if (!$menu || !$menu->is_active) {
                return $this->errorResponse('Menu not found or not active', 404);
            }

            // Generate time slots for the selected date
            $timeSlots = $this->generateTimeAvailabilitySlots($selectedDate, $menuId, $now);
            $availableCount = collect($timeSlots)->where('status', 'available')->count();

            return $this->successResponse([
                'date' => $date,
                'date_formatted' => $selectedDate->format('F j, Y'),
                'day_name' => $selectedDate->format('l'),
                'current_time' => $now->format('H:i'),
                'menu' => [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'required_time' => $menu->required_time,
                    'description' => $menu->description
                ],
                'available_slots' => $availableCount,
                'time_slots' => $timeSlots,
                'statistics' => [
                    'total_slots' => count($timeSlots),
                    'available_slots' => $availableCount,
                    'reserved_slots' => collect($timeSlots)->where('status', 'reserved')->count(),
                    'blocked_slots' => collect($timeSlots)->where('status', 'blocked')->count()
                ]
            ], 'Availability check completed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to check availability: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Get reservation statistics for dashboard
     */
    public function getReservationStatistics(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'period' => 'nullable|in:today,week,month,year',
                'date_from' => 'nullable|date_format:Y-m-d',
                'date_to' => 'nullable|date_format:Y-m-d|after_or_equal:date_from'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors()->toArray());
            }

            $period = $request->get('period', 'month');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            // Determine date range
            if ($dateFrom && $dateTo) {
                $startDate = Carbon::parse($dateFrom)->startOfDay();
                $endDate = Carbon::parse($dateTo)->endOfDay();
            } else {
                switch ($period) {
                    case 'today':
                        $startDate = Carbon::today();
                        $endDate = Carbon::today()->endOfDay();
                        break;
                    case 'week':
                        $startDate = Carbon::now()->startOfWeek();
                        $endDate = Carbon::now()->endOfWeek();
                        break;
                    case 'year':
                        $startDate = Carbon::now()->startOfYear();
                        $endDate = Carbon::now()->endOfYear();
                        break;
                    default: // month
                        $startDate = Carbon::now()->startOfMonth();
                        $endDate = Carbon::now()->endOfMonth();
                        break;
                }
            }

            $reservations = $this->reservationService->getReservationsByDateRange(
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s')
            );

            $statistics = [
                'total_reservations' => $reservations->count(),
                'by_status' => [
                    'pending' => $reservations->where('status', 'pending')->count(),
                    'confirmed' => $reservations->where('status', 'confirmed')->count(),
                    'completed' => $reservations->where('status', 'completed')->count(),
                    'cancelled' => $reservations->where('status', 'cancelled')->count()
                ],
                'total_revenue' => $reservations->where('status', '!=', 'cancelled')->sum('amount'),
                'average_amount' => $reservations->where('status', '!=', 'cancelled')->avg('amount') ?? 0,
                'total_customers' => $reservations->count('user_id') + $reservations->whereNull('user_id')->count(),
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                    'period_type' => $period
                ]
            ];

            return $this->successResponse($statistics, 'Reservation statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to retrieve reservation statistics: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Generate detailed time availability slots
     */
    private function generateTimeAvailabilitySlots(Carbon $selectedDate, int $menuId, Carbon $now): array
    {
        $dateString = $selectedDate->format('Y-m-d');
        $timeSlots = [];
        $operatingHours = $this->getOperatingHours();

        // Get menu details for required time
        $menu = $this->menuService->findMenu($menuId);
        $requiredTime = $menu->required_time;

        // Get blocked periods for this date and menu
        $blockedPeriods = $this->blockedPeriodService->getByDateRange($dateString, $dateString);
        $blockedHours = $this->getBlockedHoursForDate($blockedPeriods, $menuId, $selectedDate);

        // Get existing reservations for this date and menu
        $reservations = $this->reservationService->getReservationsByDateRangeAndMenu(
            $dateString . ' 00:00:00',
            $dateString . ' 23:59:59',
            $menuId
        );

        $reservationTimes = $reservations->pluck('reservation_datetime')
            ->map(fn($dt) => $dt->format('H:i'))
            ->toArray();

        $closingTime = Carbon::parse($dateString . ' 21:00:00');

        foreach ($operatingHours as $hour) {
            $dateTime = Carbon::parse($dateString . ' ' . $hour);
            $endTime = $dateTime->copy()->addMinutes($requiredTime);

            // Determine slot status
            $status = 'available';
            $reason = null;

            // Check if in the past
            if ($dateTime->isBefore($now)) {
                $status = 'past';
                $reason = 'Past time';
            }
            // Check if service would finish after closing
            elseif ($endTime->gt($closingTime)) {
                $status = 'blocked';
                $reason = 'Service would finish after closing time';
            }
            // Check blocked periods
            elseif (in_array($hour, $blockedHours)) {
                $status = 'blocked';
                $reason = 'Time blocked by administrator';
            }
            // Check existing reservations
            elseif (in_array($hour, $reservationTimes)) {
                $status = 'reserved';
                $reason = 'Already has reservation';
            }

            $timeSlots[] = [
                'time' => $hour,
                'datetime' => $dateTime->format('Y-m-d H:i:s'),
                'status' => $status,
                'available' => $status === 'available',
                'reason' => $reason,
                'service_end_time' => $endTime->format('H:i')
            ];
        }

        return $timeSlots;
    }

    /**
     * Get blocked hours for specific date and menu
     */
    private function getBlockedHoursForDate($blockedPeriods, int $menuId, Carbon $date): array
    {
        $blockedHours = [];
        $dateString = $date->format('Y-m-d');

        foreach ($blockedPeriods as $period) {
            // Skip if this blocked period doesn't apply to our menu
            if (!$period->all_menus && $period->menu_id != $menuId) {
                continue;
            }

            $periodStart = Carbon::parse($period->start_datetime);
            $periodEnd = Carbon::parse($period->end_datetime);

            // Check if the period affects this date
            $dayStart = max($periodStart, $date->copy()->setTime(8, 0, 0));
            $dayEnd = min($periodEnd, $date->copy()->setTime(20, 59, 59));

            if ($dayStart <= $dayEnd) {
                $hourStart = $dayStart->hour;
                $hourEnd = $dayEnd->hour;

                for ($h = $hourStart; $h <= $hourEnd; $h++) {
                    $hourStr = sprintf('%02d:00', $h);
                    if (!in_array($hourStr, $blockedHours)) {
                        $blockedHours[] = $hourStr;
                    }
                }
            }
        }

        return $blockedHours;
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
