<?php

use App\Http\Controllers\Customer\BookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('booking')
    ->name('booking.')
    ->controller(BookingController::class)
    ->group(function () {
        Route::get('first-step/{menuId}', 'firstStep')->name('first-step');
        Route::get('calendar-data', 'getCalendarData')->name('calendar-data');
        Route::get('available-hours', 'getAvailableHours')->name('available-hours');
        Route::get('second-step', 'secondStep')->name('second-step');
        Route::get('third-step', 'thirdStep')->name('third-step');
        Route::get('final-step', 'finalStep')->name('final-step');
        Route::post('create-reservation', 'createReservation')->name('create-reservation');
    });