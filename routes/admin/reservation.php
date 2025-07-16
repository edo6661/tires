<?php
use App\Http\Controllers\Admin\ReservationController;
use Illuminate\Support\Facades\Route;
Route::post('reservation/check-availability', [ReservationController::class, 'checkAvailability'])
    ->name('reservation.check-availability');
Route::get('reservation/calendar', [ReservationController::class, 'calendar'])->name('reservation.calendar');
Route::get('reservation/api/filtered', [ReservationController::class, 'getFilteredReservations'])
    ->name('reservation.api.filtered');

Route::get('reservation/block', [ReservationController::class, 'block'])
    ->name('reservation.block');
Route::get('reservation/availability', [ReservationController::class, 'availability'])
    ->name('reservation.availability');
Route::resource('reservation', ReservationController::class);