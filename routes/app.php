<?php

use App\Http\Controllers\App\BusinessController;
use App\Http\Controllers\App\CartController;
use App\Http\Controllers\App\CustomerAccessController;
use App\Http\Controllers\App\DeliveryQuoteController;
use App\Http\Controllers\App\HomeController;
use App\Http\Controllers\App\OrderController;
use App\Http\Controllers\App\OrderTrackingController;
use App\Http\Controllers\App\PaymentController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->name('app.')->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::get('negocios/{negocio:slug}', [BusinessController::class, 'show'])->name('negocio.show');
    Route::post('cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('cart', [CartController::class, 'update'])->name('cart.update');
    Route::patch('cart/address', [CartController::class, 'address'])->name('cart.address');
    Route::post('delivery/quote', DeliveryQuoteController::class)->middleware('throttle:20,1')->name('delivery.quote');
    Route::delete('cart', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('pedidos/{codigo}', [OrderTrackingController::class, 'show'])->middleware('throttle:30,1')->name('orders.show');
    Route::get('pedidos/{codigo}/estado', [OrderTrackingController::class, 'estado'])->middleware('throttle:60,1')->name('orders.estado');
    Route::post('perfil/buscar', [OrderTrackingController::class, 'buscarPorTelefono'])->middleware('throttle:10,1')->name('perfil.buscar');
    Route::post('perfil/codigo', [CustomerAccessController::class, 'requestCode'])->middleware('throttle:3,10')->name('perfil.codigo');
    Route::post('perfil/verificar', [CustomerAccessController::class, 'verifyCode'])->middleware('throttle:10,10')->name('perfil.verificar');
    Route::post('payments', [PaymentController::class, 'store'])->middleware('throttle:5,1')->name('payments.store');
    Route::post('push/subscribe', [PushSubscriptionController::class, 'customer'])->middleware('throttle:10,1')->name('push.subscribe');
    Route::get('manifest.webmanifest', fn () => response(
        file_get_contents(public_path('app/manifest.webmanifest')),
        200,
        [
            'Content-Type' => 'application/manifest+json',
        ],
    ))->name('manifest');
    Route::get('service-worker.js', fn () => response(
        file_get_contents(public_path('app/service-worker.js')),
        200,
        [
            'Content-Type' => 'application/javascript',
        ],
    ))->name('service-worker');
});
