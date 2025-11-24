<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{
    use HasFactory;

    protected $fillable = [
        'afiliado_id',
        'afiliado_dni', 
        'empresa_id',
        'estado',
        'comentario',
    ];


    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class); 
    }
}
