<?php

namespace App\Observers;

use App\Models\Cliente;
use Illuminate\Support\Str;

class ClienteObserver
{
    public function creating(Cliente $cliente): void
    {
        if (empty($cliente->codigo_cliente)) {
            $cliente->codigo_cliente = $this->generateCodigo();
        }
    }

    private function generateCodigo(): string
    {
        do {
            $next  = (Cliente::withTrashed()->max('id') ?? 0) + 1;
            $codigo = 'CLI-' . str_pad($next, 6, '0', STR_PAD_LEFT);
        } while (Cliente::withTrashed()->where('codigo_cliente', $codigo)->exists());

        return $codigo;
    }
}
