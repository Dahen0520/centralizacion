<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'subcategoria_id',
        'estado'
    ];

    /**
     * Obtiene la subcategoría a la que pertenece el producto.
     */
    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    /**
     * Obtiene las empresas a las que pertenece el producto.
     */
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_tienda_producto');
    }

    /**
     * Obtiene las tiendas a las que pertenece el producto.
     */
    public function tiendas()
    {
        return $this->belongsToMany(Tienda::class, 'empresa_tienda_producto');
    }
}

