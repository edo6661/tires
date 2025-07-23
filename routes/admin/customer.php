<?php

use App\Http\Controllers\Admin\CustomerController;
use Illuminate\Support\Facades\Route;

Route::name('customer.')->prefix('customer')->controller(CustomerController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{id}', 'show')->name('show');
    Route::get('/api/first-time', 'getFirstTimeCustomers')->name('api.first-time');
    Route::get('/api/repeat', 'getRepeatCustomers')->name('api.repeat');
    Route::get('/api/dormant', 'getDormantCustomers')->name('api.dormant');
    Route::get('/api/monthly-plan', 'getMonthlyPlanCustomers')->name('api.monthly-plan');
    Route::post('/api/search', 'search')->name('api.search');
    Route::get('/api/counts', 'getCustomerTypeCounts')->name('api.counts');
});