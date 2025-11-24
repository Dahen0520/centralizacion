<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afiliado extends Model
{
    use HasFactory;

    protected $table = 'afiliados';

    protected $fillable = [
        'dni',
        'nombre',
        'genero',
        'fecha_nacimiento',
        'email',
        'telefono',
        'barrio',
        'rtn',
        'numero_cuenta',
        'municipio_id',
        'status',
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}