<?php
use App\Http\Controllers\Admin\MenuController;
use Illuminate\Support\Facades\Route;

Route::patch('menu/{id}/toggle-status', [MenuController::class, 'toggleStatus'])
    ->name('menu.toggleStatus');
Route::post('menu/reorder', [MenuController::class, 'reorder'])
    ->name('menu.reorder');
Route::post('menu/calculate-end-time', [MenuController::class, 'calculateEndTime'])
    ->name('menu.calculateEndTime');
Route::resource('menu', MenuController::class);
