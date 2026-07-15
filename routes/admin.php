<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\CourierAssignmentController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryZoneController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
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

        Route::middleware('admin.orders')->group(function (): void {
            Route::resource('orders', OrderController::class)
                ->only(['index', 'edit', 'update']);
        });

        Route::middleware('admin.couriers')->group(function (): void {
            Route::resource('couriers', CourierController::class)
                ->except(['show']);
            Route::get('couriers-tracking', [CourierController::class, 'tracking'])
                ->name('couriers.tracking');
            Route::get('couriers-ubicaciones', [CourierController::class, 'ubicaciones'])
                ->name('couriers.ubicaciones');
            Route::patch('orders/{order}/courier', [CourierAssignmentController::class, 'update'])
                ->name('orders.courier.update');
        });

        Route::middleware('admin.payments')->group(function (): void {
            Route::resource('payments', PaymentController::class)
                ->only(['index', 'edit', 'update']);
        });

        Route::middleware('admin.reports')->group(function (): void {
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        });

        Route::middleware('admin.notifications')->group(function (): void {
            Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        });

        Route::middleware('admin.settings')->group(function (): void {
            Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
            Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
            Route::resource('delivery-zones', DeliveryZoneController::class)
                ->except(['index', 'show']);
        });

        Route::middleware('admin.businesses')->group(function (): void {
            Route::resource('businesses', BusinessController::class)
                ->except(['show']);
        });

        Route::middleware('admin.categories')->group(function (): void {
            Route::resource('categories', CategoryController::class)
                ->except(['show']);
        });

        Route::middleware('admin.clients')->group(function (): void {
            Route::resource('clients', ClientController::class)
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
