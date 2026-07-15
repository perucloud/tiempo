<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repartidores', function (Blueprint $table): void {
            $table->decimal('latitud_actual', 10, 7)->nullable()->after('estado');
            $table->decimal('longitud_actual', 10, 7)->nullable()->after('latitud_actual');
            $table->timestamp('ubicacion_actualizada_at')->nullable()->after('longitud_actual');
        });
    }

    public function down(): void
    {
        Schema::table('repartidores', function (Blueprint $table): void {
            $table->dropColumn(['latitud_actual', 'longitud_actual', 'ubicacion_actualizada_at']);
        });
    }
};
