<?php

use App\Http\Controllers\App\Auth\ForgotPasswordController;
use App\Http\Controllers\App\Auth\LoginController;
use App\Http\Controllers\App\Auth\RegisterController;
use App\Http\Controllers\App\BusinessController;
use App\Http\Controllers\App\CartController;
use App\Http\Controllers\App\CustomerAccessController;
use App\Http\Controllers\App\DeliveryQuoteController;
use App\Http\Controllers\App\DireccionController;
use App\Http\Controllers\App\HomeController;
use App\Http\Controllers\App\OrderController;
use App\Http\Controllers\App\OrderTrackingController;
use App\Http\Controllers\App\PaymentController;
use App\Http\Controllers\App\PerfilController;
use App\Http\Controllers\PushSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::prefix('app')->name('app.')->group(function (): void {

    /* ── PWA assets (siempre públicos) ── */
    Route::get('manifest.webmanifest', fn () => response(
        file_get_contents(public_path('app/manifest.webmanifest')), 200,
        ['Content-Type' => 'application/manifest+json'],
    ))->name('manifest');

    Route::get('service-worker.js', fn () => response(
        file_get_contents(public_path('app/service-worker.js')), 200,
        ['Content-Type' => 'application/javascript'],
    ))->name('service-worker');

    /* ── Páginas públicas (negocios, carta) ── */
    Route::get('negocios/{negocio:slug}', [BusinessController::class, 'show'])->name('negocio.show');
    Route::get('pedidos/{codigo}', [OrderTrackingController::class, 'show'])->middleware('throttle:30,1')->name('orders.show');
    Route::get('pedidos/{codigo}/estado', [OrderTrackingController::class, 'estado'])->middleware('throttle:60,1')->name('orders.estado');

    /* ── Solo invitados (redirige a home si ya está logueado) ── */
    Route::middleware('guest:cliente')->group(function (): void {
        Route::get('/', [LoginController::class, 'showWelcome'])->name('home');
        Route::get('login', [LoginController::class, 'showLogin'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->middleware('throttle:10,1')->name('login.post');
        Route::get('registro', [RegisterController::class, 'show'])->name('registro');
        Route::post('registro', [RegisterController::class, 'register'])->middleware('throttle:5,1')->name('registro.post');
        Route::get('recuperar', [ForgotPasswordController::class, 'show'])->name('recuperar');
        Route::post('recuperar/codigo', [ForgotPasswordController::class, 'sendCode'])->middleware('throttle:3,10')->name('recuperar.codigo');
        Route::post('recuperar/verificar', [ForgotPasswordController::class, 'verify'])->middleware('throttle:10,10')->name('recuperar.verificar');
        Route::post('recuperar/password', [ForgotPasswordController::class, 'reset'])->middleware('throttle:5,10')->name('recuperar.reset');
    });

    /* ── Requieren cliente autenticado ── */
    Route::middleware('cliente.auth')->group(function (): void {
        // Home (negocios + buscar)
        Route::get('inicio', HomeController::class)->name('inicio');

        // Logout
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        // Carrito
        Route::post('cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch('cart', [CartController::class, 'update'])->name('cart.update');
        Route::patch('cart/address', [CartController::class, 'address'])->name('cart.address');
        Route::delete('cart', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::post('delivery/quote', DeliveryQuoteController::class)->middleware('throttle:20,1')->name('delivery.quote');

        // Pedidos
        Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

        // Pagos
        Route::post('payments', [PaymentController::class, 'store'])->middleware('throttle:5,1')->name('payments.store');

        // Perfil
        Route::get('perfil', [PerfilController::class, 'show'])->name('perfil');
        Route::put('perfil', [PerfilController::class, 'update'])->name('perfil.update');
        Route::put('perfil/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');
        Route::post('perfil/foto', [PerfilController::class, 'updateFoto'])->name('perfil.foto');
        Route::get('perfil/pedidos', [PerfilController::class, 'pedidos'])->name('perfil.pedidos');

        // Direcciones
        Route::get('direcciones', [DireccionController::class, 'index'])->name('direcciones.index');
        Route::post('direcciones', [DireccionController::class, 'store'])->name('direcciones.store');
        Route::put('direcciones/{direccion}', [DireccionController::class, 'update'])->name('direcciones.update');
        Route::delete('direcciones/{direccion}', [DireccionController::class, 'destroy'])->name('direcciones.destroy');
        Route::patch('direcciones/{direccion}/predeterminada', [DireccionController::class, 'setPredeterminada'])->name('direcciones.predeterminada');

        // Push notifications
        Route::post('push/subscribe', [PushSubscriptionController::class, 'customer'])->middleware('throttle:10,1')->name('push.subscribe');

        // OTP legacy — perfil ya autenticado busca por sesión
        Route::post('perfil/buscar', [OrderTrackingController::class, 'buscarPorTelefono'])->middleware('throttle:10,1')->name('perfil.buscar');
    });

    /* ── OTP para recuperar contraseña (públicos con rate limit) ── */
    Route::post('perfil/codigo', [CustomerAccessController::class, 'requestCode'])->middleware('throttle:3,10')->name('perfil.codigo');
    Route::post('perfil/verificar', [CustomerAccessController::class, 'verifyCode'])->middleware('throttle:10,10')->name('perfil.verificar');
});
