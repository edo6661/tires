<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlockedPeriodController;

Route::prefix('blocked-period')
    ->name('blocked-period.')
    ->controller(BlockedPeriodController::class)
    ->group(function () {
    Route::get('calendar', 'calendar')->name('calendar');
    Route::post('check-conflict', 'checkConflict')->name('check-conflict');
    Route::get('export', 'export')->name('export');
    Route::post('bulk-delete', 'bulkDelete')->name('bulk-delete');
});
Route::resource('blocked-period', BlockedPeriodController::class)
    ->names('blocked-period');
