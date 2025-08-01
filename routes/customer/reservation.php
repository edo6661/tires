<?php

use App\Http\Controllers\Customer\ReservationController;
use Illuminate\Support\Facades\Route;

Route::prefix('reservation')
    ->name('reservation.')
    ->controller(ReservationController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
    });