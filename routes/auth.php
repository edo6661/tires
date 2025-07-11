<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('reset-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});