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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            
            // Claves Foráneas (Actualizado)
            $table->foreignId('tienda_id')->constrained('tiendas');
            
            // NUEVA COLUMNA: cliente_id (OPCIONAL)
            // Se asocia a la nueva tabla 'clientes'
            $table->foreignId('cliente_id')
                  ->nullable() // Permite ventas genéricas (sin cliente asignado)
                  ->constrained('clientes');
            
            $table->foreignId('usuario_id')->constrained('users');

            // Datos de la Venta
            $table->decimal('total_venta', 10, 2);
            $table->timestamp('fecha_venta');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};