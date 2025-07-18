<?php
use App\Http\Controllers\Admin\ReservationController;
use Illuminate\Support\Facades\Route;
Route::prefix('reservation')->group(function () {
    Route::name('reservation.')->group(function () {
        Route::post('check-availability', [ReservationController::class, 'checkAvailability'])
        ->name('check-availability');
        Route::get('calendar', [ReservationController::class, 'calendar'])->name('calendar');
        Route::get('api/filtered', [ReservationController::class, 'getFilteredReservations'])
            ->name('api.filtered');

        Route::get('block', [ReservationController::class, 'block'])
            ->name('block');
        Route::post('availability', [ReservationController::class, 'availability'])
            ->name('availability');
    });
});
Route::resource('reservation', ReservationController::class);
