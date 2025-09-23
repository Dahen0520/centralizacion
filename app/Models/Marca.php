<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'empresa_id',
        'codigo_marca',
        'estado'
    ];

    /**
     * Genera automáticamente el código de marca antes de guardar.
     */
    protected static function booted()
    {
        static::creating(function ($marca) {
            $producto = Producto::with('subcategoria.categoria')->find($marca->producto_id);
            $empresa = Empresa::find($marca->empresa_id);

            if ($producto && $producto->subcategoria && $producto->subcategoria->categoria && $empresa) {
                $codigo_marca = sprintf(
                    '%s-%s-%s-%s-%s',
                    $producto->subcategoria->categoria->id,
                    $producto->subcategoria->id,
                    $producto->id,
                    $empresa->id,
                    time() 
                );
                $marca->codigo_marca = $codigo_marca;
            }
        });
    }

    /**
     * Relación con el producto.
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con la empresa.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
