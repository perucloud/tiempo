<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table): void {
            // Identificación única
            $table->string('codigo_cliente', 20)->nullable()->unique()->after('id');

            // Autenticación
            $table->string('password')->nullable()->after('email');
            $table->rememberToken()->after('password');
            $table->timestamp('email_verified_at')->nullable()->after('remember_token');

            // Datos personales extendidos
            $table->string('tipo_documento', 20)->nullable()->after('documento'); // DNI, CE
            $table->date('fecha_nacimiento')->nullable()->after('tipo_documento');
            $table->string('sexo', 30)->nullable()->after('fecha_nacimiento');
            $table->string('whatsapp', 30)->nullable()->after('telefono');

            // Foto y preferencias
            $table->string('foto_perfil')->nullable()->after('estado');
            $table->string('idioma', 10)->default('es')->after('foto_perfil');
            $table->boolean('recibir_promociones')->default(true)->after('idioma');
            $table->boolean('recibir_push')->default(true)->after('recibir_promociones');
            $table->boolean('recibir_whatsapp')->default(false)->after('recibir_push');
            $table->boolean('recibir_email')->default(true)->after('recibir_whatsapp');
            $table->string('preferencia_pago', 30)->nullable()->after('recibir_email');

            // Métricas automáticas
            $table->unsignedInteger('total_pedidos')->default(0)->after('preferencia_pago');
            $table->decimal('total_gastado', 10, 2)->default('0.00')->after('total_pedidos');
            $table->timestamp('ultimo_pedido_at')->nullable()->after('total_gastado');
            $table->timestamp('ultimo_acceso')->nullable()->after('ultimo_pedido_at');
            $table->string('ip_ultimo_acceso', 45)->nullable()->after('ultimo_acceso');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table): void {
            $table->dropColumn([
                'codigo_cliente', 'password', 'remember_token', 'email_verified_at',
                'tipo_documento', 'fecha_nacimiento', 'sexo', 'whatsapp',
                'foto_perfil', 'idioma', 'recibir_promociones', 'recibir_push',
                'recibir_whatsapp', 'recibir_email', 'preferencia_pago',
                'total_pedidos', 'total_gastado', 'ultimo_pedido_at',
                'ultimo_acceso', 'ip_ultimo_acceso',
            ]);
        });
    }
};
