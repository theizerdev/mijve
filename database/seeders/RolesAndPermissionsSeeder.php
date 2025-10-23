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

        // Crear permisos
        $permissions = [
            // Permisos para usuarios
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view user profile',

            // Permisos para empresas
            'view empresas',
            'create empresas',
            'edit empresas',
            'delete empresas',

            // Permisos para sucursales
            'view sucursales',
            'create sucursales',
            'edit sucursales',
            'delete sucursales',

            // Permisos para sesiones activas
            'view active sessions',
            'terminate active sessions',

            // Permisos para roles
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',

            // Permisos para permisos
            'view permissions',
            'edit permissions',

            // Permisos para Periodo escolar
            'view school periods',
            'create school periods',
            'edit school periods',
            'show school periods',
            'delete school periods',
            // Permisos para los turnos
            'view turnos',
            'create turnos',
            'edit turnos',
            'show turnos',
            'delete turnos',

            // Permisos para niveles educativos
            'view niveles educativos',
            'create niveles educativos',
            'edit niveles educativos',
            'delete niveles educativos',

            // Permisos para perfil
            'view own profile',
            'edit own profile',
            'change own password',

            // Permisos para estudiantes
            'view students',
            'create students',
            'edit students',
            'delete students',

        ];

        // Crear permisos si no existen
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Crear roles si no existen
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $empresaAdminRole = Role::firstOrCreate(['name' => 'empresa-admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Asignar todos los permisos al super-admin
        $superAdminRole->syncPermissions($permissions);

        // Asignar permisos al admin
        $adminPermissions = [
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view user profile',
            'view empresas',
            'create empresas',
            'edit empresas',
            'delete empresas',
            'view sucursales',
            'create sucursales',
            'edit sucursales',
            'delete sucursales',
            'view active sessions',
            'terminate active sessions',
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'view permissions',
            'edit permissions',
            'view school periods',
            'create school periods',
            'edit school periods',
            'show school periods',
            'delete school periods',
            'view turnos',
            'create turnos',
            'edit turnos',
            'show turnos',
            'delete turnos',
            'view niveles educativos',
            'create niveles educativos',
            'edit niveles educativos',
            'delete niveles educativos',
            'view own profile',
            'edit own profile',
            'change own password',
            'view students',
            'create students',
            'edit students',
            'delete students',
        ];
        $adminRole->syncPermissions($adminPermissions);

        // Asignar permisos al empresa-admin
        $empresaAdminPermissions = [
            'view users',
            'create users',
            'edit users',
            'view empresas',
            'view sucursales',
            'view own profile',
            'edit own profile',
            'change own password',
            'view school periods',
            'view students',
            'create students',
            'edit students',
        ];
        $empresaAdminRole->syncPermissions($empresaAdminPermissions);

        // Asignar permisos al user
        $userPermissions = [
            'view own profile',
            'edit own profile',
            'change own password',
            'view school periods',
            'view students',
        ];
        $userRole->syncPermissions($userPermissions);
    }
}
