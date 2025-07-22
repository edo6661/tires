<?php

use App\Http\Controllers\Admin\ContactController;
use Illuminate\Support\Facades\Route;

Route::name('contact.')->prefix('contact')->controller(ContactController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{id}', 'show')->name('show');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
    Route::post('/{id}/reply', 'reply')->name('reply');
    Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
    Route::post('/mark-replied', 'markAsReplied')->name('mark-replied');
});