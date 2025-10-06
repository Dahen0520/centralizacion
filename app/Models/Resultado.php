<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente (Mass Assignable).
     * Se agrega 'afiliado_dni' para permitir su inserción.
     */
    protected $fillable = [
        'afiliado_id',
        'afiliado_dni', // <-- CAMBIO: Se agrega el nuevo campo
        'empresa_id',
        'estado',
        'comentario',
    ];

    /**
     * Obtiene la empresa a la que pertenece este resultado.
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Obtiene el afiliado/usuario que emitió este resultado.
     */
    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class); 
    }
}
