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

    Route::post('logout', [LogoutController::class, 'logout']);

    Route::resource('/admin/category', CategoryController::class)->except('create', 'edit');

    Route::resource('/admin/post', PostController::class)->except('create', 'edit', 'update');

    Route::post('/admin/post/{post}', [PostController::class, 'update'])->name('post.update');
}); 

// Public routes for post
Route::group(['prefix' => 'post'], function () {
    Route::get('/', [PostController::class, 'publicIndex'])->name('post.public.index');
    Route::get('/{post}', [PostController::class, 'publicShow'])->name('post.public.show');
});

// Public routes for category
Route::group(['prefix' => 'category'], function () {
    Route::get('/', [CategoryController::class, 'publicIndex'])->name('category.public.index');
    Route::get('/{category}', [CategoryController::class, 'publicShow'])->name('category.public.show');
});
