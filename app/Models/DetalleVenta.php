<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'venta_id',
        'inventario_id',
        'cantidad',
        'precio_unitario', 
        
        // Campos de Desglose Fiscal por línea
        'subtotal_base',   
        'isv_tasa',        
        'isv_monto',       
        'subtotal_final',  
    ];
    
    protected $casts = [
        'cantidad' => 'integer', 
        'precio_unitario' => 'float',
        'subtotal_base' => 'float',
        'isv_tasa' => 'float',
        'isv_monto' => 'float',
        'subtotal_final' => 'float',
    ];

    public function venta() { return $this->belongsTo(Venta::class); }
    public function inventario() { return $this->belongsTo(Inventario::class); }
    
    public function movimientos()
    {
        return $this->morphMany(MovimientoInventario::class, 'movible');
    }
}