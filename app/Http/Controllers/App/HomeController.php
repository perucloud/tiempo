<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\NegocioAfiliado;
use App\Models\Producto;
use App\Support\ShoppingCart;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ShoppingCart $cart): View
    {
        /** @var \App\Models\Cliente|null $cliente */
        $cliente = Auth::guard('cliente')->user();

        return view('app.home', [
            'cliente'    => $cliente,
            'categories' => Categoria::query()
                ->where('estado', Categoria::ESTADO_ACTIVO)
                ->orderBy('orden')
                ->orderBy('nombre')
                ->limit(8)
                ->pluck('nombre'),
            'businesses' => NegocioAfiliado::query()
                ->where('estado', NegocioAfiliado::ESTADO_ACTIVO)
                ->orderByDesc('abierto')
                ->orderBy('nombre_comercial')
                ->limit(12)
                ->get(),
            'products' => Producto::query()
                ->with(['negocioAfiliado', 'categoria'])
                ->whereHas('negocioAfiliado', fn ($q) => $q
                    ->where('estado', NegocioAfiliado::ESTADO_ACTIVO)
                    ->where('abierto', true))
                ->where('estado', Producto::ESTADO_ACTIVO)
                ->where('disponible', true)
                ->orderBy('nombre')
                ->limit(8)
                ->get(),
            'cart'             => $cart->summary(),
            'direccionDefault' => $cliente?->direccionPredeterminada(),
        ]);
    }
}
