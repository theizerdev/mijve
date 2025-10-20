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
        $superAdminRole = Role::where('name', 'super-admin')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $empresaAdminRole = Role::where('name', 'empresa-admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // Asignar rol de super-admin al primer usuario creado (asumimos que es el administrador principal)
        $firstUser = User::orderBy('id')->first();
        if ($firstUser) {
            $firstUser->assignRole($superAdminRole);
        }

        // Asignar roles a otros usuarios de ejemplo
        $users = User::all();
        foreach ($users as $user) {
            // Si el usuario no tiene rol asignado, asignarle el rol de usuario regular
            if ($user->roles->count() == 0) {
                $user->assignRole($userRole);
            }
        }
    }
}
