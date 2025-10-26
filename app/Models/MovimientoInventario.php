<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'inventario_id',
        'tipo_movimiento', // 'ENTRADA' o 'SALIDA'
        'razon',           // 'Venta', 'Ingreso por Compra', 'Descarte', etc.
        'cantidad',
        'movible_id',      // Clave Polimórfica
        'movible_type',    // Tipo Polimórfico (e.g., App\Models\DetalleVenta)
        'usuario_id',
    ];
    
    /**
     * Obtiene el registro de inventario (stock) afectado.
     */
    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    /**
     * Define la relación polimórfica.
     * Permite saber qué origen el movimiento (e.g., DetalleVenta, Compra, Ajuste).
     */
    public function movible()
    {
        return $this->morphTo();
    }
    
    /**
     * Obtiene el usuario que registró el movimiento.
     */
    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class); 
    }
}