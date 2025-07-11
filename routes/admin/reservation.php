<?php
use App\Http\Controllers\Admin\ReservationController;
use Illuminate\Support\Facades\Route;
Route::post('reservation/check-availability', [ReservationController::class, 'checkAvailability'])
    ->name('reservation.check-conflict');
Route::resource('reservation', ReservationController::class);