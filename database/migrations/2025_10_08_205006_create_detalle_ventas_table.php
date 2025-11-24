<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('inventario_id')->constrained('inventarios'); 
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2); 

            $table->decimal('subtotal_base', 10, 2)->comment('Cantidad * Precio Unitario');
            $table->decimal('isv_tasa', 5, 4)->default(0.1500)->comment('Tasa ISV aplicada (ej. 0.15)');
            $table->decimal('isv_monto', 10, 2)->default(0.00)->comment('Monto ISV de esta línea');
            $table->decimal('subtotal_final', 10, 2)->default(0.00)->comment('Base + ISV (monto que suma al total final)');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
