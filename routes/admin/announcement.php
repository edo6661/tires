<?php

use App\Http\Controllers\Admin\AnnouncementController;
use Illuminate\Support\Facades\Route;
Route::patch('announcement/{id}/toggle-status', [AnnouncementController::class, 'toggleStatus'])
    ->name('announcement.toggleStatus');
Route::resource('announcement', AnnouncementController::class);