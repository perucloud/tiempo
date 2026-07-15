<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Repartidor;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function show(Repartidor $repartidor): View
    {
        abort_if(
            $repartidor->estado === Repartidor::ESTADO_INACTIVO,
            403,
            'Este repartidor no está activo en el sistema.'
        );

        return view('courier.turno', compact('repartidor'));
    }
}
