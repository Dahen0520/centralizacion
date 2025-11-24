<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Afiliado;
use App\Models\Rubro;
use App\Models\TipoOrganizacion;
use App\Models\Pais;
use App\Models\Tienda;
use App\Models\Producto;
use App\Models\Marca;
use App\Models\Resultado; 

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_negocio',
        'direccion',
        'facturacion', 
        'rubro_id',
        'tipo_organizacion_id',
        'pais_exportacion_id',
        'afiliado_id',
        'estado',
    ];


    protected $casts = [
        'facturacion' => 'boolean', 
    ];

    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class);
    }

    public function rubro(): BelongsTo
    {
        return $this->belongsTo(Rubro::class);
    }

    public function tipoOrganizacion(): BelongsTo
    {
        return $this->belongsTo(TipoOrganizacion::class);
    }

    public function paisExportacion(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_exportacion_id');
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'marcas', 'empresa_id', 'producto_id')
                    ->withPivot('estado', 'codigo_marca'); 
    }

    public function tiendas()
    {
        return $this->belongsToMany(Tienda::class, 'empresa_tienda', 'empresa_id', 'tienda_id')
                    ->withPivot('estado', 'codigo_asociacion'); 
    }

    public function resultado()
    {
        return $this->hasOne(Resultado::class);
    }

    public function marcas()
    {
        return $this->hasMany(Marca::class);
    }
}

