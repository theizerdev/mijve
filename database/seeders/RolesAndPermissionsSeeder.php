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

            // Permisos para permisos
            'view school periods',
            'create school periods',
            'edit school periods',
            'show school periods',
            'delete school periods',

            // Permisos para perfil
            'view own profile',
            'edit own profile',
            'change own password',

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
            'view own profile',
            'edit own profile',
            'change own password',
            'view school periods',
            'create school periods',
            'edit school periods',
            'delete school periods',
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
        ];
        $empresaAdminRole->syncPermissions($empresaAdminPermissions);

        // Asignar permisos al user
        $userPermissions = [
            'view own profile',
            'edit own profile',
            'change own password',
            'view school periods',
        ];
        $userRole->syncPermissions($userPermissions);
    }
}
