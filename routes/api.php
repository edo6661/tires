<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Public Controllers
use App\Http\Controllers\Api\MenuController;
// Customer Controllers
use App\Http\Controllers\Api\AuthController as AuthApiController;
use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Customer\ProfileController;
use App\Http\Controllers\Api\Customer\QuestionnaireController;

// Admin Controllers
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\AnnouncementController;
use App\Http\Controllers\Api\Admin\TireStorageController;
use App\Http\Controllers\Api\Admin\ReservationController;
use App\Http\Controllers\Api\Admin\ContactController as ApiAdminContactController;
use App\Http\Controllers\Api\Admin\CustomerController as ApiAdminCustomerController;
use App\Http\Controllers\Api\Admin\DashboardController as ApiAdminDashboardController;
use App\Http\Controllers\Api\Admin\QuestionnaireController as ApiAdminQuestionnaireController;
use App\Http\Controllers\Api\Admin\BusinessSettingController as ApiAdminBusinessSettingController;
use App\Http\Controllers\Api\Admin\FaqController as ApiAdminFaqController;
use App\Http\Controllers\Api\Admin\PaymentController as ApiAdminPaymentController;
use App\Http\Controllers\Api\Admin\BlockedPeriodController as ApiAdminBlockedPeriodController;

// default route API (cek user login dengan Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')
    ->middleware(['apiSetLocale'])
    ->group(function () {

        // Public endpoints (no authentication required)
        Route::prefix('public')->group(function () {
            // Public menu access
            Route::get('/menus', [MenuController::class, 'index']);
            Route::get('/menus/{id}', [MenuController::class, 'show']);

            // Contact and inquiry endpoints
            // Route::post('/contact', [\App\Http\Controllers\Api\ContactController::class, 'submitContact']);
            Route::post('/inquiry', [\App\Http\Controllers\Api\ContactController::class, 'submitInquiry']);
        });

        // Auth
        Route::prefix('auth')->group(function () {
            Route::post('/login', [AuthApiController::class, 'login']);
            Route::post('/register', [AuthApiController::class, 'register']);
            Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
            Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);

            Route::middleware('auth:sanctum')->group(function () {
                Route::post('/logout', [AuthApiController::class, 'logout']);
            });
        });

        // Customer endpoints (authenticated customers only)
        Route::prefix('customer')
            ->middleware(['auth:sanctum'])
            ->group(function () {
                // Customer profile management
                Route::get('/profile', [CustomerController::class, 'profile']);
                Route::patch('/profile', [CustomerController::class, 'updateProfile']);
                Route::patch('/change-password', [CustomerController::class, 'changePassword']);
                Route::delete('/account', [CustomerController::class, 'deleteAccount']);

                // Customer dashboard
                Route::get('/dashboard', [CustomerController::class, 'dashboard']);

                // Customer booking functionality
                Route::prefix('booking')->group(function () {
                    Route::get('/first-step/{menuId}', [CustomerController::class, 'bookingFirstStep']);
                    Route::get('/calendar-data', [CustomerController::class, 'getCalendarData']);
                    Route::get('/available-hours', [CustomerController::class, 'getAvailableHours']);
                    Route::get('/menu-details/{menuId}', [CustomerController::class, 'getMenuDetails']);
                    Route::post('/create-reservation', [CustomerController::class, 'createReservation']);
                });

                // Customer reservations - specific routes MUST come before parameterized routes
                Route::get('/reservations', [CustomerController::class, 'reservations']);
                Route::get('/reservations/summary', [CustomerController::class, 'reservationsSummary']);
                Route::get('/reservations/pending', [CustomerController::class, 'pendingReservations']);
                Route::get('/reservations/completed', [CustomerController::class, 'completedReservations']);
                Route::get('/reservations/status/{status}', [CustomerController::class, 'reservationsByStatus']);
                Route::get('/reservations/availability', [CustomerController::class, 'getAvailability']);
                Route::post('/reservations/check-availability', [CustomerController::class, 'checkAvailability']);
                Route::get('/reservations/{id}', [CustomerController::class, 'reservation']);

                // Customer inquiry and contact
                Route::post('/inquiry', [CustomerController::class, 'submitInquiry']);
                Route::get('/inquiry-history', [CustomerController::class, 'getInquiryHistory']);

                // Customer tire storage
                Route::get('/tire-storage', [CustomerController::class, 'tireStorage']);
                Route::post('/tire-storage', [CustomerController::class, 'createTireStorage']);
                Route::get('/tire-storage/summary', [CustomerController::class, 'getTireStorageSummary']);
                Route::get('/tire-storage/{id}', [CustomerController::class, 'tireStorageItem']);
                Route::patch('/tire-storage/{id}', [CustomerController::class, 'updateTireStorage']);
                Route::post('/tire-storage/{id}/pickup', [CustomerController::class, 'requestTirePickup']);

                // Menu access for customers
                Route::prefix('menus')->group(function () {
                    Route::get('/', [MenuController::class, 'index']);
                    Route::get('/search', [MenuController::class, 'search']); // Specific route first
                    Route::post('/calculate-end-time', [MenuController::class, 'calculateEndTime']);
                    Route::get('/{id}', [MenuController::class, 'show']); // Parameterized route after specific ones
                    Route::get('/{id}/available-slots', [MenuController::class, 'getAvailableSlots']);
                });
            });

        // Admin Endpoint
        Route::prefix('admin')
            ->middleware(['auth:sanctum', 'admin'])
            ->group(function () {
                // Profile
                Route::prefix('profile')->group(function () {
                    Route::get('/', [ProfileController::class, 'show']);
                    Route::patch('/', [ProfileController::class, 'update']);
                    Route::patch('/password', [ProfileController::class, 'updatePassword']);
                    Route::get('/reservations', [ProfileController::class, 'reservations']);
                    Route::delete('/account', [ProfileController::class, 'deleteAccount']);
                });

                // User Management
                Route::get('users/search', [UserController::class, 'search']);
                Route::apiResource('users', UserController::class);
                Route::get('users/customers', [UserController::class, 'customers']);
                Route::get('users/admins', [UserController::class, 'admins']);
                Route::get('users/role/{role}', [UserController::class, 'byRole']);
                Route::patch('users/{id}/reset-password', [UserController::class, 'resetPassword']);
                // Route::patch('users/{id}/change-password', [UserController::class, 'changePassword']);

                //  Menu Management
                Route::apiResource('menus', MenuController::class);
                Route::patch('menus/{id}/toggle-status', [MenuController::class, 'toggleStatus']);
                Route::delete('menus/bulk-delete', [MenuController::class, 'bulkDelete']);
                Route::patch('menus/bulk-update-status', [MenuController::class, 'bulkUpdateStatus']);
                Route::get('menus/search', [MenuController::class, 'search']);
                Route::post('menus/calculate-end-time', [MenuController::class, 'calculateEndTime']);
                Route::get('menus/{id}/available-slots', [MenuController::class, 'getAvailableSlots']);
                Route::post('menus/reorder', [MenuController::class, 'reorder']);

                // Tire Storage Management
                Route::apiResource('storages', TireStorageController::class);
                Route::patch('/storages/{id}/end', [TireStorageController::class, 'end']);
                Route::delete('/storages/bulk-delete', [TireStorageController::class, 'bulkDelete']);
                Route::patch('/storages/bulk-end', [TireStorageController::class, 'bulkEnd']);


                // Reservation Management
                Route::apiResource('reservations', ReservationController::class);
                Route::patch('reservations/{id}/confirm', [ReservationController::class, 'confirm']);
                Route::patch('reservations/{id}/cancel', [ReservationController::class, 'cancel']);
                Route::patch('reservations/{id}/complete', [ReservationController::class, 'complete']);
                Route::patch('reservations/bulk/status', [ReservationController::class, 'bulkUpdateStatus']);

                // Announcement
                Route::get('announcements/statistics', [AnnouncementController::class, 'statistics']);
                Route::apiResource('announcements', AnnouncementController::class);
                Route::patch('announcements/{id}/toggle-status', [AnnouncementController::class, 'toggleStatus']);
                Route::patch('announcements/bulk-toggle-status', [AnnouncementController::class, 'bulkToggleStatus']);
                Route::delete('announcements/bulk-delete', [AnnouncementController::class, 'bulkDelete']);

                // Questionnaire
                Route::prefix('questionnaires')->group(function () {
                    Route::apiResource('/', QuestionnaireController::class);
                    Route::post('/submit', [QuestionnaireController::class, 'submitAnswers']);
                    Route::get('/search', [QuestionnaireController::class, 'search']);
                    // Route::get('/filtered', [QuestionnaireController::class, 'filtered']);
                    Route::get('/completion-stats', [QuestionnaireController::class, 'getCompletionStats']);
                    Route::get('/status/{status}', [QuestionnaireController::class, 'byCompletionStatus']);
                    // Route::get('/{id}/summary', [QuestionnaireController::class, 'getAnswerSummary']);
                    Route::get('/reservation/{reservationId}', [QuestionnaireController::class, 'getByReservation']);
                    Route::post('/validate-answers', [QuestionnaireController::class, 'validateAnswers']);
                });

                // Admin Questionnaire Management
                Route::prefix('questionnaires')->group(function () {
                    Route::get('/', [ApiAdminQuestionnaireController::class, 'index']);
                    Route::post('/', [ApiAdminQuestionnaireController::class, 'store']);
                    Route::get('/{id}', [ApiAdminQuestionnaireController::class, 'show']);
                    Route::patch('/{id}', [ApiAdminQuestionnaireController::class, 'update']);
                    Route::delete('/{id}', [ApiAdminQuestionnaireController::class, 'destroy']);
                    Route::get('/reservation/{reservationId}', [ApiAdminQuestionnaireController::class, 'getByReservation']);
                    Route::post('/validate-answers', [ApiAdminQuestionnaireController::class, 'validateAnswers']);
                });

                // Contact Management
                Route::prefix('contacts')->group(function () {
                    Route::get('/', [ApiAdminContactController::class, 'index']);
                    Route::get('/{id}', [ApiAdminContactController::class, 'show']);
                    Route::patch('/{id}', [ApiAdminContactController::class, 'update']);
                    Route::delete('/{id}', [ApiAdminContactController::class, 'destroy']);
                    Route::post('/{id}/reply', [ApiAdminContactController::class, 'reply']);
                    Route::delete('/bulk-delete', [ApiAdminContactController::class, 'bulkDelete']);
                    Route::post('/mark-as-replied', [ApiAdminContactController::class, 'markAsReplied']);
                });

                // Customer Management
                Route::prefix('customers')->group(function () {
                    Route::get('/', [ApiAdminCustomerController::class, 'index']);
                    Route::get('/first-time', [ApiAdminCustomerController::class, 'getFirstTimeCustomers']);
                    Route::get('/repeat-customers', [ApiAdminCustomerController::class, 'getRepeatCustomers']);
                    Route::get('/dormant-customers', [ApiAdminCustomerController::class, 'getDormantCustomers']);
                    Route::get('/search', [ApiAdminCustomerController::class, 'search']);
                    Route::get('/type-counts', [ApiAdminCustomerController::class, 'getCustomerTypeCounts']);
                    Route::get('/{id}', [ApiAdminCustomerController::class, 'show']);
                });

                // Dashboard
                Route::get('/dashboard', [ApiAdminDashboardController::class, 'index']);

                // Business Settings
                Route::prefix('business-settings')->group(function () {
                    Route::get('/', [ApiAdminBusinessSettingController::class, 'index']);
                    Route::get('/business-hours', [ApiAdminBusinessSettingController::class, 'getBusinessHours']);
                    Route::get('/top-image', [ApiAdminBusinessSettingController::class, 'getTopImage']);
                    Route::patch('/business-hours', [ApiAdminBusinessSettingController::class, 'updateBusinessHours']);
                    // Route::get('/{id}/edit', [ApiAdminBusinessSettingController::class, 'edit']);
                    Route::patch('/update', [ApiAdminBusinessSettingController::class, 'update']);
                });

                // FAQ Management
                Route::prefix('faqs')->group(function () {
                    Route::get('/', [ApiAdminFaqController::class, 'index']);
                    Route::post('/', [ApiAdminFaqController::class, 'store']);
                    Route::get('/active', [ApiAdminFaqController::class, 'getActiveFaqs']);
                    Route::post('/reorder', [ApiAdminFaqController::class, 'reorder']);
                    Route::get('/{id}', [ApiAdminFaqController::class, 'show']);
                    Route::patch('/{id}', [ApiAdminFaqController::class, 'update']);
                    Route::delete('/{id}', [ApiAdminFaqController::class, 'destroy']);
                    Route::patch('/{id}/toggle-status', [ApiAdminFaqController::class, 'toggleStatus']);
                });

                // Payment Management
                // Route::prefix('payments')->group(function () {
                //     Route::get('/', [ApiAdminPaymentController::class, 'index']);
                //     Route::post('/', [ApiAdminPaymentController::class, 'store']);
                //     Route::get('/revenue/total', [ApiAdminPaymentController::class, 'getTotalRevenue']);
                //     Route::get('/statistics', [ApiAdminPaymentController::class, 'getStatistics']);
                //     Route::post('/bulk-update-status', [ApiAdminPaymentController::class, 'bulkUpdateStatus']);
                //     Route::get('/status/{status}', [ApiAdminPaymentController::class, 'getByStatus']);
                //     Route::get('/user/{user_id}', [ApiAdminPaymentController::class, 'getByUser']);
                //     Route::get('/reservation/{reservation_id}', [ApiAdminPaymentController::class, 'getByReservation']);
                //     Route::get('/{id}', [ApiAdminPaymentController::class, 'show']);
                //     Route::patch('/{id}', [ApiAdminPaymentController::class, 'update']);
                //     Route::delete('/{id}', [ApiAdminPaymentController::class, 'destroy']);
                //     Route::post('/{id}/process', [ApiAdminPaymentController::class, 'processPayment']);
                // });

                // Blocked Period Management
                Route::prefix('blocked-periods')->group(function () {
                    Route::get('/', [ApiAdminBlockedPeriodController::class, 'index']);
                    Route::post('/', [ApiAdminBlockedPeriodController::class, 'store']);
                    Route::post('/check-conflict', [ApiAdminBlockedPeriodController::class, 'checkConflict']);
                    Route::get('/calendar', [ApiAdminBlockedPeriodController::class, 'calendar']);
                    Route::get('/calendar-with-conflicts', [ApiAdminBlockedPeriodController::class, 'calendarWithConflicts']);
                    Route::get('/available-slots', [ApiAdminBlockedPeriodController::class, 'getAvailableSlots']);
                    Route::post('/batch-check-conflicts', [ApiAdminBlockedPeriodController::class, 'batchCheckConflicts']);
                    Route::post('/export', [ApiAdminBlockedPeriodController::class, 'export']);
                    Route::delete('/bulk-delete', [ApiAdminBlockedPeriodController::class, 'bulkDelete']);
                    Route::get('/{id}', [ApiAdminBlockedPeriodController::class, 'show']);
                    Route::patch('/{id}', [ApiAdminBlockedPeriodController::class, 'update']);
                    Route::delete('/{id}', [ApiAdminBlockedPeriodController::class, 'destroy']);
                });
            });
    });
