<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'marca_id',
        'tienda_id',
        'precio',
        'stock',
    ];

    /**
     * Obtiene la marca (producto) a la que pertenece este registro de inventario.
     */
    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    /**
     * Obtiene la tienda a la que pertenece este registro de inventario.
     */
    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }
    
    // ⭐ NUEVA RELACIÓN: Obtiene todos los movimientos de inventario de este stock.
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}