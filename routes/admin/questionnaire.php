<?php
use App\Http\Controllers\Admin\QuestionnaireController;
use Illuminate\Support\Facades\Route;

Route::get('questionnaire/by-reservation', [QuestionnaireController::class, 'getByReservation'])
    ->name('questionnaire.byReservation');
Route::post('questionnaire/validate-answers', [QuestionnaireController::class, 'validateAnswers'])
    ->name('questionnaire.validateAnswers');
Route::resource('questionnaire', QuestionnaireController::class);