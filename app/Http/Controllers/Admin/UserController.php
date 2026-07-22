<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminNavigation;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'adminModules'     => AdminNavigation::for(auth()->user(), 'usuarios'),
            'users'            => User::query()->with('roleRecord')->latest()->paginate(10),
            'roleOptions'      => User::roleOptions(),
            'statusOptions'    => User::statusOptions(),
            'assignableModules'=> AdminNavigation::assignableList(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'adminModules'     => AdminNavigation::for(auth()->user(), 'usuarios'),
            'userModel'        => new User(['status' => User::STATUS_ACTIVE]),
            'roleOptions'      => User::roleOptions(),
            'statusOptions'    => User::statusOptions(),
            'assignableModules'=> AdminNavigation::assignableList(),
            'action'           => route('admin.users.store'),
            'method'           => 'POST',
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data            = $request->validated();
        $data['role_id'] = $this->roleIdFor($data['role']);

        // SuperAdmin recibe null (acceso total); otros reciben el array seleccionado
        $data['module_permissions'] = $data['role'] === User::ROLE_SUPERADMIN
            ? null
            : ($data['module_permissions'] ?? []);

        User::query()->create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'adminModules'     => AdminNavigation::for(auth()->user(), 'usuarios'),
            'userModel'        => $user,
            'roleOptions'      => User::roleOptions(),
            'statusOptions'    => User::statusOptions(),
            'assignableModules'=> AdminNavigation::assignableList(),
            'action'           => route('admin.users.update', $user),
            'method'           => 'PUT',
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data            = $request->validated();
        $data['role_id'] = $this->roleIdFor($data['role']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $data['module_permissions'] = $data['role'] === User::ROLE_SUPERADMIN
            ? null
            : ($data['module_permissions'] ?? []);

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        // El superadmin primario no puede ser bloqueado
        if ($user->isPrimary()) {
            return redirect()
                ->route('admin.users.index')
                ->with('status_error', 'El SuperAdmin principal no puede ser bloqueado.');
        }

        $user->update([
            'status' => $user->status === User::STATUS_ACTIVE
                ? User::STATUS_INACTIVE
                : User::STATUS_ACTIVE,
        ]);

        $label = $user->fresh()->status === User::STATUS_ACTIVE ? 'activado' : 'bloqueado';

        return redirect()
            ->route('admin.users.index')
            ->with('status', "Usuario {$label} correctamente.");
    }

    public function destroy(User $user): RedirectResponse
    {
        // El superadmin primario no puede ser eliminado jamás
        if ($user->isPrimary()) {
            return redirect()
                ->route('admin.users.index')
                ->with('status_error', 'El SuperAdmin principal no puede ser eliminado.');
        }

        // Nadie puede eliminarse a sí mismo
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('admin.users.index')
                ->with('status_error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario eliminado correctamente.');
    }

    private function roleIdFor(string $roleCode): ?int
    {
        return Role::query()
            ->where('code', $roleCode)
            ->value('id');
    }
}
