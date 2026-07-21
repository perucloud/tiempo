<?php

use App\Http\Controllers\Api\GeolocationController;
use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('health', HealthController::class)->name('health');

    /* ── Cliente: guarda ubicación al pedir ── */
    Route::post('pedidos/{codigo}/ubicacion', [GeolocationController::class, 'saveClientLocation'])
        ->name('pedidos.ubicacion');

    /* ── Repartidor: actualiza posición GPS (con throttle y precision_gps) ── */
    Route::post('repartidores/ubicacion', [GeolocationController::class, 'updateCourierLocation'])
        ->name('repartidores.ubicacion');

    /* ── Repartidor: actualiza estado operativo ── */
    Route::patch('repartidores/{repartidor}/estado', [GeolocationController::class, 'updateCourierEstado'])
        ->name('repartidores.estado');

    /* ── Repartidor: consulta su pedido activo (para la app del courier) ── */
    Route::get('repartidores/{repartidor}/pedido-activo', [GeolocationController::class, 'activePedido'])
        ->name('repartidores.pedido-activo');

    /* ── Admin/Operador: ubicaciones en tiempo real (acceso autenticado) ── */
    Route::middleware(['auth'])->group(function (): void {
        Route::get('repartidores/{repartidor}/ubicacion', [GeolocationController::class, 'courierLocation'])
            ->name('repartidores.ubicacion.get');
        Route::get('repartidores/activos/ubicaciones', [GeolocationController::class, 'activeCouriersLocations'])
            ->name('repartidores.activos');
    });
});
