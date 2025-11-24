<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon; 

class Tienda extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'rtn',          
        'direccion',    
        'telefono',     
        'municipio_id',
    ];
    
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_tienda', 'tienda_id', 'empresa_id')
                    ->using(EmpresaTienda::class)
                    ->withPivot('estado', 'codigo_asociacion'); 
    }
    
    public function rangosCai(): HasMany
    {
        return $this->hasMany(RangoCai::class);
    }
    
    public function rangoCaiActivo()
    {
        return $this->rangosCai()
                    ->where('esta_activo', true)
                    ->whereDate('fecha_limite_emision', '>=', Carbon::today())
                    ->first();
    }
}