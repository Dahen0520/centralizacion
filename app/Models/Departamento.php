<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    public $timestamps = false;
 
    protected $table = 'departamentos';

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'id_departamento');
    }

    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'id_departamento');
    }
}
