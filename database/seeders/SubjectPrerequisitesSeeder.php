<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use App\Models\SubjectPrerequisite;

class SubjectPrerequisitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunas materias para crear prerrequisitos de ejemplo
        $subjects = Subject::all();
        
        if ($subjects->count() < 3) {
            $this->command->info('Se necesitan al menos 3 materias para crear prerrequisitos de ejemplo.');
            return;
        }

        // Crear prerrequisitos de ejemplo
        $prerequisites = [
            [
                'subject_id' => $subjects[2]->id, // Tercera materia
                'prerequisite_subject_id' => $subjects[0]->id, // Primera materia como prerrequisito
                'type' => 'mandatory',
                'notes' => 'Es necesario haber aprobado Matemáticas Básica antes de tomar Cálculo I',
                'is_active' => true,
            ],
            [
                'subject_id' => $subjects[2]->id, // Tercera materia
                'prerequisite_subject_id' => $subjects[1]->id, // Segunda materia como prerrequisito
                'type' => 'recommended',
                'notes' => 'Se recomienda haber tomado Álgebra Lineal antes de Cálculo I',
                'is_active' => true,
            ],
            [
                'subject_id' => $subjects[3]->id, // Cuarta materia (si existe)
                'prerequisite_subject_id' => $subjects[2]->id, // Tercera materia como prerrequisito
                'type' => 'mandatory',
                'notes' => 'Cálculo I es prerrequisito obligatorio para Cálculo II',
                'is_active' => true,
            ],
        ];

        // Solo crear prerrequisitos si hay suficientes materias
        foreach ($prerequisites as $prerequisite) {
            if (isset($prerequisite['subject_id']) && isset($prerequisite['prerequisite_subject_id'])) {
                // Verificar que no exista un prerrequisito duplicado
                $exists = SubjectPrerequisite::where([
                    'subject_id' => $prerequisite['subject_id'],
                    'prerequisite_subject_id' => $prerequisite['prerequisite_subject_id']
                ])->exists();

                if (!$exists) {
                    SubjectPrerequisite::create($prerequisite);
                }
            }
        }

        $this->command->info('Prerrequisitos de materias creados exitosamente.');
    }
}