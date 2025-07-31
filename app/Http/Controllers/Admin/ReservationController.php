<?php
namespace App\Http\Controllers\Admin;

use App\Events\BookingCompleted;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Models\BlockedPeriod;
use App\Models\Menu;
use App\Models\Reservation;
use App\Services\BlockedPeriodService;
use App\Services\MenuService;
use App\Services\ReservationServiceInterface;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
class ReservationController extends Controller
{
    public function __construct(
        protected ReservationServiceInterface $reservationService,
        protected BlockedPeriodService $blockedPeriodService,
        protected MenuService $menuService,
        protected UserService $userService,
    ){}
    public function calendar(Request $request): View
    {
        $view = $request->get('view', 'month'); 
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        $tab = $request->get('tab', 'calendar');
        if ($tab === 'list') {
            return $this->listView($request);
        }
        switch ($view) {
            case 'week':
                return $this->weekView($date);
            case 'day':
                return $this->dayView($date);
            default:
                return $this->monthView($request);
        }
    }
    private function listView(Request $request): View
    {
        $query = $this->reservationService->getAllReservations()->toQuery();
        if ($request->filled('menu_id')) {
            $query->where('menu_id', $request->menu_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('reservation_datetime', [$startDate, $endDate]);
            } catch (\Exception $e) {
                Log::error('Error parsing date range: ' . $e->getMessage());
            }
        }
         if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reservation_number', 'like', "%{$search}%")
                ->orWhere('full_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone_number', 'like', "%{$search}%")
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('full_name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%");
                });
            });
        }
        $reservations = $query->with(['user', 'menu'])
                            ->orderBy('reservation_datetime', 'desc')
                            ->paginate(15);
        $menus = Menu::where('is_active', true)
                                ->get();
        $statuses = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed', 
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];
        return view('admin.reservation.calendar', compact(
            'reservations',
            'menus',
            'statuses'
        ));
    }
    public function getFilteredReservations(Request $request): JsonResponse
    {
        try {
            $query = $this->reservationService->getAllReservations()->toQuery();
            if ($request->filled('menu_id')) {
                $query->where('menu_id', $request->menu_id);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('start_date') && $request->filled('end_date')) {
                try {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                    $query->whereBetween('reservation_datetime', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Format tanggal tidak valid'
                    ], 400);
                }
            }
             if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('reservation_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function($userQuery) use ($search) {
                        $userQuery->where('full_name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhere('phone_number', 'like', "%{$search}%");
                    });
                });
            }
            $reservations = $query->with(['user', 'menu'])
                                ->orderBy('reservation_datetime', 'desc')
                                ->paginate(15);
            return response()->json([
                'success' => true,
                'data' => $reservations->items(),
                'pagination' => [
                    'current_page' => $reservations->currentPage(),
                    'last_page' => $reservations->lastPage(),
                    'per_page' => $reservations->perPage(),
                    'total' => $reservations->total(),
                    'from' => $reservations->firstItem(),
                    'to' => $reservations->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data reservasi: ' . $e->getMessage()
            ], 500);
        }
    }
    private function monthView(Request $request): View
    {
        $monthParam = $request->get('month', Carbon::now()->format('Y-m'));
        $currentMonth = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
        $previousMonth = $currentMonth->copy()->subMonth()->format('Y-m');
        $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();
        $reservations = $this->reservationService->getReservationsByDateRange(
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s')
        );
        $reservationsByDate = $reservations->groupBy(function ($reservation) {
            return $reservation->reservation_datetime->format('Y-m-d');
        });
        $blockedPeriods = $this->blockedPeriodService->getByDateRange(
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s')
        );
        $blockedDates = $this->blockedPeriodService->getBlockedDatesInRange(
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s')
        );
        $blockedHours = $this->blockedPeriodService->getBlockedHoursInRange(
            $startDate->format('Y-m-d H:i:s'),
            $endDate->format('Y-m-d H:i:s')
        );
        $calendarDays = $this->generateCalendarDays($currentMonth, $reservationsByDate, $blockedDates, $blockedHours);
        $stats = [
            'pending' => $reservations->where('status', 'pending')->count(),
            'confirmed' => $reservations->where('status', 'confirmed')->count(),
            'completed' => $reservations->where('status', 'completed')->count(),
            'cancelled' => $reservations->where('status', 'cancelled')->count(),
        ];
        return view('admin.reservation.calendar', compact(
            'currentMonth',
            'previousMonth',
            'nextMonth',
            'calendarDays',
            'stats'
        ))->with('view', 'month');
    }
    private function weekView(string $date): View
    {
        $currentDate = Carbon::parse($date);
        $startOfWeek = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);
        $previousWeek = $startOfWeek->copy()->subWeek()->format('Y-m-d');
        $nextWeek = $startOfWeek->copy()->addWeek()->format('Y-m-d');
        $reservations = $this->reservationService->getReservationsByDateRange(
            $startOfWeek->format('Y-m-d H:i:s'),
            $endOfWeek->format('Y-m-d H:i:s')
        );
        $reservationsByDate = $reservations->groupBy(function ($reservation) {
            return $reservation->reservation_datetime->format('Y-m-d');
        });
        $blockedPeriods = $this->blockedPeriodService->getByDateRange(
    $startOfWeek->format('Y-m-d H:i:s'),
    $endOfWeek->format('Y-m-d H:i:s')
        );
        $blockedDates = $this->blockedPeriodService->getBlockedDatesInRange(
            $startOfWeek->format('Y-m-d H:i:s'),
            $endOfWeek->format('Y-m-d H:i:s')
        );
        $blockedHours = $this->blockedPeriodService->getBlockedHoursInRange(
            $startOfWeek->format('Y-m-d H:i:s'),
            $endOfWeek->format('Y-m-d H:i:s')
        );
        $weekDays = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);
            $dateString = $day->format('Y-m-d');
            $weekDays[] = [
                'date' => $day,
                'isToday' => $dateString === Carbon::now()->format('Y-m-d'),
                'isBlocked' => isset($blockedDates[$dateString]), 
                'blockedPeriods' => $blockedDates[$dateString] ?? [], 
                'blockedHours' => $blockedHours[$dateString] ?? [], 
                'reservations' => $reservationsByDate->get($dateString, collect())
            ];
        }
        $stats = [
            'pending' => $reservations->where('status', 'pending')->count(),
            'confirmed' => $reservations->where('status', 'confirmed')->count(),
            'completed' => $reservations->where('status', 'completed')->count(),
            'cancelled' => $reservations->where('status', 'cancelled')->count(),
        ];
        return view('admin.reservation.calendar', compact(
            'weekDays',
            'startOfWeek',
            'endOfWeek',
            'previousWeek',
            'nextWeek',
            'stats'
        ))->with('view', 'week');
    }
    private function dayView(string $date): View
    {
        $currentDate = Carbon::parse($date);
        $previousDay = $currentDate->copy()->subDay()->format('Y-m-d');
        $nextDay = $currentDate->copy()->addDay()->format('Y-m-d');
        $startOfDay = $currentDate->copy()->startOfDay();
        $endOfDay = $currentDate->copy()->endOfDay();
        $reservations = $this->reservationService->getReservationsByDateRange(
            $startOfDay->format('Y-m-d H:i:s'),
            $endOfDay->format('Y-m-d H:i:s')
        );
        $reservationsByHour = $reservations->groupBy(function ($reservation) {
            return $reservation->reservation_datetime->format('H:00');
        });
        $blockedPeriods = $this->blockedPeriodService->getBlockedPeriodsByDate($currentDate->format('Y-m-d'));
        $blockedHours = [];
        foreach ($blockedPeriods as $period) {
            $hours = $period->getBlockedHours();
            foreach ($hours as $hour) {
                if ($hour['date'] === $currentDate->format('Y-m-d')) {
                    $blockedHours[] = $hour['hour'];
                }
            }
        }
        $hourlySlots = [];
        for ($hour = 7; $hour <= 22; $hour++) {
            $hourKey = sprintf('%02d:00', $hour);
            $hourlySlots[$hourKey] = [
                'hour' => $hourKey,
                'reservations' => $reservationsByHour->get($hourKey, collect())
            ];
        }
        $stats = [
            'pending' => $reservations->where('status', 'pending')->count(),
            'confirmed' => $reservations->where('status', 'confirmed')->count(),
            'completed' => $reservations->where('status', 'completed')->count(),
            'cancelled' => $reservations->where('status', 'cancelled')->count(),
        ];
        return view('admin.reservation.calendar', compact(
  'currentDate',
 'previousDay',
            'nextDay',
            'hourlySlots',
            'reservations',
            'stats',
            'blockedPeriods',  
            'blockedHours'     
        ))->with('view', 'day');
    }
    private function generateCalendarDays(Carbon $currentMonth, $reservationsByDate, $blockedDates = null, $blockedHours = null): array
    {
        $calendarDays = [];
        $today = Carbon::now()->format('Y-m-d');
        $startDate = $currentMonth->copy()->startOfMonth();
        $dayOfWeek = $startDate->dayOfWeek;
        if ($dayOfWeek !== 1) { 
            $startDate->subDays($dayOfWeek === 0 ? 6 : $dayOfWeek - 1);
        }
        for ($i = 0; $i < 42; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateString = $date->format('Y-m-d');
            $calendarDays[] = [
                'date' => $date,
                'isCurrentMonth' => $date->month === $currentMonth->month,
                'isToday' => $dateString === $today,
                'isBlocked' => $blockedDates && isset($blockedDates[$dateString]),
                'blockedHours' => $blockedHours[$dateString] ?? [],
                'blockedPeriods' => $blockedDates[$dateString] ?? [],
                'reservations' => $reservationsByDate->get($dateString, collect())
            ];
        }
        return $calendarDays;
    }
    /**
     * API endpoint untuk mendapatkan detail reservasi (untuk hover tooltip)
     */
    public function getReservationDetails(int $id): JsonResponse
    {
        $reservation = $this->reservationService->findReservation($id);
        if (!$reservation) {
            return response()->json(['error' => 'Reservasi tidak ditemukan'], 404);
        }
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $reservation->id,
                'reservation_number' => $reservation->reservation_number,
                'user_name' => $reservation->getFullName(),
                'user_email' => $reservation->getEmail(),
                'user_phone' => $reservation->getPhoneNumber(),
                'menu_name' => $reservation->menu->name,
                'menu_description' => $reservation->menu->description,
                'reservation_datetime' => $reservation->reservation_datetime->format('d/m/Y H:i'),
                'number_of_people' => $reservation->number_of_people,
                'amount' => number_format($reservation->amount, 0, ',', '.'),
                'status' => $reservation->status,
                'notes' => $reservation->notes ?? 'Tidak ada catatan',
                'is_guest' => $reservation->isGuestReservation(),
                'created_at' => $reservation->created_at->format('d/m/Y H:i'),
                'updated_at' => $reservation->updated_at->format('d/m/Y H:i'),
            ]
        ]);
    }
    /**
     * API endpoint untuk mendapatkan reservasi berdasarkan tanggal (untuk AJAX loading)
     */
    public function getReservationsByDate(Request $request): JsonResponse
    {
        $date = $request->get('date');
        $view = $request->get('view', 'month');
        if (!$date) {
            return response()->json(['error' => 'Tanggal harus diisi'], 400);
        }
        try {
            $currentDate = Carbon::parse($date);
            switch ($view) {
                case 'week':
                    $startDate = $currentDate->copy()->startOfWeek(Carbon::MONDAY);
                    $endDate = $currentDate->copy()->endOfWeek(Carbon::SUNDAY);
                    break;
                case 'day':
                    $startDate = $currentDate->copy()->startOfDay();
                    $endDate = $currentDate->copy()->endOfDay();
                    break;
                default:
                    $startDate = $currentDate->copy()->startOfMonth();
                    $endDate = $currentDate->copy()->endOfMonth();
                    break;
            }
            $reservations = $this->reservationService->getReservationsByDateRange(
                $startDate->format('Y-m-d H:i:s'),
                $endDate->format('Y-m-d H:i:s')
            );
            $reservationsData = $reservations->map(function ($reservation) {
                return [
                    'id' => $reservation->id,
                    'reservation_number' => $reservation->reservation_number,
                    'user_name' => $reservation->user->name,
                    'menu_name' => $reservation->menu->name,
                    'reservation_datetime' => $reservation->reservation_datetime->format('Y-m-d H:i:s'),
                    'number_of_people' => $reservation->number_of_people,
                    'amount' => $reservation->amount,
                    'status' => $reservation->status->value,
                    'notes' => $reservation->notes,
                ];
            });
            return response()->json([
                'success' => true,
                'data' => $reservationsData,
                'count' => $reservations->count(),
                'period' => [
                    'start' => $startDate->format('Y-m-d H:i:s'),
                    'end' => $endDate->format('Y-m-d H:i:s'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Format tanggal tidak valid'], 400);
        }
    }
    public function availability(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'menu_id' => 'nullable|integer|exists:menus,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);
            $menuId = $request->menu_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $availabilityData = [];
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
            $blockedPeriods = $this->blockedPeriodService->getByDateRange($startDate, $endDate);
            $existingReservations = [];
            if ($menuId) {
                $existingReservations = $this->reservationService->getReservationsByDateRangeAndMenu(
                    $startDate, 
                    $endDate, 
                    $menuId
                );
            }
            $current = $start->copy();
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                $isFullyBlocked = false;
                $availableHours = [];
                foreach ($blockedPeriods as $period) {
                    if ($period->all_menus || ($menuId && $period->menu_id == $menuId)) {
                        $startDateTime = Carbon::parse($period->start_datetime);
                        $endDateTime = Carbon::parse($period->end_datetime);
                        if ($current->between($startDateTime, $endDateTime) && 
                            $startDateTime->format('H:i') == '00:00' && 
                            $endDateTime->format('H:i') == '23:59') {
                            $isFullyBlocked = true;
                            break;
                        }
                    }
                }
                if (!$isFullyBlocked) {
                    for ($hour = 8; $hour <= 20; $hour++) {
                        $hourStr = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00';
                        $isHourAvailable = true;
                        $blockedBy = null;
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
                }
                $hasBlockedPeriods = false;
                $hasReservations = false;
                foreach ($availableHours as $hour) {
                    if ($hour['blocked_by'] === 'blocked_period') {
                        $hasBlockedPeriods = true;
                    }
                    if ($hour['blocked_by'] === 'existing_reservation') {
                        $hasReservations = true;
                    }
                }
                $availabilityData[] = [
                    'date' => $dateStr,
                    'is_blocked' => $isFullyBlocked,
                    'available_hours' => $availableHours,
                    'has_blocked_periods' => $hasBlockedPeriods,
                    'has_reservations' => $hasReservations,
                    'is_mixed' => $hasBlockedPeriods && $hasReservations
                ];
                $current->addDay();
            }
            return response()->json([
                'success' => true,
                'data' => $availabilityData
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting availability data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengambil data ketersediaan',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    public function viewAvailability() {
        $menus = $this->menuService->getActiveMenus();
        return view('admin.reservation.availability', compact('menus'));
    }
    public function create(): View
    {
        $menus = $this->menuService->getActiveMenus();
        $users = $this->userService->getCustomers();
        return view('admin.reservation.create', compact('menus', 'users'));
    }
    public function store(ReservationRequest $request): RedirectResponse
    {
        try {
            $reservation = $this->reservationService->createReservation($request->validated());
            
            BookingCompleted::dispatch($reservation);
            return redirect()->route('admin.reservation.calendar')
                ->with('success', __('admin/reservation/create.notifications.creation_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('admin/reservation/create.notifications.creation_failed', ['error' => $e->getMessage()]));
        }
    }
     public function show($locale, int $id): View
    {
        $reservation = $this->reservationService->findReservation($id);
        if (!$reservation) {
            abort(404, __('admin/reservation/general.notifications.reservation_not_found'));
        }
        return view('admin.reservation.show', compact('reservation'));
    }
    public function edit($locale, int $id): View
    {
        $reservation = $this->reservationService->findReservation($id);
        $menus = $this->menuService->getActiveMenus();
        $users = $this->userService->getCustomers();
        if (!$reservation) {
            abort(404, __('admin/reservation/general.notifications.reservation_not_found'));
        }
        return view('admin.reservation.edit', compact('reservation', 'menus', 'users'));
    }
    public function update(ReservationRequest $request, $locale, int $id): RedirectResponse
    {
        try {
            $reservation = $this->reservationService->updateReservation($id, $request->validated());
            if (!$reservation) {
                return redirect()->route('admin.reservation.calendar')
                    ->with('error', __('admin/reservation/general.notifications.reservation_not_found'));
            }
            return redirect()->route('admin.reservation.show', ['locale' => $locale, 'id' => $id])
                ->with('success', __('admin/reservation/general.notifications.update_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', __('admin/reservation/general.notifications.update_failed', ['error' => $e->getMessage()]));
        }
    }
    public function destroy($locale, int $id): RedirectResponse
    {
        try {
            $success = $this->reservationService->deleteReservation($id);
            if (!$success) {
                return redirect()->route('admin.reservation.calendar')
                    ->with('error', __('admin/reservation/general.notifications.reservation_not_found'));
            }
            return redirect()->route('admin.reservation.calendar')
                ->with('success', __('admin/reservation/general.notifications.delete_success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('admin/reservation/general.notifications.delete_failed', ['error' => $e->getMessage()]));
        }
    }
    public function checkAvailability(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'menu_id' => 'required|integer|exists:menus,id',
                'reservation_datetime' => 'required|date|after:now',
                'exclude_reservation_id' => 'nullable|integer|exists:reservations,id'
            ]);
            $available = $this->reservationService->checkAvailability(
                $request->menu_id,
                $request->reservation_datetime,
                $request->exclude_reservation_id
            );
            return response()->json([
                'available' => $available,
                'message' => $available ? __('admin/reservation/general.availability.available') : __('admin/reservation/general.availability.unavailable')
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking availability: ' . $e->getMessage());
            return response()->json([
                'error' => __('admin/reservation/general.notifications.availability_check_error'),
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function confirm($locale, int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->confirmReservation($id);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/reservation/general.notifications.reservation_not_found')
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/reservation/general.notifications.confirm_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function cancel($locale, int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->cancelReservation($id);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/reservation/general.notifications.reservation_not_found')
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/reservation/general.notifications.cancel_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function complete($locale, int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->completeReservation($id);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/reservation/general.notifications.reservation_not_found')
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/reservation/general.notifications.complete_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:reservations,id',
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);
        try {
            $success = $this->reservationService->bulkUpdateReservationStatus(
                $request->ids,
                $request->status
            );
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => __('admin/reservation/general.notifications.bulk_update_failed')
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message' => __('admin/reservation/general.notifications.bulk_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function getAvailabilityData(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'menu_id' => 'nullable|integer|exists:menus,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);
            $menuId = $request->menu_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $availabilityData = [];
            $currentDate = Carbon::parse($startDate);
            $endDateCarbon = Carbon::parse($endDate);
            while ($currentDate <= $endDateCarbon) {
                $dateStr = $currentDate->format('Y-m-d');
                $availableHours = [];
                for ($hour = 8; $hour <= 20; $hour++) {
                    $availableHours[] = [
                        'hour' => sprintf('%02d:00', $hour),
                        'available' => true
                    ];
                }
                $availabilityData[$dateStr] = [
                    'date' => $dateStr,
                    'is_blocked' => false,
                    'available_hours' => $availableHours
                ];
                $currentDate->addDay();
            }
            $blockedPeriods = BlockedPeriod::where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_datetime', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->orWhereBetween('end_datetime', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->orWhere(function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->where('start_datetime', '<=', Carbon::parse($startDate)->startOfDay())
                            ->where('end_datetime', '>=', Carbon::parse($endDate)->endOfDay());
                });
            })
            ->where(function ($query) use ($menuId) {
                $query->where('all_menus', true)
                    ->orWhere('menu_id', $menuId);
            })
            ->get();
            foreach ($blockedPeriods as $period) {
                $startDateTime = Carbon::parse($period->start_datetime);
                $endDateTime = Carbon::parse($period->end_datetime);
                if ($period->all_menus || ($menuId && $period->menu_id == $menuId)) {
                    $current = $startDateTime->copy();
                    while ($current <= $endDateTime) {
                        $dateStr = $current->format('Y-m-d');
                        if (!isset($availabilityData[$dateStr])) {
                            $current->addHour();
                            continue;
                        }
                        if ($startDateTime->format('H:i') == '00:00' && $endDateTime->format('H:i') == '23:59') {
                            $availabilityData[$dateStr]['is_blocked'] = true;
                            foreach ($availabilityData[$dateStr]['available_hours'] as &$hourInfo) {
                                $hourInfo['available'] = false;
                            }
                            $current->addDay()->startOfDay();
                        } else {
                            $hour = (int)$current->format('H');
                            foreach ($availabilityData[$dateStr]['available_hours'] as &$hourInfo) {
                                $hourInfoHour = (int)explode(':', $hourInfo['hour'])[0];
                                if ($hourInfoHour == $hour) {
                                    $hourInfo['available'] = false;
                                    break;
                                }
                            }
                            $current->addHour();
                        }
                    }
                }
            }
            if ($menuId) {
                $existingReservations = Reservation::where('menu_id', $menuId)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->whereBetween('reservation_datetime', [
                        Carbon::parse($startDate)->startOfDay(),
                        Carbon::parse($endDate)->endOfDay()
                    ])
                    ->get();
                foreach ($existingReservations as $reservation) {
                    $reservationDateTime = Carbon::parse($reservation->reservation_datetime);
                    $dateStr = $reservationDateTime->format('Y-m-d');
                    $hour = (int)$reservationDateTime->format('H');
                    if (isset($availabilityData[$dateStr])) {
                        foreach ($availabilityData[$dateStr]['available_hours'] as &$hourInfo) {
                            $hourInfoHour = (int)explode(':', $hourInfo['hour'])[0];
                            if ($hourInfoHour == $hour) {
                                $hourInfo['available'] = false;
                                break;
                            }
                        }
                    }
                }
            }
            foreach ($availabilityData as &$dateInfo) {
                $availableCount = 0;
                foreach ($dateInfo['available_hours'] as $hourInfo) {
                    if ($hourInfo['available']) {
                        $availableCount++;
                    }
                }
                if ($availableCount == 0) {
                    $dateInfo['is_blocked'] = true;
                }
            }
            $responseData = array_values($availabilityData);
            return response()->json([
                'success' => true,
                'data' => $responseData,
                'message' => 'Availability data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting availability data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan saat mengambil data ketersediaan',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    private function checkTimeSlotAvailability($menuId, $datetime, $requiredTime, $blockedPeriods, $existingReservations): bool
    {
        $startTime = Carbon::parse($datetime);
        $endTime = $startTime->copy()->addMinutes($requiredTime);
        foreach ($blockedPeriods as $dateKey => $periods) {
            foreach ($periods as $period) {
                if ($period->all_menus || $period->menu_id == $menuId) {
                    $blockStart = Carbon::parse($period->start_datetime);
                    $blockEnd = Carbon::parse($period->end_datetime);
                    if ($startTime < $blockEnd && $endTime > $blockStart) {
                        return false;
                    }
                }
            }
        }
        foreach ($existingReservations as $reservation) {
            $reservationStart = Carbon::parse($reservation->reservation_datetime);
            $reservationEnd = $reservationStart->copy()->addMinutes($reservation->menu->required_time);
            if ($startTime < $reservationEnd && $endTime > $reservationStart) {
                return false;
            }
        }
        return true;
    }
    private function isDateFullyBlocked(string $date, int $menuId, array $blockedPeriods): bool
    {
        $dayStart = Carbon::parse($date . ' 08:00:00');
        $dayEnd = Carbon::parse($date . ' 20:00:00');
        foreach ($blockedPeriods as $dateKey => $periods) {
            foreach ($periods as $period) {
                if ($period->all_menus || $period->menu_id == $menuId) {
                    $blockStart = Carbon::parse($period->start_datetime);
                    $blockEnd = Carbon::parse($period->end_datetime);
                    if ($blockStart->lte($dayStart) && $blockEnd->gte($dayEnd)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}