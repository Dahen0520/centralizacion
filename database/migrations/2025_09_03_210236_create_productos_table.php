<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            
            // Columna para el nombre del producto
            $table->string('nombre');

            // Columna para la descripción del producto (puede ser nula)
            $table->text('descripcion')->nullable();

            // Columna de clave foránea para la subcategoría
            $table->foreignId('subcategoria_id')->constrained('subcategorias')->onDelete('cascade');

            // Clave foránea para el impuesto (ya existente)
            $table->foreignId('impuesto_id')
                  ->constrained('impuestos')
                  ->onDelete('restrict');

            // Columna para el estado del producto usando un ENUM
            $table->enum('estado', ['pendiente', 'rechazado', 'aprobado'])->default('pendiente');
            
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};