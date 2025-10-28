<?php

namespace Database\Seeders;

use App\Models\Pago;
use App\Models\Matricula;
use App\Models\ConceptoPago;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class PagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all records from the pagos table to avoid duplicates
        DB::table('pagos')->delete();
        
        // Get required data
        $matriculas = Matricula::all();
        $conceptos = ConceptoPago::all();
        
        if ($matriculas->isEmpty() || $conceptos->isEmpty()) {
            $this->command->warn('No hay suficientes datos para crear pagos. Verifica que existan matrículas y conceptos de pago.');
            return;
        }
        
        // Get specific concepts
        $conceptoMatricula = $conceptos->where('nombre', 'Matrícula')->first();
        $conceptoCuotaInicial = $conceptos->where('nombre', 'Cuota Inicial')->first();
        $conceptoMensualidad = $conceptos->where('nombre', 'Mensualidad')->first();
        
        // Create payments for each matricula
        foreach ($matriculas as $matricula) {
            // Create matricula payment
            if ($conceptoMatricula) {
                Pago::create([
                    'matricula_id' => $matricula->id,
                    'concepto_pago_id' => $conceptoMatricula->id,
                    'fecha_pago' => $matricula->fecha_matricula,
                    'monto' => 50.00, // Fixed amount for matricula
                    'metodo_pago' => 'efectivo',
                    'referencia' => 'MAT-' . strtoupper(uniqid()),
                    'estado' => 'pagado',
                ]);
            }
            
            // Create initial fee payment
            if ($conceptoCuotaInicial) {
                Pago::create([
                    'matricula_id' => $matricula->id,
                    'concepto_pago_id' => $conceptoCuotaInicial->id,
                    'fecha_pago' => $matricula->fecha_matricula->addDays(rand(1, 5)),
                    'monto' => $matricula->cuota_inicial,
                    'metodo_pago' => 'efectivo',
                    'referencia' => 'CI-' . strtoupper(uniqid()),
                    'estado' => 'pagado',
                ]);
            }
            
            // Create monthly payments (3 random payments)
            if ($conceptoMensualidad) {
                for ($i = 1; $i <= 3; $i++) {
                    Pago::create([
                        'matricula_id' => $matricula->id,
                        'concepto_pago_id' => $conceptoMensualidad->id,
                        'fecha_pago' => $matricula->fecha_matricula->addMonths($i),
                        'monto' => $this->calculateMonthlyFee($matricula),
                        'metodo_pago' => 'efectivo',
                        'referencia' => 'MEN-' . strtoupper(uniqid()),
                        'estado' => rand(0, 1) ? 'pagado' : 'pendiente',
                    ]);
                }
            }
        }
    }
    
    /**
     * Calculate the monthly fee
     */
    private function calculateMonthlyFee($matricula)
    {
        // Calculate the monthly fee based on total cost minus initial fee,
        // divided by the number of installments
        if ($matricula->numero_cuotas > 0) {
            return round(($matricula->costo - $matricula->cuota_inicial) / $matricula->numero_cuotas, 2);
        }
        
        return 0;
    }
}