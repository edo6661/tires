<?php

use App\Http\Controllers\Admin\BusinessSettingController;
use Illuminate\Support\Facades\Route;

Route::name('business-setting.')->prefix('business-setting')->group(function () {
    Route::get('/', [BusinessSettingController::class, 'index'])->name('index');
    Route::get('/edit/{id}', [BusinessSettingController::class, 'edit'])->name('edit');
    Route::put('/update', [BusinessSettingController::class, 'update'])->name('update');
});