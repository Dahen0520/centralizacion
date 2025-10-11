<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importación necesaria

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'tienda_id',
        'usuario_id',
        'total_venta',
        'fecha_venta',
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
    ];
    
    // Define la relación con la tienda
    public function tienda(): BelongsTo
    { 
        return $this->belongsTo(Tienda::class); 
    }
    
    // Define la relación con el usuario (quien registró la venta)
    public function usuario(): BelongsTo // <-- ¡RELACIÓN FALTANTE!
    {
        // Asume que tu modelo de usuario es App\Models\User (el default de Laravel)
        return $this->belongsTo(\App\Models\User::class); 
    }
    
    public function detalles()
    { 
        return $this->hasMany(DetalleVenta::class); 
    }
}