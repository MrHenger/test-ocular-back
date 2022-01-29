<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\Auth\AuthMeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [LoginController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('authme', [AuthMeController::class, 'authMe']);

    Route::get('logout', [LogoutController::class, 'logout']);

    Route::resource('/category', CategoryController::class)->except('index', 'show','create', 'edit');

    Route::resource('/post', PostController::class)->except('index', 'show','create', 'edit');
}); 

Route::resource('/category', CategoryController::class)->only('show', 'index');

Route::resource('/post', PostController::class)->only('show', 'index');