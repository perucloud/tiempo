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
            $table->string('imagen')->nullable()->after('descripcion');
            $table->string('slogan', 120)->nullable()->after('imagen');
            $table->decimal('precio_minimo', 8, 2)->nullable()->after('slogan');
            $table->string('color_marca', 7)->nullable()->after('precio_minimo');
        });
    }

    public function down(): void
    {
        Schema::table('negocios_afiliados', function (Blueprint $table): void {
            $table->dropColumn(['imagen', 'slogan', 'precio_minimo', 'color_marca']);
        });
    }
};
