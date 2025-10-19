<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\PasswordResetController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\Admin\SettingController;
// use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\LocationController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rotas públicas: register, login, password reset
| Rotas protegidas: logout, posts, comments, likes, reports
|
*/

// Teste simples
Route::get('teste', function() {
    return ['ok' => true];
});

// Rotas públicas para localização usando pais_regiao_cidades
Route::get('/locations/tv_link', [LocationController::class, 'getTvLink']);
Route::get('/estados', [LocationController::class, 'estados']);
Route::get('/cidades/{uf}', [LocationController::class, 'cidades']);


// Autenticação pública
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

// Password reset
Route::post('password/email', [PasswordResetController::class,'sendResetEmail']);
Route::post('password/reset', [PasswordResetController::class,'resetPassword']);

// Rotas protegidas pelo Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Posts
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);

    // Comments (por post)
    Route::get('posts/{post}/comments', [CommentController::class, 'index']);
    Route::post('posts/{post}/comments', [CommentController::class, 'store']);

    // Comments (admin)
    Route::get('comments', [CommentController::class, 'all']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);

    // Locations (admin)
    Route::get('locations', [LocationController::class, 'index']);
    Route::post('locations', [LocationController::class, 'store']);
    Route::put('locations/{id}', [LocationController::class, 'update']);
    Route::delete('locations/{id}', [LocationController::class, 'destroy']);

    // Estados e cidades para seleção dinâmica
    Route::get('locations/states', [LocationController::class, 'estados']);
    Route::get('locations/cities', [LocationController::class, 'cidades']);

    // Banned Words (admin)
    Route::get('banned_words', [\App\Http\Controllers\BannedWordsController::class, 'index']);
    Route::post('banned_words', [\App\Http\Controllers\BannedWordsController::class, 'store']);
    Route::delete('banned_words/{id}', [\App\Http\Controllers\BannedWordsController::class, 'destroy']);

    // Likes
    Route::post('posts/{post}/like', [LikeController::class, 'toggle']);

    // Reports
    Route::post('posts/{post}/report', [ReportController::class, 'store']);

    // Usuário logado
    Route::get('/user', [UserController::class, 'getUserData']);
    Route::get('/user/city-posts', [UserController::class, 'getUserCityPosts']);
    Route::post('/user/location', [UserController::class, 'setLocation']);

    Route::post('/user/location', [LocationController::class, 'salvarLocalizacao'])->middleware('auth:sanctum');

    // Rotas de admin
    Route::prefix('admin')->group(function() {
    Route::post('/locations/tv_link', [LocationController::class, 'saveTvLink']);
    Route::get('/settings/{key}', [SettingController::class, 'show']);
    Route::put('/settings/{key}', [SettingController::class, 'update']);

    Route::get('/locations', [LocationController::class, 'index']);
    Route::post('/locations', [LocationController::class, 'store']);
    Route::put('/locations/{id}', [LocationController::class, 'update']);
    Route::delete('/locations/{id}', [LocationController::class, 'destroy']);

    // Rotas de admin para usuários
    Route::post('/users/{id}/ban', [App\Http\Controllers\API\UserController::class, 'banUser']);
    Route::post('/users/{id}/unban', [App\Http\Controllers\API\UserController::class, 'unbanUser']);
    Route::delete('/users/{id}', [App\Http\Controllers\API\UserController::class, 'deleteUser']);
    Route::get('/users', [App\Http\Controllers\API\UserController::class, 'listUsers']);
    });
});