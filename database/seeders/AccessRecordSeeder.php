<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;
use App\Models\AccessRecord;
use Carbon\Carbon;

class AccessRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunos estudiantes y usuarios existentes
        $students = Student::limit(5)->get();
        $users = User::limit(3)->get();

        if ($students->count() === 0 || $users->count() === 0) {
            $this->command->info("No hay suficientes estudiantes o usuarios para crear registros de acceso.");
            return;
        }

        // Crear registros de acceso para hoy
        foreach ($students as $student) {
            // Registro de entrada
            AccessRecord::create([
                'student_id' => $student->id,
                'date' => now()->toDateString(),
                'entry_time' => now()->subHours(2)->toTimeString(),
                'entry_user_id' => $users->random()->id,
                'access_type' => 'entry',
                'access_method' => 'manual',
                'reference_code' => 'MANUAL-' . $student->id . '-' . now()->timestamp,
                'observations' => 'Entrada por código manual',
            ]);

            // Algunos estudiantes también tienen registro de salida
            if (rand(0, 1) === 1) {
                AccessRecord::create([
                    'student_id' => $student->id,
                    'date' => now()->toDateString(),
                    'entry_time' => now()->subHours(2)->toTimeString(),
                    'exit_time' => now()->subHours(1)->toTimeString(),
                    'entry_user_id' => $users->random()->id,
                    'exit_user_id' => $users->random()->id,
                    'access_type' => 'exit',
                    'access_method' => 'qr',
                    'reference_code' => 'QR-' . $student->id . '-' . now()->timestamp,
                    'observations' => 'Salida por código QR',
                ]);
            }
        }

        // Crear registros de acceso para ayer
        foreach ($students as $student) {
            AccessRecord::create([
                'student_id' => $student->id,
                'date' => now()->subDay()->toDateString(),
                'entry_time' => now()->subDay()->subHours(2)->toTimeString(),
                'entry_user_id' => $users->random()->id,
                'access_type' => 'entry',
                'access_method' => 'manual',
                'reference_code' => 'MANUAL-' . $student->id . '-' . now()->subDay()->timestamp,
                'observations' => 'Entrada por código manual',
            ]);
        }

        $this->command->info("Registros de acceso de ejemplo creados exitosamente.");
    }
}

