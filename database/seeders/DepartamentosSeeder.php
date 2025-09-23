<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departamentos')->insert([
            ['id' => 1, 'nombre' => 'ATLANTIDA'],
            ['id' => 2, 'nombre' => 'COLON'],
            ['id' => 3, 'nombre' => 'COMAYAGUA'],
            ['id' => 4, 'nombre' => 'COPAN'],
            ['id' => 5, 'nombre' => 'CORTES'],
            ['id' => 6, 'nombre' => 'CHOLUTECA'],
            ['id' => 7, 'nombre' => 'EL PARAISO'],
            ['id' => 8, 'nombre' => 'FRANCISCO MORAZAN'],
            ['id' => 9, 'nombre' => 'GRACIAS A DIOS'],
            ['id' => 10, 'nombre' => 'INTIBUCA'],
            ['id' => 11, 'nombre' => 'ISLAS DE LA BAHIA'],
            ['id' => 12, 'nombre' => 'LA PAZ'],
            ['id' => 13, 'nombre' => 'LEMPIRA'],
            ['id' => 14, 'nombre' => 'OCOTEPEQUE'],
            ['id' => 15, 'nombre' => 'OLANCHO'],
            ['id' => 16, 'nombre' => 'SANTA BARBARA'],
            ['id' => 17, 'nombre' => 'VALLE'],
            ['id' => 18, 'nombre' => 'YORO'],
        ]);
    }
}
