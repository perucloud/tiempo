<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zonas_delivery', function (Blueprint $table): void {
            /* Geometría: array JSON [[lng, lat], ...] en orden GeoJSON,
               compatible con Leaflet y con OSRM */
            $table->json('polygon')->nullable()->after('descripcion_cobertura');

            /* Tarificación por distancia */
            $table->decimal('km_incluidos', 5, 2)->default(0)->after('costo_delivery');
            $table->decimal('precio_por_km_extra', 10, 2)->default(0)->after('km_incluidos');

            /* Delivery gratis a partir de cierto monto del pedido */
            $table->decimal('delivery_gratis_desde', 10, 2)->nullable()->after('precio_por_km_extra');

            /* Recargo adicional de la zona (ej: zona nocturna, zona difícil) */
            $table->decimal('recargo', 10, 2)->default(0)->after('delivery_gratis_desde');

            /* Distancia máxima de delivery para esta zona */
            $table->decimal('distancia_maxima_km', 5, 2)->nullable()->after('recargo');

            /* Tiempos estimados de entrega (en minutos) */
            $table->unsignedSmallInteger('tiempo_estimado_min')->nullable()->after('distancia_maxima_km');
            $table->unsignedSmallInteger('tiempo_estimado_max')->nullable()->after('tiempo_estimado_min');

            /* Prioridad para resolución de superposición de zonas (menor = más prioridad) */
            $table->unsignedTinyInteger('prioridad')->default(10)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('zonas_delivery', function (Blueprint $table): void {
            $table->dropColumn([
                'polygon',
                'km_incluidos',
                'precio_por_km_extra',
                'delivery_gratis_desde',
                'recargo',
                'distancia_maxima_km',
                'tiempo_estimado_min',
                'tiempo_estimado_max',
                'prioridad',
            ]);
        });
    }
};
