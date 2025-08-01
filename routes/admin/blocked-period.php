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
    
    Route::get('calendar/conflicts', 'calendarWithConflicts')->name('calendar.conflicts');
    Route::get('available-slots', 'getAvailableSlots')->name('available-slots');
    Route::post('batch-check-conflicts', 'batchCheckConflicts')->name('batch-check-conflicts');
});
Route::resource('blocked-period', BlockedPeriodController::class)
    ->names('blocked-period');
