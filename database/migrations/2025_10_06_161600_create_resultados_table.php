<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultados', function (Blueprint $table) { 
            $table->id();
            
            $table->foreignId('afiliado_id')->constrained('afiliados')->onDelete('cascade');
            
            $table->string('afiliado_dni', 50); 
            
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');

            $table->enum('estado', ['aprobado', 'rechazado']); 
            
            $table->text('comentario'); 

            $table->timestamps();
            
            $table->unique(['empresa_id']); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados');
    }
};