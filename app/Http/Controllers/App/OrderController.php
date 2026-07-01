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
            'nombres' => ['required', 'string', 'max:255'],
            'apellidos' => ['nullable', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'documento' => ['nullable', 'string', 'max:30'],
            'notas' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $pedido = $creator->createFromCart($cart, $data);
        } catch (RuntimeException $exception) {
            return redirect()
                ->to(route('app.home').'#checkout')
                ->withErrors(['order' => $exception->getMessage()]);
        }

        return redirect()
            ->to(route('app.home').'#pedidos')
            ->with('order_status', "Pedido {$pedido->codigo} creado correctamente.");
    }
}
