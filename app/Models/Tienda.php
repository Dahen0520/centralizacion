<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_tienda', 'tienda_id', 'empresa_id')
                    ->using(EmpresaTienda::class)
                    ->withPivot('estado');
    }
}