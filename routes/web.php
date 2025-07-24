<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/inquiry', function () {
    return view('inquiry');
})->name('inquiry');

require __DIR__ . '/auth.php';
require __DIR__ . '/customer/booking.php';


Route::middleware(['auth'])->group(function () {
    
    Route::name('customer.')->group(function () {
        Route::get('/dashboard', function () {
            return view('customer.dashboard');
        })->name('dashboard');
        require __DIR__ . '/customer/reservation.php';
    });
    
    Route::middleware('admin')->group(function () {
        Route::name('admin.')->prefix('admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            require __DIR__ . '/admin/announcement.php';
            require __DIR__ . '/admin/blocked-period.php';
            require __DIR__ . '/admin/business-setting.php';
            require __DIR__ . '/admin/contact.php';
            require __DIR__ . '/admin/customer.php';
            require __DIR__ . '/admin/faq.php';
            require __DIR__ . '/admin/menu.php';
            require __DIR__ . '/admin/payment.php';
            require __DIR__ . '/admin/questionnaire.php';
            require __DIR__ . '/admin/reservation.php';
            require __DIR__ . '/admin/tire-storage.php';
            require __DIR__ . '/admin/user.php';
        });
    });
});