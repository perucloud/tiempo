<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'admin.access'])->group(function (): void {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::middleware('admin.businesses')->group(function (): void {
            Route::resource('businesses', BusinessController::class)
                ->except(['show']);
        });

        Route::middleware('admin.categories')->group(function (): void {
            Route::resource('categories', CategoryController::class)
                ->except(['show']);
        });

        Route::middleware('admin.products')->group(function (): void {
            Route::resource('products', ProductController::class)
                ->except(['show']);
        });

        Route::middleware('admin.users')->group(function (): void {
            Route::resource('users', UserController::class)
                ->except(['show', 'destroy']);
        });
    });
});
