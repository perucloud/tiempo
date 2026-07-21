<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table): void {
            /* FK a la zona aplicada en el momento del pedido */
            $table->foreignId('zona_delivery_id')
                ->nullable()
                ->after('longitud_cliente')
                ->constrained('zonas_delivery')
                ->nullOnDelete();

            /* Distancia vial real calculada por OSRM (3 decimales = metros exactos) */
            $table->decimal('distance_km', 6, 3)->nullable()->after('zona_delivery_id');

            /* Duración estimada de la ruta según OSRM */
            $table->unsignedSmallInteger('delivery_duration_minutes')->nullable()->after('distance_km');

            /* Snapshot inmutable del cálculo de tarifa en el momento del pedido.
               Permite auditar sin que futuros cambios de tarifas afecten pedidos históricos. */
            $table->json('delivery_pricing_snapshot')->nullable()->after('delivery_duration_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table): void {
            $table->dropForeign(['zona_delivery_id']);
            $table->dropColumn([
                'zona_delivery_id',
                'distance_km',
                'delivery_duration_minutes',
                'delivery_pricing_snapshot',
            ]);
        });
    }
};
