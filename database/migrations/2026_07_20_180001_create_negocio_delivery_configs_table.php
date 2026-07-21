<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negocio_delivery_configs', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('negocio_afiliado_id')
                ->unique()
                ->constrained('negocios_afiliados')
                ->cascadeOnDelete();

            /* Si el negocio acepta o no delivery */
            $table->boolean('permite_delivery')->default(true);

            /* Distancia máxima específica del negocio (override de zona) */
            $table->decimal('distancia_maxima_km', 5, 2)->nullable();

            /* Monto mínimo de pedido para delivery (override de zona) */
            $table->decimal('pedido_minimo', 10, 2)->nullable();

            /* Delivery gratuito a partir de este monto (override de zona) */
            $table->decimal('delivery_gratis_desde', 10, 2)->nullable();

            /* Precio base personalizado del negocio (override de zona) */
            $table->decimal('precio_base_custom', 10, 2)->nullable();

            /* Precio por km personalizado (override de zona) */
            $table->decimal('precio_por_km_custom', 10, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negocio_delivery_configs');
    }
};
