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
        Schema::create('empresa_tienda', function (Blueprint $table) {
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('tienda_id')->constrained('tiendas')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'rechazado', 'aprobado'])->default('pendiente');
            $table->string('codigo_asociacion')->unique();
            
            $table->primary(['empresa_id', 'tienda_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_tienda');
    }
};