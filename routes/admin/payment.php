<?php
use App\Http\Controllers\Admin\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('payment/by-status', [PaymentController::class, 'getByStatus'])
    ->name('payment.byStatus');
Route::get('payment/by-user', [PaymentController::class, 'getByUser'])
    ->name('payment.byUser');
Route::get('payment/by-reservation', [PaymentController::class, 'getByReservation'])
    ->name('payment.byReservation');
Route::get('payment/total-revenue', [PaymentController::class, 'getTotalRevenue'])
    ->name('payment.totalRevenue');
Route::post('payment/process', [PaymentController::class, 'processPayment'])
    ->name('payment.process');
Route::resource('payment', PaymentController::class);