<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConceptoPago;

class ConceptoPagoMejoradoSeeder extends Seeder
{
    public function run(): void
    {
        $conceptos = [
            ['nombre' => 'Cuota Inicial', 'descripcion' => 'Pago de cuota inicial'],
            ['nombre' => 'Mensualidad', 'descripcion' => 'Pago mensual de colegiatura'],
            ['nombre' => 'Matrícula', 'descripcion' => 'Pago de matrícula inicial'],
            ['nombre' => 'Material Didáctico', 'descripcion' => 'Libros y materiales educativos'],
            ['nombre' => 'Uniforme', 'descripcion' => 'Uniforme escolar'],
            ['nombre' => 'Seguro Escolar', 'descripcion' => 'Seguro contra accidentes'],
            ['nombre' => 'Actividades Extracurriculares', 'descripcion' => 'Talleres y actividades adicionales'],
            ['nombre' => 'Transporte', 'descripcion' => 'Servicio de transporte escolar'],
            ['nombre' => 'Alimentación', 'descripcion' => 'Servicio de comedor'],
            ['nombre' => 'Mora', 'descripcion' => 'Interés por pago tardío'],
            ['nombre' => 'Otros', 'descripcion' => 'Otros conceptos']
        ];

        foreach ($conceptos as $concepto) {
            ConceptoPago::firstOrCreate(
                ['nombre' => $concepto['nombre']],
                [
                    'descripcion' => $concepto['descripcion'],
                    'activo' => true,
                    'empresa_id' => 1,
                    'sucursal_id' => 1
                ]
            );
        }
    }
}
