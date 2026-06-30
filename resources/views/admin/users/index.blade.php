@extends('layouts.admin')

@section('title', 'Usuarios')
@section('eyebrow', 'Seguridad y accesos')
@section('page-title', 'Gestion de usuarios')

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-header">
            <div>
                <h2>Usuarios del sistema</h2>
                <p>Administra accesos para SuperAdmin, Admin, Operador, Negocio Afiliado, Repartidor y Cliente.</p>
            </div>
            <a class="admin-button admin-button-dark" href="{{ route('admin.users.create') }}">Nuevo usuario</a>
        </div>

        @if (session('status'))
            <div class="admin-alert">{{ session('status') }}</div>
        @endif

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Creado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->roleLabel() }}</td>
                            <td>
                                <span class="admin-badge {{ $user->status === 'activo' ? 'admin-badge-green' : 'admin-badge-red' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                            <td>
                                <a class="admin-link" href="{{ route('admin.users.edit', $user) }}">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            {{ $users->links() }}
        </div>
    </section>
@endsection
