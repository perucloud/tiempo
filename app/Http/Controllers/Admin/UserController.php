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
            'adminModules'  => AdminNavigation::for('usuarios'),
            'users'         => User::query()->with('roleRecord')->latest()->paginate(10),
            'roleOptions'   => User::roleOptions(),
            'statusOptions' => User::statusOptions(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'adminModules' => AdminNavigation::for('usuarios'),
            'userModel' => new User(['status' => User::STATUS_ACTIVE]),
            'roleOptions' => User::roleOptions(),
            'statusOptions' => User::statusOptions(),
            'action' => route('admin.users.store'),
            'method' => 'POST',
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['role_id'] = $this->roleIdFor($data['role']);

        User::query()->create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'adminModules' => AdminNavigation::for('usuarios'),
            'userModel' => $user,
            'roleOptions' => User::roleOptions(),
            'statusOptions' => User::statusOptions(),
            'action' => route('admin.users.update', $user),
            'method' => 'PUT',
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $data['role_id'] = $this->roleIdFor($data['role']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Usuario actualizado correctamente.');
    }

    private function roleIdFor(string $roleCode): ?int
    {
        return Role::query()
            ->where('code', $roleCode)
            ->value('id');
    }
}
