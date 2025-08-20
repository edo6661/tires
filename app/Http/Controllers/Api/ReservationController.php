<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use App\Enums\ReservationStatus;

class ReservationController extends Controller
{
    // GET /api/reservations
    public function index(Request $request)
    {
        $reservations = Reservation::with(['user', 'menu'])
            ->latest()
            ->paginate(10);

        return response()->json($reservations);
    }

    // GET /api/reservations/{id}
    public function show(Reservation $reservation)
    {
        return response()->json($reservation->load(['user', 'menu', 'payments', 'questionnaire']));
    }

    // POST /api/reservations
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reservation_number' => 'required|string|unique:reservations',
            'user_id' => 'nullable|exists:users,id',
            'menu_id' => 'required|exists:menus,id',
            'reservation_datetime' => 'required|date|after:now',
            'number_of_people' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'status' => ['required', Rule::in(ReservationStatus::cases())],
            'notes' => 'nullable|string',
            'full_name' => 'nullable|string',
            'full_name_kana' => 'nullable|string',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string',
        ]);

        $reservation = Reservation::create($validated);

        return response()->json($reservation, Response::HTTP_CREATED);
    }

    // PUT/PATCH /api/reservations/{id}
    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'menu_id' => 'sometimes|exists:menus,id',
            'reservation_datetime' => 'sometimes|date|after:now',
            'number_of_people' => 'sometimes|integer|min:1',
            'amount' => 'sometimes|numeric|min:0',
            'status' => ['sometimes', Rule::in(ReservationStatus::cases())],
            'notes' => 'nullable|string',
            'full_name' => 'nullable|string',
            'full_name_kana' => 'nullable|string',
            'email' => 'nullable|email',
            'phone_number' => 'nullable|string',
        ]);

        $reservation->update($validated);

        return response()->json($reservation);
    }

    // DELETE /api/reservations/{id}
    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    // PATCH /api/reservations/bulk-status
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'reservation_ids' => 'required|array',
            'reservation_ids.*' => 'exists:reservations,id',
            'status' => ['required', Rule::in(ReservationStatus::cases())],
        ]);

        Reservation::whereIn('id', $validated['reservation_ids'])
            ->update(['status' => $validated['status']]);

        return response()->json([
            'message' => 'Statuses updated successfully',
        ]);
    }
}
