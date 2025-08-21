<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Admin\QuestionnaireController;
use App\Http\Controllers\Api\Admin\TireStorageController;
use App\Http\Controllers\Api\Admin\AnnouncementController;


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

        Route::prefix('admin-users')
            ->middleware(['auth:sanctum', 'admin'])
            ->group(function () {
                Route::apiResource('users', UserController::class);

                // Reset password khusus admin
                Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
            });

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

        // Tire Storage Admin
        Route::prefix('admin-tire-storages')
            ->middleware(['auth:sanctum', 'admin'])
            ->group(function () {
                Route::apiResource('storages', TireStorageController::class);

                Route::patch('/storages/{id}/end', [TireStorageController::class, 'end']);
                Route::delete('/storages/bulk-delete', [TireStorageController::class, 'bulkDelete']);
                Route::patch('/storages/bulk-end', [TireStorageController::class, 'bulkEnd']);
            });

        Route::prefix('admin')->middleware(['auth:sanctum'])->group(function () {
            Route::apiResource('questionnaires', QuestionnaireController::class);

            Route::get('questionnaires-by-reservation', [QuestionnaireController::class, 'getByReservation']);
            Route::post('questionnaires/validate-answers', [QuestionnaireController::class, 'validateAnswers']);
        });

        Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
            Route::apiResource('announcements', AnnouncementController::class);

            Route::patch('announcements/{id}/toggle-status', [AnnouncementController::class, 'toggleStatus']);
            Route::patch('announcements/bulk-toggle-status', [AnnouncementController::class, 'bulkToggleStatus']);
            Route::delete('announcements/bulk-delete', [AnnouncementController::class, 'bulkDelete']);
        });

        
    });
