<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inventario_id')->constrained('inventarios')->onDelete('cascade');
            $table->enum('tipo_movimiento', ['ENTRADA', 'SALIDA']);
            $table->string('razon'); 
            $table->integer('cantidad');
            $table->morphs('movible'); 

            $table->foreignId('usuario_id')->constrained('users')->nullable(); 

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};