<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            CategoriaSeeder::class,
        ]);

        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            return;
        }

        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@tiempo.test')],
            [
                'name' => env('ADMIN_NAME', 'SuperAdmin TIEMPO'),
                'password' => Hash::make($password),
                'role' => User::ROLE_SUPERADMIN,
                'role_id' => Role::query()->where('code', User::ROLE_SUPERADMIN)->value('id'),
                'status' => User::STATUS_ACTIVE,
            ],
        );
    }
}
