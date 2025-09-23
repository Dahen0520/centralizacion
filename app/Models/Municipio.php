<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    public $timestamps = false;
    
    protected $table = 'municipios';

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_municipio');
    }
}