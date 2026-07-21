<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repartidores', function (Blueprint $table): void {
            // Estado operativo granular (no reemplaza al campo estado existente)
            $table->string('estado_operativo', 30)->default('offline')->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('repartidores', function (Blueprint $table): void {
            $table->dropColumn('estado_operativo');
        });
    }
};
