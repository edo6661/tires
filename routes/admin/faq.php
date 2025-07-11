<?php
use App\Http\Controllers\Admin\FaqController;
use Illuminate\Support\Facades\Route;

Route::patch('faq/{id}/toggle-status', [FaqController::class, 'toggleStatus'])
    ->name('faq.toggleStatus');
Route::post('faq/reorder', [FaqController::class, 'reorder'])
    ->name('faq.reorder');
Route::resource('faq', FaqController::class);