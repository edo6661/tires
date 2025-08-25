<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController as AuthApiController;
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
            Route::post('/login', [AuthApiController::class, 'login']);
            Route::post('/register', [AuthApiController::class, 'register']);
            Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
            Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);

            Route::middleware('auth:sanctum')->group(function () {
                Route::get('/profile', [UserController::class, 'profile']);
                Route::post('/logout', [AuthApiController::class, 'logout']);
            });
        });

        // Public Menu API
        Route::prefix('menus')->group(function () {
            Route::get('/', [MenuController::class, 'index']);
            Route::get('/{id}', [MenuController::class, 'show']);
            // TAMBAHAN: Route untuk search menu
            Route::get('/search', [MenuController::class, 'search']);
            // TAMBAHAN: Route untuk calculate end time
            Route::post('/calculate-end-time', [MenuController::class, 'calculateEndTime']);
            // TAMBAHAN: Route untuk available slots
            Route::get('/{id}/available-slots', [MenuController::class, 'getAvailableSlots']);
        });

        // Admin Menu Management
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::apiResource('admin-menus', MenuController::class);

            Route::patch('admin-menus/{id}/toggle-status', [MenuController::class, 'toggleStatus']);
            Route::delete('admin-menus/bulk-delete', [MenuController::class, 'bulkDelete']);
            Route::patch('admin-menus/bulk-update-status', [MenuController::class, 'bulkUpdateStatus']);
            Route::get('admin-menus/search', [MenuController::class, 'search']);
            Route::post('admin-menus/calculate-end-time', [MenuController::class, 'calculateEndTime']);
            Route::get('admin-menus/{id}/available-slots', [MenuController::class, 'getAvailableSlots']);
        });


        Route::prefix('admin')->group(function () {
            Route::apiResource('announcements', AnnouncementController::class);

            Route::patch('announcements/{id}/toggle-status', [AnnouncementController::class, 'toggleStatus']);
            Route::patch('announcements/bulk-toggle-status', [AnnouncementController::class, 'bulkToggleStatus']);
            Route::delete('announcements/bulk-delete', [AnnouncementController::class, 'bulkDelete']);
        });

        Route::prefix('admin-users')
            ->middleware(['auth:sanctum'])
            ->group(function () {
                Route::apiResource('users', UserController::class);

                // Reset password khusus admin
                Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
            });

        // Reservations API Admin
        Route::prefix('admin-reservations')
            ->middleware(['auth:sanctum'])
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
            ->middleware(['auth:sanctum'])
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
    });
