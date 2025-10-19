<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\ReportController;


// Serve o SPA Vue em qualquer rota que não seja API
Route::get('/{any}', function () {
    return view('layouts.app');
})->where('any', '^(?!api).*$');


