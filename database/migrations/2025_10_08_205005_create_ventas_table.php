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
            
            // ===================================
            // CLAVES FORÁNEAS
            // ===================================
            $table->foreignId('tienda_id')->constrained('tiendas');
            
            // Cliente (ID opcional para ventas genéricas)
            $table->foreignId('cliente_id')
                  ->nullable() 
                  ->constrained('clientes');
            
            $table->foreignId('usuario_id')->constrained('users');

            // ===================================
            // CAMPOS DE FACTURACIÓN (SAR)
            // ===================================
            
            // Tipo de Documento: TICKET, QUOTE, INVOICE (solo Factura usa CAI)
            $table->string('tipo_documento', 20)->default('TICKET');
            $table->string('tipo_pago', 20)->nullable(); // Pago: Efectivo, Tarjeta, Transferencia, etc.
            
            // Campos Fiscales (solo llenos para INVOICE)
            $table->string('cai', 100)->nullable();
            $table->string('numero_documento', 50)->nullable(); // Número de Factura/Documento

            // Estado de la Transacción
            $table->string('estado', 20)->default('COMPLETADA'); // COMPLETADA, PENDIENTE (Quote), ANULADA

            // ===================================
            // DESGLOSE FINANCIERO / FISCAL
            // ===================================
            $table->decimal('descuento', 10, 2)->default(0.00);

            // Subtotales Base (antes de ISV)
            $table->decimal('subtotal_neto', 10, 2)->default(0.00)->comment('Suma de gravado + exonerado');
            $table->decimal('subtotal_gravado', 10, 2)->default(0.00)->comment('Suma de bases imponibles');
            $table->decimal('subtotal_exonerado', 10, 2)->default(0.00)->comment('Suma de bases exoneradas');
            
            // Impuesto y Total Final
            $table->decimal('total_isv', 10, 2)->default(0.00);
            $table->decimal('total_final', 10, 2)->default(0.00)->comment('Monto a pagar (Neto - Descuento + ISV)');
            
            // Campo original (lo mantenemos por si acaso, aunque subtotal_neto es más preciso)
            $table->decimal('total_venta', 10, 2)->default(0.00); 

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
