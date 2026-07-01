<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table): void {
            $table->id();
            $table->string('tipo', 80);
            $table->string('canal', 40)->default('interno');
            $table->string('destinatario_tipo', 40);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('cliente_id')->nullable()->constrained('clientes')->nullOnDelete();
            $table->foreignId('repartidor_id')->nullable()->constrained('repartidores')->nullOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->foreignId('pago_id')->nullable()->constrained('pagos')->nullOnDelete();
            $table->string('titulo', 160);
            $table->text('mensaje');
            $table->json('data')->nullable();
            $table->timestamp('leido_at')->nullable();
            $table->timestamps();

            $table->index(['destinatario_tipo', 'leido_at']);
            $table->index(['tipo', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
