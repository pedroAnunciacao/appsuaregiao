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
use App\Http\Middleware\BindRequestFilter;
use App\Http\Controllers\API\ContinenteController;
use App\Http\Controllers\API\PaisController;
use App\Http\Controllers\API\PaisesRegiaoController;
use App\Http\Controllers\API\PaisRegioCidadeController;
use App\Http\Controllers\API\BannedWordController;
use App\Http\Controllers\API\StatisticsController;

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
Route::get('/admin/dashboard', [StatisticsController::class, 'index']);
Route::post('/banned-words/store', [BannedWordController::class, 'store']);

// Rotas protegidas pelo Sanctum
Route::middleware(['auth:sanctum', BindRequestFilter::class])->group(function () {
Route::get('auth/me', [AuthController::class, 'me']);

    // Logout
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('/continentes', [ContinenteController::class, 'index']);
    Route::get('/paises', [PaisController::class, 'index']);
    Route::get('/regioes', [PaisesRegiaoController::class, 'index']);
    Route::get('/cidades', [PaisRegioCidadeController::class, 'index']);

    // Posts
    Route::get('posts', [PostController::class, 'index']);
    Route::post('posts', [PostController::class, 'store']);


    // Comments (por post)
    Route::post('likes', [LikeController::class, 'store']);
    Route::post('comments', [CommentController::class, 'store']);


    // Comments (admin)
    // Route::get('comments', [CommentController::class, 'all']);
    // Route::delete('comments/{id}', [CommentController::class, 'destroy']);

    // Locations (admin)
    Route::get('locations', [LocationController::class, 'index']);
    Route::post('locations', [LocationController::class, 'store']);
    Route::put('locations/{id}', [LocationController::class, 'update']);
    Route::delete('locations/{id}', [LocationController::class, 'destroy']);

    // Estados e cidades para seleção dinâmica
    Route::get('locations/states', [LocationController::class, 'estados']);
    Route::get('locations/cities', [LocationController::class, 'cidades']);

    // Banned Words (admin)
    Route::get('banned_words', [BannedWordController::class, 'index']);
    Route::post('banned_words', [BannedWordController::class, 'store']);
    Route::delete('banned_words/{id}', [BannedWordController::class, 'destroy']);

    // Likes
    // Route::post('posts/{post}/like', [LikeController::class, 'toggle']);

    // Reports
    Route::post('posts/{post}/report', [ReportController::class, 'store']);

    // Usuário logado
    Route::get('/user', [UserController::class, 'getUserData']);
    Route::get('/user/city-posts', [UserController::class, 'getUserCityPosts']);
    Route::post('/user/location', [UserController::class, 'setLocation']);

    Route::post('/user/location', [LocationController::class, 'salvarLocalizacao'])->middleware('auth:sanctum');

    // Rotas de admin
    // Route::prefix('admin')->group(function() {
    // Route::post('/locations/tv_link', [LocationController::class, 'saveTvLink']);
    // Route::get('/settings/{key}', [SettingController::class, 'show']);
    // Route::put('/settings/{key}', [SettingController::class, 'update']);

    // Route::get('/locations', [LocationController::class, 'index']);
    // Route::post('/locations', [LocationController::class, 'store']);
    // Route::put('/locations/{id}', [LocationController::class, 'update']);
    // Route::delete('/locations/{id}', [LocationController::class, 'destroy']);

    // // Rotas de admin para usuários
    // Route::post('/users/{id}/ban', [App\Http\Controllers\API\UserController::class, 'banUser']);
    // Route::post('/users/{id}/unban', [App\Http\Controllers\API\UserController::class, 'unbanUser']);
    // Route::delete('/users/{id}', [App\Http\Controllers\API\UserController::class, 'deleteUser']);
    // Route::get('/users', [App\Http\Controllers\API\UserController::class, 'listUsers']);
    // });
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/create', [UserController::class, 'create']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'edit']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);
Route::post('/users/{id}/restore', [UserController::class, 'restore']);
Route::post('/users/{id}/ban', [UserController::class, 'ban']);
Route::post('/users/{id}/unban', [UserController::class, 'unban']);

// BANNED WORDS
Route::get('/banned-words', [BannedWordController::class, 'index']);
Route::get('/banned-words/create', [BannedWordController::class, 'create']);
Route::get('/banned-words/{id}/edit', [BannedWordController::class, 'edit']);
Route::put('/banned-words/update/{id}', [BannedWordController::class, 'update']);
Route::get('/banned-words/{id}', [BannedWordController::class, 'destroy']);

// REPORTS
Route::get('/reports', [ReportController::class, 'index']);
Route::get('/reports/{id}', [ReportController::class, 'show']);


Route::get('/users', [UserController::class, 'index']);
Route::get('/banned-words', [BannedWordController::class, 'index']);
Route::get('/reports', [ReportController::class, 'index']);
Route::get('/admin/dashboard', [StatisticsController::class, 'index']);

});