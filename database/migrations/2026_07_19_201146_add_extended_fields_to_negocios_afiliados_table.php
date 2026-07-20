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
            /* Paso 1 — horarios estructurados + preparación */
            $table->time('hora_apertura')->nullable()->after('horarios');
            $table->time('hora_cierre')->nullable()->after('hora_apertura');
            $table->unsignedSmallInteger('tiempo_preparacion')->nullable()->after('hora_cierre')->comment('minutos');

            /* Paso 3 — ubicación */
            $table->string('departamento', 80)->nullable()->after('direccion');
            $table->string('provincia', 80)->nullable()->after('departamento');
            $table->string('distrito', 80)->nullable()->after('provincia');
            $table->string('referencia')->nullable()->after('distrito');
            $table->decimal('latitud', 10, 8)->nullable()->after('referencia');
            $table->decimal('longitud', 11, 8)->nullable()->after('latitud');

            /* Paso 3 — contacto extendido */
            $table->string('celular', 20)->nullable()->after('telefono');
            $table->string('whatsapp', 20)->nullable()->after('celular');
            $table->string('telefono_fijo', 20)->nullable()->after('whatsapp');
            $table->string('pagina_web')->nullable()->after('email');

            /* Paso 4 — redes sociales */
            $table->string('facebook')->nullable()->after('pagina_web');
            $table->string('instagram')->nullable()->after('facebook');
            $table->string('tiktok')->nullable()->after('instagram');
        });
    }

    public function down(): void
    {
        Schema::table('negocios_afiliados', function (Blueprint $table): void {
            $table->dropColumn([
                'hora_apertura', 'hora_cierre', 'tiempo_preparacion',
                'departamento', 'provincia', 'distrito', 'referencia', 'latitud', 'longitud',
                'celular', 'whatsapp', 'telefono_fijo', 'pagina_web',
                'facebook', 'instagram', 'tiktok',
            ]);
        });
    }
};
