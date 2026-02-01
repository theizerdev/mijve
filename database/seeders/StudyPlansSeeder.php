<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StudyPlan;
use App\Models\StudyPlanSubject;
use App\Models\Programa;
use App\Models\NivelEducativo;
use App\Models\Subject;

class StudyPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener programas y niveles educativos existentes
        $programas = Programa::all();
        $nivelesEducativos = NivelEducativo::all();
        $subjects = Subject::all();

        if ($programas->isEmpty() || $nivelesEducativos->isEmpty() || $subjects->isEmpty()) {
            $this->command->info('No hay programas, niveles educativos o materias suficientes para crear planes de estudio.');
            return;
        }

        // Obtener empresa y sucursal del primer programa para asignar a los planes de estudio
        $empresaId = $programas->first()->empresa_id;
        $sucursalId = $programas->first()->sucursal_id;

        // Crear planes de estudio de ejemplo
        $studyPlans = [
            [
                'name' => 'Plan de Estudio - Educación General Básica',
                'code' => 'PE-EGB-2024',
                'description' => 'Plan de estudio para Educación General Básica, diseñado para proporcionar una formación integral en los niveles inicial y primario.',
                'program_id' => $programas->first()->id,
                'educational_level_id' => $nivelesEducativos->first()->id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalId,
                'total_credits' => 120,
                'total_hours' => 1800,
                'duration_years' => 6,
                'duration_semesters' => 12,
                'status' => 'active',
                'effective_date' => now(),
                'expiration_date' => now()->addYears(5),
                'is_default' => true,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Plan de Estudio - Bachillerato General',
                'code' => 'PE-BG-2024',
                'description' => 'Plan de estudio para Bachillerato General, orientado a la formación académica integral y preparación para educación superior.',
                'program_id' => $programas->skip(1)->first() ? $programas->skip(1)->first()->id : $programas->first()->id,
                'educational_level_id' => $nivelesEducativos->skip(1)->first() ? $nivelesEducativos->skip(1)->first()->id : $nivelesEducativos->first()->id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalId,
                'total_credits' => 180,
                'total_hours' => 2400,
                'duration_years' => 5,
                'duration_semesters' => 10,
                'status' => 'active',
                'effective_date' => now(),
                'expiration_date' => now()->addYears(5),
                'is_default' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'name' => 'Plan de Estudio - Educación Media Técnica',
                'code' => 'PE-EMT-2024',
                'description' => 'Plan de estudio para Educación Media Técnica, combinando formación académica con habilidades técnicas y profesionales.',
                'program_id' => $programas->skip(2)->first() ? $programas->skip(2)->first()->id : $programas->first()->id,
                'educational_level_id' => $nivelesEducativos->skip(2)->first() ? $nivelesEducativos->skip(2)->first()->id : $nivelesEducativos->first()->id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalId,
                'total_credits' => 200,
                'total_hours' => 2800,
                'duration_years' => 6,
                'duration_semesters' => 12,
                'status' => 'draft',
                'effective_date' => now()->addMonths(6),
                'expiration_date' => now()->addYears(6),
                'is_default' => false,
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        // Crear los planes de estudio
        foreach ($studyPlans as $planData) {
            $studyPlan = StudyPlan::create($planData);
            
            // Si es el plan por defecto, desactivar otros planes por defecto del mismo programa
            if ($studyPlan->is_default) {
                StudyPlan::where('program_id', $studyPlan->program_id)
                    ->where('id', '!=', $studyPlan->id)
                    ->update(['is_default' => false]);
            }

            // Asignar algunas materias al plan de estudio
            $this->assignSubjectsToStudyPlan($studyPlan, $subjects);
        }

        $this->command->info('Planes de estudio creados exitosamente.');
    }

    /**
     * Asignar materias al plan de estudio
     */
    private function assignSubjectsToStudyPlan($studyPlan, $subjects)
    {
        // Tomar las primeras 5-7 materias para asignarlas al plan
        $subjectsToAssign = $subjects->take(rand(5, 7));
        
        $order = 1;
        foreach ($subjectsToAssign as $subject) {
            StudyPlanSubject::create([
                'study_plan_id' => $studyPlan->id,
                'subject_id' => $subject->id,
                'semester' => rand(1, 2), // Semestre 1 o 2
                'year' => rand(1, $studyPlan->duration_years), // Año aleatorio dentro de la duración
                'subject_type' => rand(0, 1) ? 'mandatory' : 'elective', // Obligatoria o electiva
                'order' => $order,
                'is_active' => true,
            ]);
            $order++;
        }
    }
}