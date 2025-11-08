<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Venta extends Model
{
    use HasFactory;

    // Define los tipos de pago disponibles (Enum-like structure)
    // Se ha simplificado a 3 opciones clave.
    public const TIPOS_PAGO = [
        'EFECTIVO'      => 'Efectivo',
        'TARJETA'       => 'Tarjeta (Crédito/Débito)',
        'OTRO'          => 'Otro (Transferencia, Cheque, etc.)'
    ];

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
        'tipo_pago',            // <-- Campo de pago
        'cai',                  
        'numero_documento',     
        'estado',               
        
        'fecha_venta',
        'cliente_id',
    ];

    /**
     * Define los tipos de datos para la conversión automática.
     */
    protected $casts = [
        'fecha_venta' => 'datetime',
        'total_venta' => 'float',
        'descuento' => 'float',
        'subtotal_neto' => 'float',
        'subtotal_gravado' => 'float',
        'subtotal_exonerado' => 'float',
        'total_isv' => 'float',
        'total_final' => 'float',
    ];
    
    public function tienda(): BelongsTo
    { 
        return $this->belongsTo(Tienda::class); 
    }
    
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