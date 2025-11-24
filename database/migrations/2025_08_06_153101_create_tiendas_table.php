<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up(): void
    {
        Schema::create('tiendas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique(); 
            $table->string('rtn', 50)->nullable();         
            $table->string('direccion')->nullable();       
            $table->string('telefono', 20)->nullable();   
            $table->foreignId('municipio_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiendas');
    }
};