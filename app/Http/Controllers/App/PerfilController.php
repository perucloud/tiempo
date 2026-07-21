<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function show(): View
    {
        $cliente = Auth::guard('cliente')->user();

        return view('app.perfil', [
            'cliente'     => $cliente->load('direcciones'),
            'pedidos'     => $cliente->pedidos()->with('negocioAfiliado')->latest()->limit(10)->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var \App\Models\Cliente $cliente */
        $cliente = Auth::guard('cliente')->user();

        $data = $request->validate([
            'nombres'          => ['required', 'string', 'max:150'],
            'apellidos'        => ['nullable', 'string', 'max:150'],
            'email'            => ['nullable', 'email', 'max:255', 'unique:clientes,email,' . $cliente->id],
            'whatsapp'         => ['nullable', 'string', 'max:30'],
            'tipo_documento'   => ['nullable', 'in:DNI,CE'],
            'documento'        => ['nullable', 'string', 'max:30'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'sexo'             => ['nullable', 'in:masculino,femenino,otro,prefiero_no_decir'],
            'idioma'           => ['nullable', 'in:es,en'],
            'recibir_promociones' => ['boolean'],
            'recibir_push'        => ['boolean'],
            'recibir_whatsapp'    => ['boolean'],
            'recibir_email'       => ['boolean'],
            'preferencia_pago'    => ['nullable', 'in:yape,plin,tarjeta,efectivo'],
        ]);

        $cliente->update($data);

        return back()->with('perfil_ok', 'Perfil actualizado correctamente.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var \App\Models\Cliente $cliente */
        $cliente = Auth::guard('cliente')->user();

        $request->validate([
            'password_actual'   => ['required', 'string'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($request->password_actual, $cliente->password)) {
            return back()->withErrors(['password_actual' => 'La contraseña actual no es correcta.']);
        }

        $cliente->update(['password' => $request->password]);

        return back()->with('perfil_ok', 'Contraseña actualizada.');
    }

    public function updateFoto(Request $request): RedirectResponse
    {
        $request->validate([
            'foto' => ['required', 'image', 'max:2048'],
        ]);

        /** @var \App\Models\Cliente $cliente */
        $cliente = Auth::guard('cliente')->user();
        $path = $request->file('foto')->store('clientes/fotos', 'public');
        $cliente->update(['foto_perfil' => $path]);

        return back()->with('perfil_ok', 'Foto de perfil actualizada.');
    }

    public function pedidos(): JsonResponse
    {
        $cliente = Auth::guard('cliente')->user();
        $pedidos = $cliente->pedidos()
            ->with('negocioAfiliado')
            ->latest()
            ->paginate(10);

        return response()->json($pedidos);
    }
}
