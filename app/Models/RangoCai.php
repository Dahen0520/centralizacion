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
        // 🆕 Agregamos el nuevo campo
        'prefijo_sar', 
        
        // Los siguientes campos ahora almacenan solo el número entero de la secuencia
        'rango_inicial',
        'rango_final',
        'numero_actual',
        'fecha_limite_emision',
        'esta_activo',
    ];

    /**
     * Define los tipos de datos para la conversión automática.
     */
    protected $casts = [
        'fecha_limite_emision' => 'date',
        'esta_activo' => 'boolean',
        'tienda_id' => 'integer',
        
        // ⭐ CRÍTICO: Aseguramos que estos campos siempre se traten como números enteros
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