<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'tienda_id',
        'usuario_id',
        'total_venta',
        'fecha_venta',
        'cliente_id',
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
    public function usuario(): BelongsTo 
    {
        return $this->belongsTo(\App\Models\User::class); 
    }
    
    public function detalles()
    { 
        return $this->hasMany(DetalleVenta::class); 
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}