<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Services\ReservationServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationServiceInterface $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    public function index(Request $request): View
    {
        $reservations = $this->reservationService->getPaginatedReservations(15);
        
        return view('admin.reservations.index', compact('reservations'));
    }

    public function create(): View
    {
        return view('admin.reservations.create');
    }

    public function store(ReservationRequest $request): RedirectResponse
    {
        try {
            $this->reservationService->createReservation($request->validated());
            
            return redirect()->route('admin.reservations.index')
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

        return view('admin.reservations.show', compact('reservation'));
    }

    public function edit(int $id): View
    {
        $reservation = $this->reservationService->findReservation($id);
        
        if (!$reservation) {
            abort(404, 'Reservasi tidak ditemukan');
        }

        return view('admin.reservations.edit', compact('reservation'));
    }

    public function update(ReservationRequest $request, int $id): RedirectResponse
    {
        try {
            $reservation = $this->reservationService->updateReservation($id, $request->validated());
            
            if (!$reservation) {
                return redirect()->route('admin.reservations.index')
                    ->with('error', 'Reservasi tidak ditemukan');
            }

            return redirect()->route('admin.reservations.show', $id)
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
                return redirect()->route('admin.reservations.index')
                    ->with('error', 'Reservasi tidak ditemukan');
            }

            return redirect()->route('admin.reservations.index')
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

    // public function byStatus(string $status): View
    // {
    //     $allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        
    //     if (!in_array($status, $allowedStatuses)) {
    //         abort(404, 'Status tidak valid');
    //     }

    //     $reservations = $this->reservationService->getReservationsByStatus($status);
        
    //     return view('admin.reservations.by-status', compact('reservations', 'status'));
    // }

    // public function today(): View
    // {
    //     $reservations = $this->reservationService->getTodayReservations();
        
    //     return view('admin.reservations.today', compact('reservations'));
    // }

    // public function upcoming(): View
    // {
    //     $reservations = $this->reservationService->getUpcomingReservations();
        
    //     return view('admin.reservations.upcoming', compact('reservations'));
    // }
}