<?php

use App\Http\Controllers\Admin\TireStorageController;
use Illuminate\Support\Facades\Route;
Route::resource('tire-storage', TireStorageController::class);