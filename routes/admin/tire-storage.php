<?php

use App\Http\Controllers\Admin\TireStorageController;
use Illuminate\Support\Facades\Route;

Route::name('tire-storage.')->prefix('tire-storage')->controller(TireStorageController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/create', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
    Route::get('/{id}/edit', 'edit')->name('edit');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
    
    Route::post('/{id}/end', 'end')->name('end');
    Route::get('/{id}/calculate-fee', 'calculateFee')->name('calculate-fee');
    Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
    Route::post('/bulk-end', 'bulkEnd')->name('bulk-end');
});