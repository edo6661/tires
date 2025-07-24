<?php
namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;
use App\Services\ReservationServiceInterface;
use Illuminate\Http\Request;
class ReservationController extends Controller
{
    public function __construct(
        protected ReservationServiceInterface $reservationService,
    ) {}
    public function index()
    {
        $reservations = $this->reservationService->getReservationsByUser(auth()->id());
        return view('customer.reservation.index', compact('reservations'));
    }
    public function show($id)
    {
        $reservation = $this->reservationService->findReservation($id);
        if (!$reservation || $reservation->user_id !== auth()->id()) {
            abort(404);
        }
        return view('customer.reservation.show', compact('reservation'));
    }
}