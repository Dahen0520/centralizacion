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
        Schema::create('rango_cais', function (Blueprint $table) {
            $table->id();
            
            // Relación con la tienda (sucursal) que utiliza este rango de facturación
            $table->foreignId('tienda_id')->constrained('tiendas')->onDelete('cascade');
            
            // Código de Autorización de Impresión (CAI) emitido por el SAR
            $table->string('cai', 100)->unique();
            
            // Rango autorizado de facturas
            $table->string('rango_inicial', 50);
            $table->string('rango_final', 50);

            // Último número de factura utilizado en esta serie (se actualiza en cada venta)
            // Se inicializa con el rango_inicial o el valor que defina el sistema
            $table->string('numero_actual', 50);

            // Fecha límite para la emisión de documentos con este CAI
            $table->date('fecha_limite_emision');
            
            // Estado del rango (activo/inactivo/expirado)
            $table->boolean('esta_activo')->default(true);
            
            $table->timestamps();

            // Índice para búsquedas rápidas por tienda y estado activo
            $table->index(['tienda_id', 'esta_activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rango_cais');
    }
};