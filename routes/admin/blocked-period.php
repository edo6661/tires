<?php

use App\Http\Controllers\Admin\BlockedPeriodController;
use Illuminate\Support\Facades\Route;
Route::post('blocked-period/{id}/check-conflict', [BlockedPeriodController::class, 'checkConflict'])
    ->name('blocked-period.check-conflict');
Route::resource('blocked-period', BlockedPeriodController::class);