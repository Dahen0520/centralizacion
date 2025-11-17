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
            
            // Relación con la tienda (sucursal)
            $table->foreignId('tienda_id')->constrained('tiendas')->onDelete('cascade');
            
            // Código de Autorización de Impresión (CAI) emitido por el SAR
            $table->string('cai', 100);
            
            // 🆕 Campo para almacenar el prefijo o serie de la factura (Ej: '000-001-01-')
            $table->string('prefijo_sar', 20)->comment('Prefijo de la serie de facturación SAR');

            // 🌟 Rango autorizado de facturas (ALMACENADO COMO NÚMERO ENTERO PURO)
            // Usamos unsignedBigInteger o unsignedInteger para números grandes sin signo. 
            // Para la secuencia de 8 dígitos de SAR, unsignedInteger (max ~4.2 billones) es suficiente.
            $table->unsignedInteger('rango_inicial')->comment('Secuencia numérica inicial (Ej: 1)');
            $table->unsignedInteger('rango_final')->comment('Secuencia numérica final (Ej: 500)');

            // 🌟 Último número de factura utilizado (ALMACENADO COMO NÚMERO ENTERO PURO)
            // Se inicializará a (rango_inicial - 1) para que la primera factura sea la número inicial
            $table->unsignedInteger('numero_actual')->comment('Última secuencia numérica utilizada (Ej: 0 ó 499)');

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