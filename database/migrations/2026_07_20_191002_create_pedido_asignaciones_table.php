<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_asignaciones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('repartidor_id')->constrained('repartidores')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('assignment_type', 20)->default('manual');  // manual|automatico
            $table->string('status', 20)->default('activo');           // activo|cancelado|completado

            /* Métricas repartidor → negocio */
            $table->decimal('distance_to_business_km', 6, 3)->nullable();
            $table->smallInteger('estimated_time_to_business_min')->nullable();
            $table->json('route_to_business')->nullable();             // [[lng,lat],...] GeoJSON

            /* Métricas negocio → cliente */
            $table->decimal('distance_to_customer_km', 6, 3)->nullable();
            $table->smallInteger('estimated_time_to_customer_min')->nullable();
            $table->json('route_to_customer')->nullable();             // [[lng,lat],...] GeoJSON

            $table->text('notes')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('canceled_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            /* Sin unique en pedido_id — permite reasignación (cancelar + crear nueva) */
            $table->index(['pedido_id', 'status']);
            $table->index(['repartidor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_asignaciones');
    }
};
