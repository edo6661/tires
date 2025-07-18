<?php
namespace App\Http\Controllers\Admin;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Models\Menu;
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
        
        // Perbaikan untuk date range - gunakan input HTML native
        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                
                $query->whereBetween('reservation_datetime', [$startDate, $endDate]);
            } catch (\Exception $e) {
                // Jika gagal parse, abaikan filter tanggal
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
                                ->orderBy('name')
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
            
            // Perbaikan untuk date range - gunakan input HTML native
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
    public function block(): View
    {
        return view('admin.reservation.block');
    }
    public function availability(): View
    {
        return view('admin.reservation.availability');
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
            $this->reservationService->createReservation($request->validated());
            return redirect()->route('admin.reservation.index')
                ->with('success', 'Reservasi berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat reservasi: ' . $e->getMessage());
        }
    }
    public function show(int $id): View
    {
        $reservation = $this->reservationService->findReservation($id);
        if (!$reservation) {
            abort(404, 'Reservasi tidak ditemukan');
        }
        return view('admin.reservation.show', compact('reservation'));
    }
    public function edit(int $id): View
    {
        $reservation = $this->reservationService->findReservation($id);
        if (!$reservation) {
            abort(404, 'Reservasi tidak ditemukan');
        }
        return view('admin.reservation.edit', compact('reservation'));
    }
    public function update(ReservationRequest $request, int $id): RedirectResponse
    {
        try {
            $reservation = $this->reservationService->updateReservation($id, $request->validated());
            if (!$reservation) {
                return redirect()->route('admin.reservation.index')
                    ->with('error', 'Reservasi tidak ditemukan');
            }
            return redirect()->route('admin.reservation.show', $id)
                ->with('success', 'Reservasi berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui reservasi: ' . $e->getMessage());
        }
    }
    public function destroy(int $id): RedirectResponse
    {
        try {
            $success = $this->reservationService->deleteReservation($id);
            if (!$success) {
                return redirect()->route('admin.reservation.calendar')
                    ->with('error', 'Reservasi tidak ditemukan');
            }
            return redirect()->route('admin.reservation.calendar')
                ->with('success', 'Reservasi berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus reservasi: ' . $e->getMessage());
        }
    }
    public function checkAvailability(Request $request): JsonResponse
    {
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
            'message' => $available ? 'Waktu tersedia' : 'Waktu tidak tersedia'
        ]);
    }
    public function confirm(int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->confirmReservation($id);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi tidak ditemukan'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dikonfirmasi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function cancel(int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->cancelReservation($id);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi tidak ditemukan'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function complete(int $id): JsonResponse
    {
        try {
            $success = $this->reservationService->completeReservation($id);
            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Reservasi tidak ditemukan'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Reservasi berhasil diselesaikan'
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
                    'message' => 'Gagal memperbarui status reservasi'
                ], 400);
            }
            return response()->json([
                'success' => true,
                'message' => 'Status reservasi berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}