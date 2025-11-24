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
        'prefijo_sar', 
        
        'rango_inicial',
        'rango_final',
        'numero_actual',
        'fecha_limite_emision',
        'esta_activo',
    ];

    protected $casts = [
        'fecha_limite_emision' => 'date',
        'esta_activo' => 'boolean',
        'tienda_id' => 'integer',
        
        'rango_inicial' => 'integer', 
        'rango_final' => 'integer',
        'numero_actual' => 'integer', 
    ];

    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }
}