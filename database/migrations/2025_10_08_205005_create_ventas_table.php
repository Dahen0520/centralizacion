<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('tienda_id')->constrained('tiendas');
            
            $table->foreignId('cliente_id')
                  ->nullable() 
                  ->constrained('clientes');
            
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('tipo_documento', 20)->default('TICKET');
            $table->enum('tipo_pago', ['EFECTIVO', 'TARJETA', 'TRANSFERENCIA', 'OTRO'])->nullable(); 
            $table->string('cai', 100)->nullable();
            $table->string('numero_documento', 50)->nullable(); 
            $table->string('estado', 20)->default('COMPLETADA'); 
            $table->decimal('descuento', 10, 2)->default(0.00);

            // Subtotales Base (antes de ISV)
            $table->decimal('subtotal_neto', 10, 2)->default(0.00)->comment('Suma de gravado + exonerado');
            $table->decimal('subtotal_gravado', 10, 2)->default(0.00)->comment('Suma de bases imponibles');
            $table->decimal('subtotal_exonerado', 10, 2)->default(0.00)->comment('Suma de bases exoneradas');
            
            // Impuesto y Total Final
            $table->decimal('total_isv', 10, 2)->default(0.00);
            $table->decimal('total_final', 10, 2)->default(0.00)->comment('Monto a pagar (Neto - Descuento + ISV)');
            
            $table->decimal('total_venta', 10, 2)->default(0.00); 
            $table->timestamp('fecha_venta');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};