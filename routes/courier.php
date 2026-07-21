<?php

use App\Http\Controllers\Courier\AuthController;
use App\Http\Controllers\Courier\ShiftController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

/*
 | Rutas de la interfaz del repartidor — accesibles desde el celular.
 | TODO: agregar autenticación de repartidor (login propio) en siguiente fase.
 */
Route::prefix('repartidor')->name('courier.')->group(function (): void {
    Route::get('service-worker.js', fn () => response(file_get_contents(public_path('courier/service-worker.js')), 200, ['Content-Type' => 'application/javascript']))->name('service-worker');
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AuthController::class, 'login'])->name('login.store');
    });

    Route::middleware(['auth', 'courier.access'])->group(function (): void {
        Route::get('{repartidor}/turno', [ShiftController::class, 'show'])->name('turno');
        Route::post('{repartidor}/estado', [ShiftController::class, 'updateEstado'])->name('estado.update');
        Route::post('{repartidor}/push/subscribe', [PushSubscriptionController::class, 'courier'])->name('push.subscribe');
        Route::post('logout', [AuthController::class, 'logout'])->withoutMiddleware('courier.access')->name('logout');
    });
});
