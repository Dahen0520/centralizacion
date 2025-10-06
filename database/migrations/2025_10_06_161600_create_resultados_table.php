<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Usamos 'resultados' para el nombre de la tabla.
        Schema::create('resultados', function (Blueprint $table) { 
            $table->id();
            
            // 1. Quién dicta el resultado (afiliado o usuario revisor)
            // Clave foránea al ID del afiliado (necesaria para la integridad)
            $table->foreignId('afiliado_id')->constrained('afiliados')->onDelete('cascade');
            
            // NUEVO: Columna para registrar el DNI del afiliado (para consulta rápida)
            $table->string('afiliado_dni', 50); 
            
            // 2. A qué empresa corresponde el resultado
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

            // 3. Estado final registrado
            $table->enum('estado', ['aprobado', 'rechazado']); 
            
            // 4. Comentario/Retroalimentación obligatoria
            $table->text('comentario'); 

            $table->timestamps();
            
            // Asegura que solo haya un resultado final por empresa
            $table->unique(['empresa_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};