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
        Schema::create('tiendas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); 
            
            // --- NUEVOS CAMPOS AÑADIDOS ---
            $table->string('rtn', 50)->nullable();          // Registro Tributario Nacional
            $table->string('direccion')->nullable();        // Dirección completa de la tienda
            $table->string('telefono', 20)->nullable();     // Teléfono de contacto
            // --- FIN NUEVOS CAMPOS ---
            
            $table->foreignId('municipio_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tiendas');
    }
};