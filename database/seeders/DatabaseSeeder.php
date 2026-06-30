<?php

namespace Database\Seeders;

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
                'status' => User::STATUS_ACTIVE,
            ],
        );
    }
}
