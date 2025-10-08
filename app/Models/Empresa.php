<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Asegúrate de importar todos los modelos relacionados
use App\Models\Afiliado;
use App\Models\Rubro;
use App\Models\TipoOrganizacion;
use App\Models\Pais;
use App\Models\Tienda;
use App\Models\Producto;
use App\Models\Marca;

class Empresa extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre_negocio',
        'direccion',
        'rubro_id',
        'tipo_organizacion_id',
        'pais_exportacion_id',
        'afiliado_id',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // ...
    ];

    /**
     * Get the afiliado that owns the empresa.
     */
    public function afiliado(): BelongsTo
    {
        return $this->belongsTo(Afiliado::class);
    }

    /**
     * Get the rubro that owns the empresa.
     */
    public function rubro(): BelongsTo
    {
        return $this->belongsTo(Rubro::class);
    }

    /**
     * Get the tipo organizacion that owns the empresa.
     */
    public function tipoOrganizacion(): BelongsTo
    {
        return $this->belongsTo(TipoOrganizacion::class);
    }

    /**
     * Get the pais de exportacion that owns the empresa.
     */
    public function paisExportacion(): BelongsTo
    {
        return $this->belongsTo(Pais::class, 'pais_exportacion_id');
    }

    // =======================================================
    // RELACIÓN CON PRODUCTOS A TRAVÉS DE LA TABLA PIVOT 'MARCAS'
    // Importante: Quitamos ->using(Marca::class)
    // =======================================================
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'marcas', 'empresa_id', 'producto_id')
                    ->withPivot('estado', 'codigo_marca'); // Campos extra de la pivot
    }

    // =======================================================
    // RELACIÓN CON TIENDAS A TRAVÉS DE LA TABLA PIVOT 'EMPRESA_TIENDA'
    // Importante: Quitamos ->using(EmpresaTienda::class)
    // =======================================================
    public function tiendas()
    {
        return $this->belongsToMany(Tienda::class, 'empresa_tienda', 'empresa_id', 'tienda_id')
                    ->withPivot('estado', 'codigo_asociacion'); // Campos extra de la pivot
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

