<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangoCai extends Model
{
    use HasFactory;

    protected $fillable = [
        'tienda_id',
        'cai',
        'rango_inicial',
        'rango_final',
        'numero_actual',
        'fecha_limite_emision',
        'esta_activo',
    ];

    /**
     * Define los tipos de datos para la conversión automática.
     * CRÍTICO: Forzar los campos de numeración a INTEGER para que PHP y el Controller
     * puedan realizar sumas y comparaciones con seguridad.
     */
    protected $casts = [
        'fecha_limite_emision' => 'date',
        'esta_activo' => 'boolean',
        // ⭐ CAMPOS CORREGIDOS
        'tienda_id' => 'integer',
        'rango_inicial' => 'integer',
        'rango_final' => 'integer',
        'numero_actual' => 'integer', 
    ];

    // Relación con la Tienda
    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }
}