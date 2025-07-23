<?php
use App\Http\Controllers\Admin\ReservationController;
use Illuminate\Support\Facades\Route;

Route::post('reservation/{reservation}/confirm', [ReservationController::class, 'confirm'])->name('reservation.confirm');
Route::post('reservation/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservation.cancel');

Route::prefix('reservation')->group(function () {
    Route::name('reservation.')->group(function () {
        Route::post('check-availability', [ReservationController::class, 'checkAvailability'])
        ->name('check-availability');
        Route::get('calendar', [ReservationController::class, 'calendar'])->name('calendar');
        Route::get('api/filtered', [ReservationController::class, 'getFilteredReservations'])
        ->name('api.filtered');
        Route::post('availability', [ReservationController::class, 'availability'])
        ->name('availability');
        Route::get('availability', [ReservationController::class, 'viewAvailability'])
            ->name('viewAvailability');
    });
});
Route::resource('reservation', ReservationController::class);
