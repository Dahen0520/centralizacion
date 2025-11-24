<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'subcategoria_id',
        'impuesto_id',
        'estado'
    ];

    public function subcategoria()
    {
        return $this->belongsTo(Subcategoria::class);
    }

    public function impuesto()
    {
        return $this->belongsTo(Impuesto::class);
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_tienda_producto');
    }

    public function tiendas()
    {
        return $this->belongsToMany(Tienda::class, 'empresa_tienda_producto');
    }
}

