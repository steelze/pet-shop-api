<?php

use App\Helpers\RespondWith;
use App\Http\Controllers\Auth\Admin\CreateAdminController;
use App\Http\Controllers\Auth\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Auth\User\CreateUserController;
use App\Http\Controllers\Auth\User\ForgotPasswordController;
use App\Http\Controllers\Auth\User\LoginController;
use App\Http\Controllers\Auth\User\ResetPasswordController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function() {
    // User Routes
    Route::prefix('user')->group(function() {
        // Authentication Routes
        Route::post('create', CreateUserController::class);
        Route::post('login', LoginController::class);
        Route::post('forgot-password', ForgotPasswordController::class);
        Route::post('reset-password-token', ResetPasswordController::class);

        // Protected Routes
        Route::middleware('jwt.auth', 'can:user')->group(function() {
            Route::get('/', fn (Request $request) => RespondWith::success($request->user()->toArray()));
            Route::put('edit', [ProfileController::class, 'edit']);
        });
    });

    // Admin Routes
    Route::prefix('admin')->group(function() {
        // Authentication Routes
        Route::post('create', CreateAdminController::class);
        Route::post('login', AdminLoginController::class);

        // Protected Routes
        Route::middleware('jwt.auth', 'can:admin')->group(function() {

        });
    });
});
