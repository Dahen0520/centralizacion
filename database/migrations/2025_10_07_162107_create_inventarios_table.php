<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();


            $table->foreignId('marca_id')->constrained('marcas')->onDelete('cascade');
            
            $table->foreignId('tienda_id')->constrained('tiendas')->onDelete('cascade');

            $table->decimal('precio', 10, 2); 
            
            $table->integer('stock')->default(0); 

            $table->timestamps();

            $table->unique(['marca_id', 'tienda_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventarios');
    }
};