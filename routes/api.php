<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Api\ReservationController;


// default route API (cek user login dengan Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')
    ->middleware(['apiSetLocale'])
    ->group(function () {

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('/login', [AuthController::class, 'login']);
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
            Route::post('/reset-password', [AuthController::class, 'resetPassword']);

            Route::middleware('auth:sanctum')->group(function () {
                Route::get('/profile', [UserController::class, 'profile']);
                Route::post('/logout', [AuthController::class, 'logout']);
            });
        });

        // Menu API
        // Route::get('/menus', [MenuController::class, 'index']);
        // Route::get('/menus/{id}', [MenuController::class, 'show']);

        // Users API
        // Route::middleware('auth:sanctum')->group(function () {
        //     Route::get('/users', [UserController::class, 'index']);
        //     Route::get('/users/{id}', [UserController::class, 'show']);
        //     Route::post('/users', [UserController::class, 'store']);
        //     Route::put('/users/{id}', [UserController::class, 'update']);
        //     Route::delete('/users/{id}', [UserController::class, 'destroy']);
        // });

        // Reservations API Admin

        Route::prefix('admin-reservations')
            ->middleware(['auth:sanctum', 'admin'])
            ->group(function () {

                // 📌 Bulk update status banyak reservasi sekaligus
                Route::patch('/reservations/bulk-status', [ReservationController::class, 'bulkUpdateStatus'])
                    ->name('reservations.bulk-status');

                // 📌 Ubah status reservasi (misal confirm, cancel, complete)
                Route::patch('/reservations/{reservation}/status', [ReservationController::class, 'changeStatus'])
                    ->name('reservations.change-status');

                // 📌 Resource API (index, store, show, update, destroy)
                Route::apiResource('reservations', ReservationController::class);
            });
    });
