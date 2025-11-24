<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TipoOrganizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            'Individual',
            'Sector Social Economía', 
            'Sociedad',
        ];

        $data = [];
        $now = Carbon::now();

        foreach ($tipos as $tipo) {
            $data[] = [
                'nombre' => $tipo,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insertar los datos en la tabla 'tipo_organizacions'
        DB::table('tipo_organizacions')->insert($data);
    }
}