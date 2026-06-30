<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_view_users_index(): void
    {
        $admin = $this->makeUser(User::ROLE_SUPERADMIN);

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('Gestion de usuarios')
            ->assertSee('Nuevo usuario')
            ->assertSee('SuperAdmin');
    }

    public function test_admin_can_create_user_with_official_role(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Operador TIEMPO',
                'email' => 'operador@tiempo.test',
                'password' => 'secret-password',
                'role' => User::ROLE_OPERADOR,
                'status' => User::STATUS_ACTIVE,
            ])
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'email' => 'operador@tiempo.test',
            'role' => User::ROLE_OPERADOR,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    public function test_admin_can_update_user_without_changing_password(): void
    {
        $admin = $this->makeUser(User::ROLE_ADMIN);
        $user = $this->makeUser(User::ROLE_OPERADOR, 'operador@tiempo.test');
        $password = $user->password;

        $this->actingAs($admin)
            ->put("/admin/users/{$user->id}", [
                'name' => 'Operador Actualizado',
                'email' => 'operador.actualizado@tiempo.test',
                'password' => '',
                'role' => User::ROLE_OPERADOR,
                'status' => User::STATUS_INACTIVE,
            ])
            ->assertRedirect('/admin/users');

        $user->refresh();

        $this->assertSame('Operador Actualizado', $user->name);
        $this->assertSame(User::STATUS_INACTIVE, $user->status);
        $this->assertSame($password, $user->password);
    }

    public function test_operador_cannot_manage_users(): void
    {
        $operator = $this->makeUser(User::ROLE_OPERADOR);

        $this->actingAs($operator)
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_client_cannot_access_user_management(): void
    {
        $client = $this->makeUser(User::ROLE_CLIENTE);

        $this->actingAs($client)
            ->get('/admin/users')
            ->assertForbidden();
    }

    private function makeUser(string $role, string $email = 'admin@tiempo.test'): User
    {
        return User::query()->create([
            'name' => "Usuario {$role}",
            'email' => $email,
            'password' => Hash::make('secret-password'),
            'role' => $role,
            'status' => User::STATUS_ACTIVE,
        ]);
    }
}
