<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // <-- Mantiene la herencia de MODEL
// Importación de modelos necesarios para la función booted
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
    
    // NOTA: Si tu tabla 'marcas' usa una clave primaria compuesta y no la columna 'id',
    // debes definir: protected $primaryKey = ['producto_id', 'empresa_id'];
    // Pero asumiendo que tu migración usa $table->id(), esta línea no es necesaria.


    /**
     * Genera automáticamente el código de marca antes de guardar.
     * Esta lógica asegura que el código se genere al momento de la creación.
     */
    protected static function booted()
    {
        static::creating(function ($marca) {
            // Se asume que Producto y Subcategoria ya están definidos y tienen sus relaciones.
            $producto = Producto::with('subcategoria.categoria')->find($marca->producto_id);
            $empresa = Empresa::find($marca->empresa_id);

            if ($producto && $producto->subcategoria && $producto->subcategoria->categoria && $empresa) {
                // Formato del código de marca (ej: CATID-SUBID-PRODID-EMPID-TIMESTAMP)
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
