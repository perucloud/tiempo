<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_direcciones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();

            // Alias y receptor
            $table->string('alias', 50)->default('Casa'); // Casa, Trabajo, Mamá, Oficina…
            $table->string('nombre_receptor')->nullable();
            $table->string('celular_receptor', 30)->nullable();
            $table->boolean('puede_recibir_otra_persona')->default(false);
            $table->text('instrucciones')->nullable(); // "La casa verde con portón azul"

            // Dirección
            $table->string('direccion_exacta');
            $table->string('departamento', 100)->nullable();
            $table->string('urbanizacion', 150)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('region', 100)->nullable();
            $table->text('referencia')->nullable();

            // Geolocalización
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            // Preferencia
            $table->boolean('es_predeterminada')->default(false);

            $table->timestamps();
            $table->index('cliente_id');
            $table->index(['cliente_id', 'es_predeterminada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_direcciones');
    }
};
