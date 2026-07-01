<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table): void {
            $table->index(['estado', 'disponible', 'nombre'], 'productos_estado_disponible_nombre_idx');
        });

        Schema::table('pedidos', function (Blueprint $table): void {
            $table->index(['created_at', 'estado'], 'pedidos_created_estado_idx');
            $table->index(['negocio_afiliado_id', 'estado', 'created_at'], 'pedidos_negocio_estado_created_idx');
            $table->index(['repartidor_id', 'estado', 'created_at'], 'pedidos_repartidor_estado_created_idx');
        });

        Schema::table('pagos', function (Blueprint $table): void {
            $table->index(['created_at', 'estado'], 'pagos_created_estado_idx');
            $table->index(['metodo', 'estado', 'created_at'], 'pagos_metodo_estado_created_idx');
        });

        Schema::table('notificaciones', function (Blueprint $table): void {
            $table->index(['destinatario_tipo', 'tipo', 'created_at'], 'notificaciones_dest_tipo_created_idx');
        });

        Schema::table('zonas_delivery', function (Blueprint $table): void {
            $table->index(['activo', 'nombre'], 'zonas_delivery_activo_nombre_idx');
        });

        Schema::table('configuracion_auditorias', function (Blueprint $table): void {
            $table->index(['entidad', 'created_at'], 'config_auditorias_entidad_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_auditorias', function (Blueprint $table): void {
            $table->dropIndex('config_auditorias_entidad_created_idx');
        });

        Schema::table('zonas_delivery', function (Blueprint $table): void {
            $table->dropIndex('zonas_delivery_activo_nombre_idx');
        });

        Schema::table('notificaciones', function (Blueprint $table): void {
            $table->dropIndex('notificaciones_dest_tipo_created_idx');
        });

        Schema::table('pagos', function (Blueprint $table): void {
            $table->dropIndex('pagos_created_estado_idx');
            $table->dropIndex('pagos_metodo_estado_created_idx');
        });

        Schema::table('pedidos', function (Blueprint $table): void {
            $table->dropIndex('pedidos_created_estado_idx');
            $table->dropIndex('pedidos_negocio_estado_created_idx');
            $table->dropIndex('pedidos_repartidor_estado_created_idx');
        });

        Schema::table('productos', function (Blueprint $table): void {
            $table->dropIndex('productos_estado_disponible_nombre_idx');
        });
    }
};
