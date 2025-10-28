<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        $this->call([
            RolesAndPermissionsSeeder::class,
            UsersTableSeeder::class,
            EmpresaSeeder::class,
            SucursalSeeder::class,
            ShiftSeeder::class,
            EducationalLevelSeeder::class,
            SchoolPeriodSeeder::class,
            StudentSeeder::class,
            ProgramaSeeder::class,
            ConceptoPagoSeeder::class,
            MatriculaSeeder::class,
            PagoSeeder::class,
        ]);
    }
}
