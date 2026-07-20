<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\NegocioAfiliado;
use Illuminate\View\View;

class BusinessController extends Controller
{
    public function show(NegocioAfiliado $negocio): View
    {
        $productos = $negocio->productos()
            ->where('estado', 'activo')
            ->where('disponible', true)
            ->with('categoria')
            ->orderBy('nombre')
            ->get();

        return view('app.business', compact('negocio', 'productos'));
    }
}
