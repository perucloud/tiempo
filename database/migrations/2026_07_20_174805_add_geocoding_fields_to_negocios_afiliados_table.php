<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('negocios_afiliados', function (Blueprint $table): void {
            $table->string('codigo_postal', 10)->nullable()->after('distrito');
            $table->string('pais', 80)->nullable()->default('Perú')->after('codigo_postal');
        });
    }

    public function down(): void
    {
        Schema::table('negocios_afiliados', function (Blueprint $table): void {
            $table->dropColumn(['codigo_postal', 'pais']);
        });
    }
};
