<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up(): void
    {
        Schema::create('rango_cais', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('tienda_id')->constrained('tiendas')->onDelete('cascade');
            $table->string('cai', 100);
            $table->string('prefijo_sar', 20)->comment('Prefijo de la serie de facturación SAR');
            $table->unsignedInteger('rango_inicial')->comment('Secuencia numérica inicial (Ej: 1)');
            $table->unsignedInteger('rango_final')->comment('Secuencia numérica final (Ej: 500)');
            $table->unsignedInteger('numero_actual')->comment('Última secuencia numérica utilizada (Ej: 0 ó 499)');
            $table->date('fecha_limite_emision');
            $table->boolean('esta_activo')->default(true);
            $table->timestamps();
            $table->index(['tienda_id', 'esta_activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rango_cais');
    }
};