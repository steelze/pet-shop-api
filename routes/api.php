<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Auth\User\LoginController;
use App\Http\Controllers\Auth\User\CreateUserController;
use App\Http\Controllers\Auth\Admin\CreateAdminController;
use App\Http\Controllers\Auth\User\ResetPasswordController;
use App\Http\Controllers\Auth\User\ForgotPasswordController;
use App\Http\Controllers\Auth\Admin\LoginController as AdminLoginController;

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

Route::prefix('v1')->group(function (): void {
    Route::middleware('jwt.auth')->get('user', [ProfileController::class, 'view']);

    // User Routes
    Route::prefix('user')->group(function (): void {
        // Authentication Routes
        Route::post('create', CreateUserController::class);
        Route::post('login', LoginController::class);
        Route::post('forgot-password', ForgotPasswordController::class);
        Route::post('reset-password-token', ResetPasswordController::class);

        // Protected Routes
        Route::middleware('jwt.auth', 'can:user')->group(function (): void {
            Route::put('edit', [ProfileController::class, 'edit']);
        });
    });

    // Admin Routes
    Route::prefix('admin')->group(function (): void {
        // Authentication Routes
        Route::post('create', CreateAdminController::class);
        Route::post('login', AdminLoginController::class);

        // Protected Routes
        Route::middleware('jwt.auth', 'can:admin')->group(function (): void {
            Route::get('user-listing', [UserController::class, 'listing']);
            Route::put('user-edit/{uuid}', [UserController::class, 'edit']);
            Route::delete('user-delete/{uuid}', [UserController::class, 'delete']);
        });
    });

    Route::middleware('jwt.auth')->group(function (): void {
        Route::controller(CategoryController::class)->prefix('category')->group(function (): void {
            Route::post('create', 'create');
            Route::put('{uuid}', 'update');
            Route::delete('{uuid}', 'delete');
        });

        Route::controller(BrandController::class)->prefix('brand')->group(function (): void {
            Route::post('create', 'create');
            Route::put('{uuid}', 'update');
            Route::delete('{uuid}', 'delete');
        });

        Route::controller(OrderStatusController::class)->prefix('order-status')->group(function (): void {
            Route::post('create', 'create');
            Route::put('{uuid}', 'update');
            Route::delete('{uuid}', 'delete');
        });
    });

    // Unprotected Routes
    Route::controller(CategoryController::class)->group(function (): void {
        Route::get('category/{uuid}', 'findOne');
        Route::get('categories', 'listAll');
    });

    Route::controller(BrandController::class)->group(function (): void {
        Route::get('brand/{uuid}', 'findOne');
        Route::get('brands', 'listAll');
    });

    Route::controller(OrderStatusController::class)->group(function (): void {
        Route::get('order-status/{uuid}', 'findOne');
        Route::get('order-statuses', 'listAll');
    });
});
