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
                'nombre' => 'Educación Inicial',
                'descripcion' => 'Educación inicial para niños menores de 8 años',
                'costo' => 1200.00,
                'numero_cuotas' => 12,
                'cuota_inicial' => 200.00,
                'status' => 1,
            ],
            [
                'nombre' => 'Primaria',
                'descripcion' => 'Educación primaria de 1ro a 6to grado',
                'costo' => 1500.00,
                'numero_cuotas' => 12,
                'cuota_inicial' => 250.00,
                'status' => 1,
            ],
            [
                'nombre' => 'Secundaria',
                'descripcion' => 'Educación secundaria de 1ro a 5to grado',
                'costo' => 1800.00,
                'numero_cuotas' => 12,
                'cuota_inicial' => 300.00,
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