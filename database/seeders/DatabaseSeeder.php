<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

      $this->call([
          RolesAndPermissionsSeeder::class,
          EmpresaSeeder::class,
          SucursalSeeder::class,
          UsersTableSeeder::class,
          AssignRolesToUsersSeeder::class,
          EducationalLevelSeeder::class,
          ShiftSeeder::class,
          SchoolPeriodSeeder::class,
      ]);
    }
}