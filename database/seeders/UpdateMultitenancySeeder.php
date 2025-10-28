<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Matricula;
use App\Models\Pago;
use App\Models\Programa;
use App\Models\ConceptoPago;

class UpdateMultitenancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Actualizar matrículas con empresa_id y sucursal_id basados en el estudiante
        Matricula::with('estudiante')->each(function ($matricula) {
            if ($matricula->estudiante) {
                $matricula->update([
                    'empresa_id' => $matricula->estudiante->empresa_id,
                    'sucursal_id' => $matricula->estudiante->sucursal_id,
                ]);
            }
        });

        // Actualizar programas con empresa_id y sucursal_id (asumiendo que pertenecen a la primera empresa/sucursal)
        $firstEmpresaId = \App\Models\Empresa::first()?->id;
        $firstSucursalId = \App\Models\Sucursal::first()?->id;
        
        if ($firstEmpresaId && $firstSucursalId) {
            Programa::whereNull('empresa_id')->update([
                'empresa_id' => $firstEmpresaId,
                'sucursal_id' => $firstSucursalId,
            ]);
            
            ConceptoPago::whereNull('empresa_id')->update([
                'empresa_id' => $firstEmpresaId,
                'sucursal_id' => $firstSucursalId,
            ]);
        }

        // Actualizar pagos con empresa_id y sucursal_id basados en la matrícula
        Pago::with('matricula')->each(function ($pago) {
            if ($pago->matricula) {
                $pago->update([
                    'empresa_id' => $pago->matricula->empresa_id,
                    'sucursal_id' => $pago->matricula->sucursal_id,
                ]);
            }
        });
    }
}
