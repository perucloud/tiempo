<?php

use App\Http\Controllers\App\CartController;
use App\Http\Controllers\App\HomeController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->name('app.')->group(function (): void {
    Route::get('/', HomeController::class)->name('home');
    Route::post('cart', [CartController::class, 'store'])->name('cart.store');
    Route::patch('cart', [CartController::class, 'update'])->name('cart.update');
    Route::patch('cart/address', [CartController::class, 'address'])->name('cart.address');
    Route::delete('cart', [CartController::class, 'destroy'])->name('cart.destroy');
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
