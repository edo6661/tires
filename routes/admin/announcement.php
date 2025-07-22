<?php

use App\Http\Controllers\Admin\AnnouncementController;
use Illuminate\Support\Facades\Route;

Route::post('announcement/{id}/toggle-status', [AnnouncementController::class, 'toggleStatus'])
    ->name('announcement.toggleStatus'); 

Route::post('announcement/bulk-toggle-status', [AnnouncementController::class, 'bulkToggleStatus'])
    ->name('announcement.bulkToggleStatus');

Route::post('announcement/bulk-delete', [AnnouncementController::class, 'bulkDelete'])
    ->name('announcement.bulkDelete');

Route::resource('announcement', AnnouncementController::class);