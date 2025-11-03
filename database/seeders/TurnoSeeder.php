<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Turno;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all records from the turnos table to avoid duplicates
        DB::table('turnos')->delete();

        $turnos = [
            [
                'nombre' => 'Mañana',
                'descripcion' => 'Turno de la mañana',
                'hora_inicio' => '07:00:00',
                'hora_fin' => '12:00:00',
                'status' => 1,
                'empresa_id' => 1,
                'sucursal_id' => 1,
            ],
            [
                'nombre' => 'Tarde',
                'descripcion' => 'Turno de la tarde',
                'hora_inicio' => '12:00:00',
                'hora_fin' => '17:00:00',
                'status' => 1,
                'empresa_id' => 1,
                'sucursal_id' => 1,
            ],
            [
                'nombre' => 'Noche',
                'descripcion' => 'Turno de la noche',
                'hora_inicio' => '17:00:00',
                'hora_fin' => '22:00:00',
                'status' => 1,
                'empresa_id' => 1,
                'sucursal_id' => 1,
            ],
        ];

        foreach ($turnos as $turno) {
            Turno::create($turno);
        }
    }
}
