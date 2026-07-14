<?php
use App\Http\Controllers\Api\AssessmentController;
use Illuminate\Support\Facades\Route;

Route::get('/batches', [AssessmentController::class, 'batches']);
Route::get('/technologies', [AssessmentController::class, 'technologies']);
Route::get('/employees', [AssessmentController::class, 'employees']);
Route::post('/marks', [AssessmentController::class, 'saveMark']);
Route::get('/report', [AssessmentController::class, 'report']);
Route::get('/report/pdf', [AssessmentController::class, 'reportPdf']);
