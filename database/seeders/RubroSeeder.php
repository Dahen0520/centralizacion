<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class RubroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rubros = [
            'Agricultura, ganadería, silvicultura y pesca',
            'Explotación de minas y canteras',
            'Industrias manufactureras',
            'Suministro de electricidad, gas, vapor y aire acondicionado',
            'Suministro de agua; evacuación de aguas residuales, gestión de desechos y descontaminación',
            'Construcción',
            'Comercio al por mayor y al por menor; reparación de vehículos automotores y motocicletas',
            'Transporte y almacenamiento',
            'Actividades de alojamiento y de servicio de comidas',
            'Información y comunicaciones',
            'Actividades financieras y de seguros',
            'Actividades inmobiliarias',
            'Actividades profesionales, científicas y técnicas',
            'Actividades de servicios administrativos y de apoyo',
            'Administración pública y defensa; planes de seguridad social de afiliación obligatoria',
            'Enseñanza',
            'Actividades de atención de la salud humana y de asistencia social',
            'Actividades artísticas, de entretenimiento y recreativas',
            'Otras actividades de servicios',
            'Actividades de los hogares como empleadores y actividades no diferenciadas de los hogares como productores de bienes y servicios para uso propio',
            'Actividades de organizaciones y órganos extraterritoriales',
        ];

        $data = [];
        $now = Carbon::now();

        foreach ($rubros as $rubro) {
            $data[] = [
                'nombre' => $rubro,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insertar los datos en la tabla 'rubros'
        DB::table('rubros')->insert($data);
    }
}