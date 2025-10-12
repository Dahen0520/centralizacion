<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'identificacion',
        'telefono',
        'email',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}