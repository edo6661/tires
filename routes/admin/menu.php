<?php
use App\Http\Controllers\Admin\MenuController;
use Illuminate\Support\Facades\Route;

Route::name('menu.')->prefix('menu')->controller(MenuController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
    Route::get('/{id}/edit', 'edit')->name('edit');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
    Route::patch('/{id}/toggle-status', 'toggleStatus')->name('toggleStatus');
    Route::post('/reorder', 'reorder')->name('reorder');
    Route::post('/calculate-end-time', action: 'calculateEndTime')->name('calculateEndTime');
    Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
});