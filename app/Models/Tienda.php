<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon; // Necesario para el método de ayuda

class Tienda extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'rtn',          // <--- AÑADIDO: Registro Tributario Nacional
        'direccion',    // <--- AÑADIDO: Dirección de la tienda
        'telefono',     // <--- AÑADIDO: Teléfono de la tienda
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
    
    // =======================================================
    // RELACIÓN CON RANGO CAI (CRÍTICO PARA FACTURACIÓN)
    // =======================================================
    /**
     * Obtiene todos los rangos CAI asociados a esta tienda.
     */
    public function rangosCai(): HasMany
    {
        return $this->hasMany(RangoCai::class);
    }
    
    /**
     * Método auxiliar para obtener el rango CAI activo y no expirado.
     * Utilizado principalmente para la vista de impresión.
     */
    public function rangoCaiActivo()
    {
        // Se busca el rango que está activo y cuya fecha límite aún no ha pasado.
        // Se asume que solo debe haber uno activo a la vez, aunque se usa first() por seguridad.
        return $this->rangosCai()
                    ->where('esta_activo', true)
                    ->whereDate('fecha_limite_emision', '>=', Carbon::today())
                    ->first();
    }
}