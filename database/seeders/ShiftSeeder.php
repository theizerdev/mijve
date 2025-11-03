<?php

namespace Database\Seeders;

use App\Models\Turno;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'nombre' => 'Mañana',
                'hora_inicio' => '07:00:00',
                'hora_fin' => '12:00:00',
                'descripcion' => 'Turno de la mañana',
                'status' => 1,
                'empresa_id' => 1,
                'sucursal_id' => 1,
            ],
            [
                'nombre' => 'Tarde',
                'hora_inicio' => '12:00:00',
                'hora_fin' => '17:00:00',
                'descripcion' => 'Turno de la tarde',
                'status' => 1,
                'empresa_id' => 1,
                'sucursal_id' => 1,
            ],
            [
                'nombre' => 'Noche',
                'hora_inicio' => '17:00:00',
                'hora_fin' => '22:00:00',
                'descripcion' => 'Turno de la noche',
                'status' => 1,
                'empresa_id' => 1,
                'sucursal_id' => 1,
            ],
        ];

        foreach ($shifts as $shift) {
            Turno::updateOrCreate(
                ['nombre' => $shift['nombre']],
                $shift
            );
        }
    }
}
