<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impuestos', function (Blueprint $table) {
            $table->id(); // Crea 'id' como clave primaria (INT AUTO_INCREMENT)
            $table->string('nombre', 50)->unique(); // Nombre del impuesto (e.g., IVA General)
            $table->decimal('porcentaje', 5, 2); // Tasa (e.g., 16.00)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impuestos');
    }
};