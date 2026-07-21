<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('repartidor_ubicaciones', function (Blueprint $table): void {
            $table->float('precision_gps')->nullable()->comment('Precisión GPS en metros')->after('longitud');
        });
    }

    public function down(): void
    {
        Schema::table('repartidor_ubicaciones', function (Blueprint $table): void {
            $table->dropColumn('precision_gps');
        });
    }
};
