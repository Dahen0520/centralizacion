<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaTienda extends Pivot
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'empresa_tienda';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'empresa_id',
        'tienda_id',
        'estado',
        'codigo_asociacion' 
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
    
    // **NUEVO**: Define la relación con el modelo Tienda
    public function tienda(): BelongsTo
    {
        return $this->belongsTo(Tienda::class);
    }
}