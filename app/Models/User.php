<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_OPERADOR = 'operador';

    public const ROLE_NEGOCIO_AFILIADO = 'negocio_afiliado';

    public const ROLE_REPARTIDOR = 'repartidor';

    public const ROLE_CLIENTE = 'cliente';

    public const STATUS_ACTIVE = 'activo';

    public const STATUS_INACTIVE = 'inactivo';

    public const ADMIN_ROLES = [
        self::ROLE_SUPERADMIN,
        self::ROLE_ADMIN,
        self::ROLE_OPERADOR,
        self::ROLE_NEGOCIO_AFILIADO,
    ];

    public function canAccessAdmin(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && in_array($this->role, self::ADMIN_ROLES, true);
    }

    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_SUPERADMIN => 'SuperAdmin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_OPERADOR => 'Operador',
            self::ROLE_NEGOCIO_AFILIADO => 'Negocio Afiliado',
            self::ROLE_REPARTIDOR => 'Repartidor',
            self::ROLE_CLIENTE => 'Cliente',
            default => 'Sin rol',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
