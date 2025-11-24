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
        'tipo_movimiento', 
        'razon',          
        'cantidad',
        'movible_id',     
        'movible_type',   
        'usuario_id',
    ];
    
    protected $casts = [
        'inventario_id' => 'integer',
        'cantidad' => 'integer',
        'movible_id' => 'integer',
        'usuario_id' => 'integer',
    ];
    
 
    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function movible()
    {
        return $this->morphTo();
    }
    
    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class); 
    }
}