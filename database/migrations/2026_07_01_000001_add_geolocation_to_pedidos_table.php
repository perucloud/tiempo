<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table): void {
            $table->decimal('latitud_cliente', 10, 7)->nullable()->after('referencia_entrega');
            $table->decimal('longitud_cliente', 10, 7)->nullable()->after('latitud_cliente');
            $table->timestamp('geolocalizacion_at')->nullable()->after('longitud_cliente');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table): void {
            $table->dropColumn(['latitud_cliente', 'longitud_cliente', 'geolocalizacion_at']);
        });
    }
};
