<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
Route::middleware('guest')->group(function () {
    Route::get('/login', function (){
      
    })->name('login');
    Route::post('/login', function (){
      
    });
    
    Route::get('/register', function (){
      
    })->name('register');
    Route::post('/register', function (){
      
    });
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function (){
      
    })->name('logout');
});