<?php
namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Seeder;

class PaisSeeder extends Seeder
{
    public function run(): void
    {
        $paises = [
            'Honduras',
            'Guatemala',
            'El Salvador',
            'Nicaragua',
            'Costa Rica',
            'Panamá',
            'México',
            'Colombia',
            'Venezuela',
            'Estados Unidos',
            'Canadá',
            'Brasil',
            'Argentina',
            'Chile',
            'España',
        ];

        foreach ($paises as $pais) {
            Pais::create(['nombre' => $pais]);
        }
    }
}