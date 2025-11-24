<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 
use App\Models\Producto;
use App\Models\Empresa; 

class Marca extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'empresa_id',
        'codigo_marca',
        'estado'
    ];
    
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

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }
}
