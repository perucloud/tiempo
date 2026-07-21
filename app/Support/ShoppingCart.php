<?php

namespace App\Support;

use App\Models\Producto;
use Illuminate\Support\Collection;

class ShoppingCart
{
    private const SESSION_KEY = 'app_cart';

    public function add(Producto $product, int $quantity = 1): void
    {
        $cart = $this->raw();

        if (($cart['business_id'] ?? null) !== null && (int) $cart['business_id'] !== $product->negocio_afiliado_id) {
            $cart = $this->emptyCart();
        }

        $items = $cart['items'] ?? [];
        $productId = (string) $product->id;
        $items[$productId] = [
            'product_id' => $product->id,
            'quantity' => min(20, max(1, ($items[$productId]['quantity'] ?? 0) + $quantity)),
        ];

        $cart['business_id'] = $product->negocio_afiliado_id;
        $cart['items'] = $items;

        session()->put(self::SESSION_KEY, $cart);
    }

    public function update(int $productId, int $quantity): void
    {
        $cart = $this->raw();
        $items = $cart['items'] ?? [];
        $key = (string) $productId;

        if ($quantity <= 0) {
            unset($items[$key]);
        } elseif (isset($items[$key])) {
            $items[$key]['quantity'] = min(20, $quantity);
        }

        $cart['items'] = $items;

        if ($items === []) {
            $cart['business_id'] = null;
        }

        session()->put(self::SESSION_KEY, $cart);
    }

    public function setAddress(?string $address): void
    {
        $cart = $this->raw();
        $cart['delivery_address'] = trim((string) $address);

        session()->put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function summary(): array
    {
        $cart = $this->raw();
        $productIds = array_keys($cart['items'] ?? []);
        $products = Producto::query()
            ->with('negocioAfiliado')
            ->whereHas('negocioAfiliado', fn ($query) => $query
                ->where('estado', \App\Models\NegocioAfiliado::ESTADO_ACTIVO)
                ->where('abierto', true))
            ->where('estado', Producto::ESTADO_ACTIVO)
            ->where('disponible', true)
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $items = collect($cart['items'] ?? [])
            ->map(function (array $item) use ($products): ?array {
                $product = $products->get($item['product_id']);

                if (! $product) {
                    return null;
                }

                $unitPrice = (float) ($product->precio_promocional ?: $product->precio);
                $quantity = (int) $item['quantity'];

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $unitPrice * $quantity,
                ];
            })
            ->filter()
            ->values();

        $subtotal = $items->sum('subtotal');
        $delivery = 0.0;

        return [
            'items' => $items,
            'count' => $items->sum('quantity'),
            'subtotal' => $subtotal,
            'delivery' => $delivery,
            'total' => $subtotal + $delivery,
            'business_id' => $cart['business_id'] ?? null,
            'business_name' => $this->businessName($items),
            'delivery_address' => $cart['delivery_address'] ?? '',
        ];
    }

    private function raw(): array
    {
        return session(self::SESSION_KEY, $this->emptyCart());
    }

    private function emptyCart(): array
    {
        return [
            'business_id' => null,
            'items' => [],
            'delivery_address' => '',
        ];
    }

    private function businessName(Collection $items): ?string
    {
        $first = $items->first();

        return $first ? $first['product']->negocioAfiliado?->nombre_comercial : null;
    }
}
