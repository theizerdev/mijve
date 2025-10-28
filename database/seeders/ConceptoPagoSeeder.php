<?php

namespace Database\Seeders;

use App\Models\ConceptoPago;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class ConceptoPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all records from the conceptos_pago table to avoid duplicates
        DB::table('conceptos_pago')->delete();
        
        $conceptos = [
            [
                'nombre' => 'Matrícula',
                'descripcion' => 'Pago por concepto de matrícula',
                'activo' => true,
            ],
            [
                'nombre' => 'Cuota Inicial',
                'descripcion' => 'Cuota inicial del programa educativo',
                'activo' => true,
            ],
            [
                'nombre' => 'Mensualidad',
                'descripcion' => 'Pago mensual por derecho de estudio',
                'activo' => true,
            ],
            [
                'nombre' => 'Material Escolar',
                'descripcion' => 'Pago por material escolar y útiles',
                'activo' => true,
            ],
            [
                'nombre' => 'Transporte',
                'descripcion' => 'Servicio de transporte escolar',
                'activo' => true,
            ],
            [
                'nombre' => 'Alimentación',
                'descripcion' => 'Servicio de alimentación escolar',
                'activo' => true,
            ],
        ];

        foreach ($conceptos as $concepto) {
            ConceptoPago::create($concepto);
        }
    }
}