<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sistema_configuraciones', function (Blueprint $table): void {
            $table->id();
            $table->string('clave')->unique();
            $table->string('grupo', 80)->default('general');
            $table->string('etiqueta', 160);
            $table->text('valor')->nullable();
            $table->string('tipo', 40)->default('string');
            $table->boolean('editable')->default(true);
            $table->timestamps();
        });

        Schema::create('zonas_delivery', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion_cobertura')->nullable();
            $table->decimal('costo_delivery', 10, 2)->default(0);
            $table->decimal('pedido_minimo', 10, 2)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('configuracion_auditorias', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('entidad', 80);
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->string('accion', 80);
            $table->json('cambios')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_auditorias');
        Schema::dropIfExists('zonas_delivery');
        Schema::dropIfExists('sistema_configuraciones');
    }
};
