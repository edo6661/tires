<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\QuestionnaireController;
use App\Http\Controllers\Api\Admin\TireStorageController;
use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\AuthController as AuthApiController;

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
            Route::post('admin-menus/reorder', [MenuController::class, 'reorder']);
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

        // Reservation API routes
        Route::prefix('reservations')->group(function () {
            // Public routes
            Route::get('/calendar', [ReservationController::class, 'getCalendarData']);
            Route::get('/availability', [ReservationController::class, 'getAvailability']);
            Route::get('/available-hours', [ReservationController::class, 'getAvailableHours']);
            Route::post('/check-availability', [ReservationController::class, 'checkAvailability']);

            // admin
            // Protected routes
            Route::middleware('auth:sanctum')->group(function () {
                Route::get('/', [ReservationController::class, 'index']);
                Route::post('/', [ReservationController::class, 'store']);
                Route::get('/{id}', [ReservationController::class, 'show']);
                Route::put('/{id}', [ReservationController::class, 'update']);
                Route::delete('/{id}', [ReservationController::class, 'destroy']);

                // Status management
                Route::patch('/{id}/confirm', [ReservationController::class, 'confirm']);
                Route::patch('/{id}/cancel', [ReservationController::class, 'cancel']);
                Route::patch('/{id}/complete', [ReservationController::class, 'complete']);

                // Bulk operations
                Route::patch('/bulk/status', [ReservationController::class, 'bulkUpdateStatus']);
            });
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

        // Public user routes
        Route::prefix('users')->group(function () {
            Route::get('/', [UserController::class, 'index']);
            Route::get('/search', [UserController::class, 'search']);
            Route::get('/customers', [UserController::class, 'customers']);
            Route::get('/admins', [UserController::class, 'admins']);
            Route::get('/role/{role}', [UserController::class, 'byRole']);
            Route::get('/{id}', [UserController::class, 'show']);
        });

        // Protected user routes
        Route::middleware('auth:sanctum')->group(function () {
            // User management (admin only)
            Route::middleware('admin')->group(function () {
                Route::post('users', [UserController::class, 'store']);
                Route::put('users/{id}', [UserController::class, 'update']);
                Route::delete('users/{id}', [UserController::class, 'destroy']);
                Route::patch('users/{id}/reset-password', [UserController::class, 'resetPassword']);
                Route::patch('users/{id}/change-password', [UserController::class, 'changePassword']);
            });

            // Profile routes
            Route::prefix('profile')->group(function () {
                Route::get('/', [ProfileController::class, 'show']);
                Route::put('/', [ProfileController::class, 'update']);
                Route::patch('/password', [ProfileController::class, 'updatePassword']);
                Route::get('/reservations', [ProfileController::class, 'reservations']);
                Route::delete('/account', [ProfileController::class, 'deleteAccount']);
            });
        });

        // Public questionnaire routes
        Route::prefix('questionnaires')->group(function () {
            Route::get('/', [QuestionnaireController::class, 'index']);
            Route::get('/search', [QuestionnaireController::class, 'search']);
            // Route::get('/filtered', [QuestionnaireController::class, 'filtered']);
            Route::get('/completion-stats', [QuestionnaireController::class, 'getCompletionStats']);
            Route::get('/status/{status}', [QuestionnaireController::class, 'byCompletionStatus']);
            Route::get('/{id}', [QuestionnaireController::class, 'show']);
            // Route::get('/{id}/summary', [QuestionnaireController::class, 'getAnswerSummary']);
            Route::get('/reservation/{reservationId}', [QuestionnaireController::class, 'getByReservation']);
            Route::post('/validate-answers', [QuestionnaireController::class, 'validateAnswers']);
        });

        // Protected questionnaire routes
        Route::middleware('auth:sanctum')->group(function () {
            // Customer can submit answers
            Route::post('questionnaires/submit', [QuestionnaireController::class, 'submitAnswers']);

            // Admin only routes
            Route::middleware('admin')->group(function () {
                Route::post('questionnaires', [QuestionnaireController::class, 'store']);
                Route::put('questionnaires/{id}', [QuestionnaireController::class, 'update']);
                Route::delete('questionnaires/{id}', [QuestionnaireController::class, 'destroy']);
            });
        });
    });
