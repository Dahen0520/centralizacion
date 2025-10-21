<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
    use HasFactory;
    
    // CAMPOS PERMITIDOS PARA ASIGNACIÓN MASIVA
    protected $fillable = [
        'nombre', 
        'porcentaje'
    ];

    // Un impuesto puede ser aplicado a muchos productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
