<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        // 'estado' => \App\Enums\EstadoEmpresa::class, // Se remueve la asignación de tipo para evitar el error de casting
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

    public function tiendas()
    {
        return $this->belongsToMany(Tienda::class, 'empresa_tienda', 'empresa_id', 'tienda_id')
                    ->using(EmpresaTienda::class)
                    ->withPivot('estado');
    }
}
