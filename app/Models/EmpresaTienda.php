<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpresaTienda extends Pivot
{
    use HasFactory;

    protected $table = 'empresa_tienda';

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
    
    public function tienda(): BelongsTo
    {
        return $this->belongsTo(Tienda::class);
    }
}