<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir módulos y sus permisos
        $modules = [
            'empresas' => [
                'access empresas',
                'create empresas',
                'edit empresas',
                'delete empresas',
            ],
            'sucursales' => [
                'access sucursales',
                'create sucursales',
                'edit sucursales',
                'delete sucursales',
            ],
            'users' => [
                'access users',
                'create users',
                'edit users',
                'delete users',
            ],
            'roles' => [
                'access roles',
                'create roles',
                'edit roles',
                'delete roles',
                'assign roles',
            ],
            'permissions' => [
                'access permissions',
                'create permissions',
                'edit permissions',
                'delete permissions',
                'assign permissions',
            ],
            'school_years' => [
                'access school years',
                'create school years',
                'edit school years',
                'delete school years',
            ],
            'school_periods' => [
                'access school periods',
                'create school periods',
                'edit school periods',
                'delete school periods',
            ],
            'educational_levels' => [
                'access educational levels',
                'create educational levels',
                'edit educational levels',
                'delete educational levels',
            ],
            'turnos' => [
                'access turnos',
                'create turnos',
                'edit turnos',
                'delete turnos',
            ],
            'students' => [
                'access students',
                'create students',
                'edit students',
                'delete students',
                'access student control',
            ],
            'active_sessions' => [
                'view active sessions',
                'delete active sessions',
            ],
            'dashboard' => [
                'access dashboard',
            ],
        ];

        // Crear permisos organizados por módulos
        foreach ($modules as $module => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(
                    ['name' => $permission],
                    ['module' => $module]
                );
            }
        }

        // Crear roles y asignar permisos
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Administrador']);
        $recepcionistaRole = Role::firstOrCreate(['name' => 'Recepcionista']);

        // Asignar todos los permisos al Super Administrador
        $superAdminRole->syncPermissions(Permission::all());

        // Asignar permisos al Administrador (todos menos los de super admin)
        $adminPermissions = Permission::whereNotIn('name', [
            'assign roles',
            'assign permissions'
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        // Asignar permisos al Recepcionista (solo de estudiantes y dashboard)
        $recepcionistaPermissions = Permission::whereIn('module', ['students', 'dashboard'])->get();
        $recepcionistaRole->syncPermissions($recepcionistaPermissions);
    }
}
