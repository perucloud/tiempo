<?php

use App\Http\Controllers\Api\GeolocationController;
use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    Route::get('health', HealthController::class)->name('health');

    /* -------------------------------------------------------
     | Geolocalización — cliente guarda su ubicación al pedir
     | Sin auth: usa código de pedido como referencia implícita
     ------------------------------------------------------- */
    Route::post('pedidos/{codigo}/ubicacion', [GeolocationController::class, 'saveClientLocation'])
        ->name('pedidos.ubicacion');

    /* -------------------------------------------------------
     | Geolocalización — repartidor actualiza su posición GPS
     | TODO: agregar auth de repartidor cuando se implemente
     ------------------------------------------------------- */
    Route::post('repartidores/ubicacion', [GeolocationController::class, 'updateCourierLocation'])
        ->name('repartidores.ubicacion');
});
