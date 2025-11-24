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

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }
    
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}