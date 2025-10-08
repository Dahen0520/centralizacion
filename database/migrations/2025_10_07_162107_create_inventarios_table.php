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
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();

            // Claves Foráneas:
            // 1. Relación con la Marca (Producto)
            $table->foreignId('marca_id')->constrained('marcas')->onDelete('cascade');
            
            // 2. Relación con la Tienda (Punto de venta)
            $table->foreignId('tienda_id')->constrained('tiendas')->onDelete('cascade');

            // Campos de Negocio:
            // Precio del producto en esta tienda específica.
            $table->decimal('precio', 10, 2); 
            
            // Stock o cantidad disponible en esta tienda (puede ser 0 si no hay existencias).
            $table->integer('stock')->default(0); 

            $table->timestamps();

            // Restricción: Asegura que solo puede haber un registro por par (Marca + Tienda)
            $table->unique(['marca_id', 'tienda_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};