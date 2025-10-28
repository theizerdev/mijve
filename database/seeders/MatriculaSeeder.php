<?php

namespace Database\Seeders;

use App\Models\Matricula;
use App\Models\Student;
use App\Models\Programa;
use App\Models\SchoolPeriod;
use App\Models\EducationalLevel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;

class MatriculaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all records from the matriculas table to avoid duplicates
        DB::table('matriculas')->delete();
        
        // Get required data
        $students = Student::all();
        $programas = Programa::all();
        $periodo = SchoolPeriod::where('is_current', true)->first();
        
        if ($students->isEmpty() || $programas->isEmpty() || !$periodo) {
            $this->command->warn('No hay suficientes datos para crear matrículas. Verifica que existan estudiantes, programas y períodos escolares.');
            return;
        }
        
        // Create a matricula for each student
        foreach ($students as $student) {
            // Get an appropriate program according to the student's educational level
            $programa = $this->getAppropriateProgram($student, $programas);
            
            if ($programa) {
                // Copy cost information from the educational level
                $nivelEducativo = $student->nivelEducativo;
                
                Matricula::create([
                    'estudiante_id' => $student->id,
                    'programa_id' => $programa->id,
                    'periodo_id' => $periodo->id,
                    'fecha_matricula' => now()->subDays(rand(1, 30)),
                    'estado' => 'activo',
                    'costo' => $nivelEducativo->costo ?? 0,
                    'cuota_inicial' => $nivelEducativo->cuota_inicial ?? 0,
                    'numero_cuotas' => $nivelEducativo->numero_cuotas ?? 0,
                ]);
            }
        }
    }
    
    /**
     * Get an appropriate program for the student according to their educational level
     */
    private function getAppropriateProgram($student, $programas)
    {
        $nivelNombre = $student->nivelEducativo->nombre ?? '';
        
        // Find a program that matches the educational level and student's shift
        $programa = $programas->first(function ($prog) use ($nivelNombre, $student) {
            return strpos($prog->nombre, $nivelNombre) !== false;
        });
        
        // If no specific program is found, use any program
        if (!$programa) {
            $programa = $programas->first();
        }
        
        return $programa;
    }
}