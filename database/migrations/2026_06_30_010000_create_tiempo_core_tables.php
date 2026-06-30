<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 80)->unique();
            $table->string('module', 60)->index();
            $table->string('action', 60)->index();
            $table->string('name', 120);
            $table->timestamps();
        });

        Schema::create('role_permissions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id', 'permission_id']);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('role_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        Schema::create('categorias', function (Blueprint $table): void {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('tipo', 40)->default('producto')->index();
            $table->string('estado', 20)->default('activo')->index();
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('negocios_afiliados', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombre_comercial');
            $table->string('slug')->unique();
            $table->string('tipo_negocio', 60)->index();
            $table->string('ruc', 20)->nullable()->index();
            $table->string('telefono', 30)->nullable()->index();
            $table->string('email')->nullable();
            $table->string('direccion')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('estado', 20)->default('activo')->index();
            $table->boolean('abierto')->default(false)->index();
            $table->json('horarios')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('productos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('negocio_afiliado_id')->constrained('negocios_afiliados')->cascadeOnDelete();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->string('nombre');
            $table->string('slug');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->decimal('precio_promocional', 10, 2)->nullable();
            $table->string('imagen')->nullable();
            $table->string('estado', 20)->default('activo')->index();
            $table->boolean('disponible')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['negocio_afiliado_id', 'slug']);
        });

        Schema::create('clientes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombres');
            $table->string('apellidos')->nullable();
            $table->string('telefono', 30)->index();
            $table->string('email')->nullable()->index();
            $table->string('documento', 30)->nullable()->index();
            $table->string('estado', 20)->default('activo')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('repartidores', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nombres');
            $table->string('apellidos')->nullable();
            $table->string('telefono', 30)->index();
            $table->string('documento', 30)->nullable()->index();
            $table->string('vehiculo_tipo', 40)->nullable();
            $table->string('vehiculo_placa', 20)->nullable();
            $table->string('estado', 20)->default('disponible')->index();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pedidos', function (Blueprint $table): void {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('negocio_afiliado_id')->constrained('negocios_afiliados')->restrictOnDelete();
            $table->foreignId('repartidor_id')->nullable()->constrained('repartidores')->nullOnDelete();
            $table->foreignId('operador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('estado', 40)->default('pendiente')->index();
            $table->string('estado_pago', 30)->default('pendiente')->index();
            $table->string('direccion_entrega');
            $table->string('referencia_entrega')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('costo_delivery', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->text('notas')->nullable();
            $table->timestamp('confirmado_at')->nullable();
            $table->timestamp('entregado_at')->nullable();
            $table->timestamp('cancelado_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pedido_detalles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->nullOnDelete();
            $table->string('producto_nombre');
            $table->unsignedInteger('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        Schema::create('pagos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('verificado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->string('metodo', 30)->index();
            $table->decimal('monto', 10, 2);
            $table->string('estado', 30)->default('pendiente')->index();
            $table->string('voucher_path')->nullable();
            $table->string('codigo_operacion')->nullable()->index();
            $table->text('observacion')->nullable();
            $table->timestamp('verificado_at')->nullable();
            $table->timestamps();
        });

        Schema::create('pedido_estados', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('estado_anterior', 40)->nullable();
            $table->string('estado_nuevo', 40)->index();
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_estados');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('pedido_detalles');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('repartidores');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('negocios_afiliados');
        Schema::dropIfExists('categorias');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
