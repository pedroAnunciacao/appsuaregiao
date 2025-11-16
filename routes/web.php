<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\StatisticsController;
use App\Http\Controllers\API\BannedWordController;
use App\Http\Controllers\WebAuthController;

Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Serve o SPA Vue em qualquer rota que não seja API
// Route::get('/portal', function () {
//     return view('layouts.app');
// })->where('any', '^(?!api).*$');

Route::get('/', function () {
    return view('layouts.app');
});




// USERS

Route::middleware(['web', 'auth'])->group(function () {
Route::get('/users', [UserController::class, 'index']);
Route::get('/admin/dashboard', [StatisticsController::class, 'index']);

Route::get('/users/create', [UserController::class, 'create']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/edit/{id}', [UserController::class, 'edit']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('/users/{id}/restore', [UserController::class, 'restore']);
Route::patch('/users/ban/{id}', [UserController::class, 'ban']);
Route::patch('/users/unban/{id}', [UserController::class, 'unban']);

// BANNED WORDS
Route::get('/banned-words', [BannedWordController::class, 'index']);
Route::get('/banned-words/create', [BannedWordController::class, 'create']);
Route::post('/banned-words', [BannedWordController::class, 'store']);
Route::get('/banned-words/{id}/edit', [BannedWordController::class, 'edit']);
Route::get('/banned-words/update/{id}', [BannedWordController::class, 'update']);
Route::get('/banned-words/{id}', [BannedWordController::class, 'destroy']);

// REPORTS
Route::get('/reports', [ReportController::class, 'index']);
Route::get('/reports/{id}', [ReportController::class, 'show']);
Route::patch('/reports/reject/{id}', [ReportController::class, 'reject']);
Route::patch('/reports/approve/{id}', [ReportController::class, 'approved']);

});

// DASHBOARD



