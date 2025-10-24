<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRolesToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los roles
        $superadminRole = Role::where('name', 'Super Administrador')->first();

        $adminRole = Role::where('name', 'Administrador')->first();
        $receptionistRole = Role::where('name', 'Recepcionista')->first();

        // Asignar rol de administrador al primer usuario creado (asumimos que es el administrador principal)
        $firstUser = User::orderBy('id')->first();
        if ($firstUser) {
            $firstUser->assignRole($superadminRole);
        }

        // Asignar roles a otros usuarios de ejemplo
        $users = User::all();
        foreach ($users as $user) {
            // Si el usuario no tiene rol asignado, asignarle el rol de usuario regular
            if ($user->roles->count() == 0) {
                $user->assignRole($receptionistRole);
            }
        }
    }
}
