<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            
            $table->string('nombre');

            $table->text('descripcion')->nullable();

            $table->foreignId('subcategoria_id')->constrained('subcategorias')->onDelete('cascade');

            $table->foreignId('impuesto_id')
                  ->constrained('impuestos')
                  ->onDelete('restrict');

            $table->enum('estado', ['pendiente', 'rechazado', 'aprobado'])->default('pendiente');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};