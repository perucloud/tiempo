<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\OrderCreator;
use App\Support\ShoppingCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use RuntimeException;

class OrderController extends Controller
{
    public function store(Request $request, ShoppingCart $cart, OrderCreator $creator): RedirectResponse
    {
        $data = $request->validate([
            'nombres'          => ['required', 'string', 'max:255'],
            'apellidos'        => ['nullable', 'string', 'max:255'],
            'telefono'         => ['required', 'string', 'max:30'],
            'email'            => ['nullable', 'email', 'max:255'],
            'documento'        => ['nullable', 'string', 'max:30'],
            'notas'            => ['nullable', 'string', 'max:500'],
            'latitud_cliente'  => ['required', 'numeric', 'between:-90,90'],
            'longitud_cliente' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $latitud  = isset($data['latitud_cliente'])  ? (float) $data['latitud_cliente']  : null;
        $longitud = isset($data['longitud_cliente']) ? (float) $data['longitud_cliente'] : null;

        try {
            $pedido = $creator->createFromCart($cart, $data, $latitud, $longitud);
        } catch (RuntimeException $exception) {
            return redirect()
                ->to(route('app.home').'#checkout')
                ->withErrors(['order' => $exception->getMessage()]);
        }

        return redirect()->route('app.orders.show', $pedido->codigo);
    }
}
