<?php

use App\Http\Controllers\Courier\ShiftController;
use Illuminate\Support\Facades\Route;

/*
 | Rutas de la interfaz del repartidor — accesibles desde el celular.
 | Auth: ninguna en MVP. El operador comparte la URL directamente al repartidor.
 | TODO: agregar autenticación cuando se implemente el login de repartidor.
 */
Route::prefix('repartidor')->name('courier.')->group(function (): void {
    Route::get('{repartidor}/turno', [ShiftController::class, 'show'])->name('turno');
});
