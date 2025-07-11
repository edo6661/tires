<?php

use App\Http\Controllers\Admin\BusinessSettingController;
use Illuminate\Support\Facades\Route;
Route::resource('business-setting', BusinessSettingController::class);