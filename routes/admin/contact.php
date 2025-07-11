<?php

use App\Http\Controllers\Admin\ContactController;
use Illuminate\Support\Facades\Route;
Route::resource('contact', ContactController::class);