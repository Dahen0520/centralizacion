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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_negocio');
            $table->string('direccion');
            
            // NUEVO CAMPO BOLEANO PARA FACTURACIÓN
            // Indica si la empresa tiene habilitada la facturación (default: falso)
            $table->boolean('facturacion')->default(false); 
            
            // Relaciones
            $table->foreignId('rubro_id')->constrained('rubros');
            $table->foreignId('tipo_organizacion_id')->constrained('tipo_organizacions');
            
            // Campo para el país de exportación, permite valores nulos
            $table->foreignId('pais_exportacion_id')->nullable()->constrained('paises');
            
            // Nuevo campo para el afiliado, con clave foránea
            $table->foreignId('afiliado_id')->constrained('afiliados');
            
            // Nuevo campo para el estado usando ENUM
            $table->enum('estado', ['pendiente', 'rechazado', 'aprobado'])->default('pendiente');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
