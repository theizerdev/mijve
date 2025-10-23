<?php

namespace Database\Seeders;

use App\Models\NivelEducativo;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EducationalLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationalLevels = [
            [
                'nombre' => 'Inicial',
                'descripcion' => 'Educación inicial para niños de 3 a 5 años',
                'costo' => 0.00,
                'numero_cuotas' => 12,
                'cuota_inicial' => 0.00,
                'status' => 1,
            ],
            [
                'nombre' => 'Primaria',
                'descripcion' => 'Educación primaria de 1ro a 6to grado',
                'costo' => 0.00,
                'numero_cuotas' => 12,
                'cuota_inicial' => 0.00,
                'status' => 1,
            ],
            [
                'nombre' => 'Secundaria',
                'descripcion' => 'Educación secundaria de 1ro a 5to grado',
                'costo' => 0.00,
                'numero_cuotas' => 12,
                'cuota_inicial' => 0.00,
                'status' => 1,
            ],
        ];

        foreach ($educationalLevels as $level) {
            NivelEducativo::updateOrCreate(
                ['nombre' => $level['nombre']],
                $level
            );
        }
    }
}
