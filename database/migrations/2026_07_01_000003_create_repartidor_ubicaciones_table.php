<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repartidor_ubicaciones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('repartidor_id')->constrained('repartidores')->cascadeOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            $table->timestamp('created_at')->useCurrent();

            $table->index('repartidor_id');
            $table->index('pedido_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repartidor_ubicaciones');
    }
};
