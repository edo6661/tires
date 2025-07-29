<?php
namespace App\Http\Controllers\Customer;

use App\Events\BookingCompleted;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Services\ReservationServiceInterface;
use App\Services\BlockedPeriodService;
use App\Services\MenuService;
use App\Services\UserService;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
class BookingController extends Controller
{
    public function __construct(
        protected ReservationServiceInterface $reservationService,
        protected BlockedPeriodService $blockedPeriodService,
        protected MenuService $menuService,
        protected UserService $userService,
    ) {}
    public function firstStep($locale, $menuId): View
    {
        $menu = $this->menuService->findMenu($menuId);
        $currentMonth = Carbon::now()->startOfMonth();
        $calendarData = $this->generateBookingCalendar($currentMonth, $menu->id);
        return view('customer.booking.first-step', compact(
            'menu',
            'calendarData',
            'currentMonth'
        ));
    }
    public function getCalendarData(Request $request): JsonResponse
    {
        $monthParam = $request->get('month', Carbon::now()->format('Y-m'));
        $menuId = $request->get('menu_id');
        $currentMonth = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
        $calendarData = $this->generateBookingCalendar($currentMonth, $menuId);
        return response()->json([
            'success' => true,
            'data' => $calendarData,
            'currentMonth' => $currentMonth->format('F Y'),
            'previousMonth' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->copy()->addMonth()->format('Y-m')
        ]);
    }
    public function getAvailableHours(Request $request): JsonResponse
    {
        $date = $request->get('date');
        $menuId = $request->get('menu_id');
        if (!$date || !$menuId) {
            return response()->json([
                'success' => false,
                'message' => 'Date and menu_id are required'
            ], 400);
        }
        $selectedDate = Carbon::parse($date);
        $now = Carbon::now();
        if ($selectedDate->isBefore($now->startOfDay())) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot book for past dates',
                'hours' => []
            ]);
        }
        $availableHours = $this->generateAvailableHours($selectedDate, $menuId);
        return response()->json([
            'success' => true,
            'hours' => $availableHours
        ]);
    }
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
            'currentMonth' => $currentMonth,
            'previousMonth' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->copy()->addMonth()->format('Y-m')
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
                'date' => $date,
                'dateString' => $dateString,
                'day' => $date->day,
                'isCurrentMonth' => $date->month === $currentMonth->month,
                'isToday' => $dateString === $todayString,
                'isPastDate' => $isPastDate,
                'hasAvailableHours' => $hasAvailableHours,
                'bookingStatus' => $bookingStatus,
                'blockedHours' => $blockedHours[$dateString] ?? [],
                'reservations' => $reservationsByDate->get($dateString, collect()),
                'reservationCount' => $reservationsByDate->get($dateString, collect())->count()
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
                $indicator = __('first-step.indicator.blocked'); 
            } elseif ($hasReservation) {
                $status = 'reserved';
                $indicator = __('first-step.indicator.reserved');
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
    private function getOperatingHours(): array
    {
        $hours = [];
        for ($i = 8; $i <= 20; $i++) {
            $hours[] = sprintf('%02d:00', $i);
        }
        return $hours;
    }
    public function secondStep(): View
    {
        return view('customer.booking.second-step');
    }
    public function thirdStep(): View
    {
        return view('customer.booking.third-step');
    }
    public function finalStep(): View
    {
        return view('customer.booking.final-step');
    }
    public function createReservation(ReservationRequest $request): JsonResponse
    {
        try {
            $reservation = $this->reservationService->createReservation($request->validated());
            
            BookingCompleted::dispatch($reservation);
            
            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully',
                'reservation_number' => $reservation->reservation_number,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create reservation: ' . $e->getMessage()
            ], 500);    
        }
    }
    public function getMenuDetails($locale, $menuId): JsonResponse
    {
        try {
            $menu = $this->menuService->findMenu($menuId);
            return response()->json([
                'success' => true,
                'menu' => [
                    'name' => $menu->name,
                    'required_time' => $menu->required_time,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Menu not found.'
            ], 404);
        }
    }
}