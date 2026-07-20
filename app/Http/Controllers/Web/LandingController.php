<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NegocioAfiliado;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(): View
    {
        $businesses = NegocioAfiliado::where('estado', NegocioAfiliado::ESTADO_ACTIVO)
            ->orderBy('nombre_comercial')
            ->take(14)
            ->get();

        return view('web.landing', compact('businesses'));
    }
}
