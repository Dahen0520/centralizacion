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
        Schema::create('afiliados', function (Blueprint $table) {
            $table->id();
            $table->string('dni')->unique();
            $table->string('nombre');
            $table->string('genero');
            $table->date('fecha_nacimiento');
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('barrio')->nullable();
            $table->string('rtn')->nullable();
            $table->string('numero_cuenta')->nullable();
            $table->tinyInteger('status')->default(0);

            // Clave foránea para el municipio
            $table->foreignId('municipio_id')->constrained('municipios');


            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('afiliados');
    }
};