<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('app.home', [
            'categories' => ['Pollos', 'Pizzas', 'Bodegas', 'Farmacias'],
            'businesses' => [
                [
                    'name' => 'Brasas del Centro',
                    'category' => 'Polleria afiliada',
                    'eta' => '25-35 min',
                    'status' => 'Abierto',
                ],
                [
                    'name' => 'Pizza Norte',
                    'category' => 'Pizzeria afiliada',
                    'eta' => '30-40 min',
                    'status' => 'Abierto',
                ],
            ],
            'products' => [
                [
                    'name' => 'Combo familiar',
                    'business' => 'Brasas del Centro',
                    'price' => 'S/ 59.90',
                ],
                [
                    'name' => 'Pizza americana',
                    'business' => 'Pizza Norte',
                    'price' => 'S/ 32.00',
                ],
            ],
        ]);
    }
}
