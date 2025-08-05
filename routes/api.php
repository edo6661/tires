<?php

use App\Http\Controllers\Api\MenuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')
    ->middleware(['apiSetLocale'])
    ->group(function () {
        Route::prefix('menus')->group(function () {
            Route::get('/', [MenuController::class, 'index']);
        });
    });