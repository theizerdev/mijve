<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\EducationalLevel;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Delete all records from the students table to avoid duplicates
        DB::table('students')->delete();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Get all educational levels
        $niveles = EducationalLevel::all();

        if ($niveles->isEmpty()) {
            $this->command->warn('No hay niveles educativos disponibles. Ejecuta EducationalLevelSeeder primero.');
            return;
        }

        // Create students for the initial education level (under 8 years old)
        $nivelInicial = $niveles->where('nombre', 'Educación Inicial')->first();
        if ($nivelInicial) {
            $this->createStudentsForLevel($nivelInicial, 10, 'inicial');
        }

        // Create students for the primary level
        $nivelPrimaria = $niveles->where('nombre', 'Primaria')->first();
        if ($nivelPrimaria) {
            $this->createStudentsForLevel($nivelPrimaria, 15, 'primaria');
        }

        // Create students for the secondary level
        $nivelSecundaria = $niveles->where('nombre', 'Secundaria')->first();
        if ($nivelSecundaria) {
            $this->createStudentsForLevel($nivelSecundaria, 15, 'secundaria');
        }
    }

    /**
     * Create students for a specific educational level
     */
    private function createStudentsForLevel($nivel, $count, $prefix)
    {
        $faker = \Faker\Factory::create('es_ES');

        for ($i = 1; $i <= $count; $i++) {
            // Calculate birth date according to level
            $fechaNacimiento = $this->calculateBirthDate($prefix);

            Student::create([
                'nombres' => $faker->firstName,
                'apellidos' => $faker->lastName,
                'fecha_nacimiento' => $fechaNacimiento,
                'codigo' => str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'documento_identidad' => $this->generateDocumentNumber($prefix, $i),
                'grado' => $this->assignGrade($prefix, $i),
                'seccion' => $faker->randomElement(['A', 'B', 'C']),
                'nivel_educativo_id' => $nivel->id,
                'turno_id' => rand(1, 3), // Using valid turno IDs (1-3)
                'school_periods_id' => 1, // Assuming school period ID 1 exists
                'correo_electronico' => $faker->unique()->safeEmail,
                'status' => true,
                'representante_nombres' => $faker->firstName,
                'representante_apellidos' => $faker->lastName,
                'representante_documento_identidad' => $this->generateRepresentativeDocument(),
                'representante_telefonos' => json_encode([$faker->phoneNumber]),
                'representante_correo' => $faker->safeEmail,
            ]);
        }
    }

    /**
     * Calculate birth date according to educational level
     */
    private function calculateBirthDate($level)
    {
        $faker = \Faker\Factory::create('es_ES');

        switch ($level) {
            case 'inicial':
                // Under 8 years old (3-7 years)
                $yearsAgo = rand(3, 7);
                break;
            case 'primaria':
                // Typical age 6-12 years
                $yearsAgo = rand(6, 12);
                break;
            case 'secundaria':
                // Typical age 12-18 years
                $yearsAgo = rand(12, 18);
                break;
            default:
                $yearsAgo = rand(5, 15);
        }

        return Carbon::now()->subYears($yearsAgo)->subMonths(rand(0, 11))->subDays(rand(0, 30));
    }

    /**
     * Generate document number according to level
     */
    private function generateDocumentNumber($prefix, $index)
    {
        // For initial level students (under 8 years old) use special codes
        if ($prefix === 'inicial') {
            return 'MENOR' . str_pad($index, 5, '0', STR_PAD_LEFT);
        }

        // For other levels use DNI
        return 'DNI' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Assign grade according to educational level
     */
    private function assignGrade($level, $index)
    {
        switch ($level) {
            case 'inicial':
                return $index <= 5 ? 'Nivel 1' : 'Nivel 2';
            case 'primaria':
                $grades = ['1ro', '2do', '3ro', '4to', '5to', '6to'];
                return $grades[($index - 1) % count($grades)];
            case 'secundaria':
                $grades = ['1ro', '2do', '3ro', '4to', '5to'];
                return $grades[($index - 1) % count($grades)];
            default:
                return 'N/A';
        }
    }

    /**
     * Generate representative document
     */
    private function generateRepresentativeDocument()
    {
        return 'DNI' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
    }
}