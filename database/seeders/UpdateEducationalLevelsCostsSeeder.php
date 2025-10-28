<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationalLevel;

class UpdateEducationalLevelsCostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Actualizar todos los niveles educativos existentes para establecer valores predeterminados
        EducationalLevel::whereNull('costo_matricula')->update(['costo_matricula' => 0]);
        EducationalLevel::whereNull('costo_mensualidad')->update(['costo_mensualidad' => 0]);
        
        // Opcionalmente, puedes establecer valores específicos para ciertos niveles educativos
        // Por ejemplo, si tienes niveles con nombres específicos:
        /*
        EducationalLevel::where('nombre', 'Educación Inicial')->update([
            'costo_matricula' => 100.00,
            'costo_mensualidad' => 50.00
        ]);
        
        EducationalLevel::where('nombre', 'Primaria')->update([
            'costo_matricula' => 150.00,
            'costo_mensualidad' => 75.00
        ]);
        */
    }
}
