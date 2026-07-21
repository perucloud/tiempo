<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Support\ShoppingCart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function store(Request $request, ShoppingCart $cart): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:productos,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $product = Producto::query()
            ->whereHas('negocioAfiliado', fn ($query) => $query
                ->where('estado', \App\Models\NegocioAfiliado::ESTADO_ACTIVO)
                ->where('abierto', true))
            ->where('estado', Producto::ESTADO_ACTIVO)
            ->where('disponible', true)
            ->findOrFail($data['product_id']);

        $cart->add($product, (int) ($data['quantity'] ?? 1));

        return redirect()
            ->to(route('app.home').'#carrito')
            ->with('cart_status', 'Producto agregado al carrito.');
    }

    public function update(Request $request, ShoppingCart $cart): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:0', 'max:20'],
        ]);

        $cart->update((int) $data['product_id'], (int) $data['quantity']);

        return redirect()
            ->to(route('app.home').'#carrito')
            ->with('cart_status', 'Carrito actualizado.');
    }

    public function address(Request $request, ShoppingCart $cart): RedirectResponse
    {
        $data = $request->validate([
            'delivery_address' => ['nullable', 'string', 'max:255'],
        ]);

        $cart->setAddress($data['delivery_address'] ?? null);

        return redirect()
            ->to(route('app.home').'#carrito')
            ->with('cart_status', 'Direccion de entrega guardada.');
    }

    public function destroy(ShoppingCart $cart): RedirectResponse
    {
        $cart->clear();

        return redirect()
            ->route('app.home')
            ->with('cart_status', 'Carrito vaciado.');
    }
}
