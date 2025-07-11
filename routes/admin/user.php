<?php
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
Route::resource('user', UserController::class);