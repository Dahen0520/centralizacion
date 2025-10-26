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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();

            // 1. Relación con el stock (el registro de la tabla 'inventarios')
            $table->foreignId('inventario_id')->constrained('inventarios')->onDelete('cascade');
            
            // 2. Tipo de Movimiento: Determina si es Entrada o Salida
            $table->enum('tipo_movimiento', ['ENTRADA', 'SALIDA']);
            
            // 3. Razón del Movimiento: La clave de la trazabilidad (e.g., 'Venta', 'Ingreso por Compra', 'Descarte')
            $table->string('razon'); 
            
            // 4. Cantidad del Movimiento (siempre positiva)
            $table->integer('cantidad');
            
            // 5. Referencia Opcional: Para vincularlo a otra tabla (e.g., DetalleVenta, Compra)
            $table->morphs('movible'); // Crea: movible_id (integer) y movible_type (string)

            // 6. Usuario que realizó el movimiento
            $table->foreignId('usuario_id')->constrained('users')->nullable(); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};