<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\ClienteDireccion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DireccionController extends Controller
{
    public function index(): JsonResponse
    {
        $cliente = Auth::guard('cliente')->user();

        return response()->json([
            'direcciones' => $cliente->direcciones()->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateDireccion($request);

        /** @var \App\Models\Cliente $cliente */
        $cliente = Auth::guard('cliente')->user();

        if ($data['es_predeterminada'] ?? false) {
            $cliente->direcciones()->update(['es_predeterminada' => false]);
        }

        $direccion = $cliente->direcciones()->create($data);

        return response()->json(['message' => 'Dirección guardada.', 'direccion' => $direccion], 201);
    }

    public function update(Request $request, ClienteDireccion $direccion): JsonResponse
    {
        $this->authorizeOwnership($direccion);
        $data = $this->validateDireccion($request, $direccion->id);

        if ($data['es_predeterminada'] ?? false) {
            Auth::guard('cliente')->user()->direcciones()->where('id', '!=', $direccion->id)->update(['es_predeterminada' => false]);
        }

        $direccion->update($data);

        return response()->json(['message' => 'Dirección actualizada.', 'direccion' => $direccion->fresh()]);
    }

    public function destroy(ClienteDireccion $direccion): JsonResponse
    {
        $this->authorizeOwnership($direccion);
        $direccion->delete();

        return response()->json(['message' => 'Dirección eliminada.']);
    }

    public function setPredeterminada(ClienteDireccion $direccion): JsonResponse
    {
        $this->authorizeOwnership($direccion);

        Auth::guard('cliente')->user()->direcciones()->update(['es_predeterminada' => false]);
        $direccion->update(['es_predeterminada' => true]);

        return response()->json(['message' => 'Dirección predeterminada actualizada.']);
    }

    private function validateDireccion(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'alias'                      => ['required', 'string', 'max:50'],
            'nombre_receptor'            => ['nullable', 'string', 'max:150'],
            'celular_receptor'           => ['nullable', 'string', 'max:30'],
            'puede_recibir_otra_persona' => ['boolean'],
            'instrucciones'              => ['nullable', 'string', 'max:500'],
            'direccion_exacta'           => ['required', 'string', 'max:255'],
            'departamento'               => ['nullable', 'string', 'max:100'],
            'urbanizacion'               => ['nullable', 'string', 'max:150'],
            'distrito'                   => ['nullable', 'string', 'max:100'],
            'provincia'                  => ['nullable', 'string', 'max:100'],
            'region'                     => ['nullable', 'string', 'max:100'],
            'referencia'                 => ['nullable', 'string', 'max:500'],
            'latitud'                    => ['nullable', 'numeric', 'between:-90,90'],
            'longitud'                   => ['nullable', 'numeric', 'between:-180,180'],
            'es_predeterminada'          => ['boolean'],
        ]);
    }

    private function authorizeOwnership(ClienteDireccion $direccion): void
    {
        abort_unless(
            $direccion->cliente_id === Auth::guard('cliente')->id(),
            403,
            'No puedes modificar esta dirección.',
        );
    }
}
