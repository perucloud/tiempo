@extends('layouts.admin')

@section('title', $business->exists ? 'Editar negocio' : 'Nuevo negocio')
@section('eyebrow', 'Red comercial')
@section('page-title', $business->exists ? 'Editar negocio afiliado' : 'Nuevo negocio afiliado')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>{{ $business->exists ? 'Actualizar informacion comercial' : 'Crear negocio afiliado' }}</h2>
                <p>Este modulo no entrega acceso a pedidos, pagos, clientes ni repartidores al negocio.</p>
            </div>
            <a class="admin-button" href="{{ route('admin.businesses.index') }}">Volver</a>
        </div>

        <form class="admin-form" method="POST" action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif

            <div class="admin-form-grid">
                <label class="admin-field">
                    <span>Nombre comercial</span>
                    <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial', $business->nombre_comercial) }}" required>
                    @error('nombre_comercial') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Tipo de negocio</span>
                    <select name="tipo_negocio" required>
                        @foreach ($tipoOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo_negocio', $business->tipo_negocio) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('tipo_negocio') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>RUC</span>
                    <input type="text" name="ruc" value="{{ old('ruc', $business->ruc) }}">
                    @error('ruc') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Telefono</span>
                    <input type="text" name="telefono" value="{{ old('telefono', $business->telefono) }}">
                    @error('telefono') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Email</span>
                    <input type="email" name="email" value="{{ old('email', $business->email) }}">
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Direccion</span>
                    <input type="text" name="direccion" value="{{ old('direccion', $business->direccion) }}">
                    @error('direccion') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Estado</span>
                    <select name="estado" required>
                        @foreach ($estadoOptions as $value => $label)
                            <option value="{{ $value }}" @selected(old('estado', $business->estado) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('estado') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Atencion</span>
                    <select name="abierto" required>
                        <option value="1" @selected((string) old('abierto', (int) $business->abierto) === '1')>Abierto</option>
                        <option value="0" @selected((string) old('abierto', (int) $business->abierto) === '0')>Cerrado</option>
                    </select>
                    @error('abierto') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Horarios</span>
                    <input type="text" name="horarios_texto" value="{{ old('horarios_texto', $business->horarios['general'] ?? '') }}" placeholder="Lun-Dom 09:00-22:00">
                    @error('horarios_texto') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field admin-field-wide">
                    <span>Descripcion</span>
                    <textarea name="descripcion" rows="4">{{ old('descripcion', $business->descripcion) }}</textarea>
                    @error('descripcion') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field admin-field-wide">
                    <span>Imagen (URL o ruta)</span>
                    <input type="text" name="imagen" value="{{ old('imagen', $business->imagen) }}" placeholder="https://... o /images/negocios/foto.jpg">
                    @error('imagen') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Slogan <small style="font-weight:400;color:#888">(máx. 3-4 palabras)</small></span>
                    <input type="text" name="slogan" value="{{ old('slogan', $business->slogan) }}" placeholder="Lo mejor del sabor">
                    @error('slogan') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Precio mínimo (S/)</span>
                    <input type="number" name="precio_minimo" value="{{ old('precio_minimo', $business->precio_minimo) }}" step="0.50" min="0" placeholder="15.00">
                    @error('precio_minimo') <small>{{ $message }}</small> @enderror
                </label>

                <label class="admin-field">
                    <span>Color de marca <small style="font-weight:400;color:#888">(hex)</small></span>
                    <div style="display:flex;gap:.5rem;align-items:center">
                        <input type="color" name="color_marca" value="{{ old('color_marca', $business->color_marca ?? '#CC3D00') }}" style="width:48px;height:38px;padding:2px;border-radius:6px;border:1px solid #ddd;cursor:pointer">
                        <input type="text" name="color_marca_text" value="{{ old('color_marca', $business->color_marca ?? '#CC3D00') }}" placeholder="#CC3D00" style="flex:1" oninput="this.previousElementSibling.value=this.value">
                    </div>
                    @error('color_marca') <small>{{ $message }}</small> @enderror
                </label>
            </div>

            <div class="admin-form-actions">
                <button class="admin-button admin-button-dark" type="submit">
                    {{ $business->exists ? 'Guardar cambios' : 'Crear negocio' }}
                </button>
            </div>
        </form>
    </section>
@endsection
