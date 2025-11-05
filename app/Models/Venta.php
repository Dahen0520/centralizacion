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
        
        // Totales originales y descuento
        'total_venta', 
        'descuento',
        
        // Campos de Desglose Fiscal (SAR)
        'subtotal_neto',       
        'subtotal_gravado',     
        'subtotal_exonerado',   
        'total_isv',            
        'total_final',          
        
        // Campos de Facturación / Documento
        'tipo_documento',       
        'tipo_pago',            
        'cai',                  
        'numero_documento',     
        'estado',               
        
        'fecha_venta',
        'cliente_id',
    ];

    /**
     * Define los tipos de datos para la conversión automática.
     * Esta es la corrección crítica para el error 'non-numeric value encountered'.
     */
    protected $casts = [
        'fecha_venta' => 'datetime',
        
        // Todos los campos numéricos deben ser 'float' para evitar errores.
        'total_venta' => 'float',
        'descuento' => 'float',
        'subtotal_neto' => 'float',
        'subtotal_gravado' => 'float',
        'subtotal_exonerado' => 'float',
        'total_isv' => 'float',
        'total_final' => 'float',
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