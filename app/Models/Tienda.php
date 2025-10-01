<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Asegúrate de importar los modelos
use App\Models\Municipio;
use App\Models\Empresa;
use App\Models\EmpresaTienda;

class Tienda extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'municipio_id',
    ];
    
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    // =======================================================
    // RELACIÓN INVERSA CON EMPRESAS A TRAVÉS DE LA PIVOT
    // =======================================================
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_tienda', 'tienda_id', 'empresa_id')
                    ->using(EmpresaTienda::class)
                    ->withPivot('estado', 'codigo_asociacion'); // Campos extra de la pivot
    }
}
