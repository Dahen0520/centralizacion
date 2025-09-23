<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoOrganizacion extends Model
{
    use HasFactory;
    protected $table = 'tipo_organizacions'; 
    protected $fillable = [
        'nombre',
    ];
}