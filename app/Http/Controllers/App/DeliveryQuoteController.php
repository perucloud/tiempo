<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\DeliveryPricingService;
use App\Support\ShoppingCart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryQuoteController extends Controller
{
    public function __invoke(Request $request, ShoppingCart $cart, DeliveryPricingService $pricing): JsonResponse
    {
        $data = $request->validate([
            'latitud' => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $summary = $cart->summary();

        if ($summary['items']->isEmpty()) {
            return response()->json(['available' => false, 'message' => 'El carrito está vacío.'], 422);
        }

        $business = $summary['items']->first()['product']->negocioAfiliado;
        $quote = $pricing->calculate(
            $business,
            (float) $data['latitud'],
            (float) $data['longitud'],
            number_format((float) $summary['subtotal'], 2, '.', ''),
        );

        return response()->json(array_merge($quote->toArray(), [
            'message' => $quote->available ? 'Tarifa calculada.' : $quote->unavailableReason,
            'order_total' => number_format((float) $summary['subtotal'] + (float) $quote->finalDeliveryPrice, 2, '.', ''),
        ]), $quote->available ? 200 : 422);
    }
}
