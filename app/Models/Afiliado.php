<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afiliado extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'afiliados';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    /**
     * Get the municipio associated with the afiliado.
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}