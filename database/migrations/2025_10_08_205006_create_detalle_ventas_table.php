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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            
            // ===================================
            // CLAVES FORÁNEAS
            // ===================================
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('inventario_id')->constrained('inventarios'); 
            
            // ===================================
            // CANTIDAD Y PRECIO BASE
            // ===================================
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2); // Precio base unitario (sin ISV)

            // ===================================
            // DESGLOSE FISCAL POR LÍNEA
            // ===================================
            $table->decimal('subtotal_base', 10, 2)->comment('Cantidad * Precio Unitario');
            $table->decimal('isv_tasa', 5, 4)->default(0.1500)->comment('Tasa ISV aplicada (ej. 0.15)');
            $table->decimal('isv_monto', 10, 2)->default(0.00)->comment('Monto ISV de esta línea');
            $table->decimal('subtotal_final', 10, 2)->default(0.00)->comment('Base + ISV (monto que suma al total final)');
            
            // El campo 'subtotal' original no es necesario si usamos 'subtotal_final',
            // pero lo eliminamos para evitar duplicidad y confusiones.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
