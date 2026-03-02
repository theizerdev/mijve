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
            'paises' => [
                'access paises',
                'create paises',
                'edit paises',
                'delete paises',
            ],
            'sucursales' => [
                'access sucursales',
                'create sucursales',
                'edit sucursales',
                'delete sucursales',
            ],
            'extensiones' => [
                'access extensiones',
                'create extensiones',
                'edit extensiones',
                'delete extensiones',
            ],
            'actividades' => [
                'access actividades',
                'create actividades',
                'edit actividades',
                'delete actividades',
            ],
            'participantes' => [
                'access participantes',
                'create participantes',
                'edit participantes',
                'delete participantes',
            ],
            'metodos_pago' => [
                'access metodos_pago',
                'create metodos_pago',
                'edit metodos_pago',
                'delete metodos_pago',
            ],
            'pagos' => [
                'access pagos',
                'create pagos',
                'edit pagos',
                'delete pagos',
                'approve pagos',
                'reject pagos',
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
    
            'active_sessions' => [
                'view active sessions',
                'delete active sessions',
            ],
            'dashboard' => [
                'access dashboard',
                'dashboard.alerts',
                'dashboard.financial',
                'dashboard.academic',
                'dashboard.access',
                'dashboard.charts',
            ],
            'niveles_educativos' => [
                'access niveles educativos',
                'create niveles educativos',
                'edit niveles educativos',
                'delete niveles educativos',
            ],
            'monitoreo' => [
                'access monitoreo',
                'view monitoreo servidor',
                'view monitoreo base-datos',
                'view monitoreo estudiantes',
                'view monitoreo accesos',
                'export monitoreo accesos',
            ],
            // Módulo de matrículas
            'matriculas' => [
                'access matriculas',
                'create matriculas',
                'edit matriculas',
                'delete matriculas',
                'view matriculas',
                'cambiar cuotas matriculas',
            ],
            // Módulo de pagos
            'pagos' => [
                'access pagos',
                'create pagos',
                'edit pagos',
                'delete pagos',
                'view pagos',
            ],
            // Módulo de reportes
            'reportes' => [
                'access reportes',
                'view estado cuentas',
                'view resumen pagos',
                'view morosidad',
                'view ingresos totales',
                'view historico matriculas',
                'export reportes',
                // Reportes Académicos - Fase 1
                'view estadisticas calificaciones materia',
                'view rendimiento estudiantil periodo',
                'view asistencia evaluaciones',
                'view boletines calificaciones',
            ],
            // Módulo de actividad
            'activity_log' => [
                'access activity log',
                'view activity log',
                'delete activity log',
                'export activity log',
            ],
            // Módulo de exportación de base de datos
            'database_export' => [
                'access database export',
                'export database',
            ],

            // Módulo de series de documentos
            'series' => [
                'access series',
                'create series',
                'edit series',
                'delete series',
            ],
            // Módulo de cajas
            'cajas' => [
                'access cajas',
                'create cajas',
                'edit cajas',
                'delete cajas',
                'view cajas',
            ],
            // Módulo de tasas de cambio
            'exchange_rates' => [
                'view exchange-rates',
                'fetch exchange-rates',
                'edit exchange-rates',
                'manage exchange-rates',
            ],

            // Módulo de WhatsApp
            'whatsapp' => [
                'access whatsapp',
                'create whatsapp templates',
                'edit whatsapp templates',
                'delete whatsapp templates',
                'send whatsapp messages',
                'schedule whatsapp messages',
                'view whatsapp statistics',
                'export whatsapp reports',
                'retry failed whatsapp messages',
                'view whatsapp retry statistics',
                'manage whatsapp auto retry',
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
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Administrador']);
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $liderRole = Role::firstOrCreate(['name' => 'Líder de Jóvenes']);

        // Asignar todos los permisos al Super Administrador
        $superAdminRole->syncPermissions(Permission::all());

        // Asignar permisos al Administrador (todos menos los de super admin)
        $adminPermissions = Permission::whereNotIn('name', [
            'assign roles',
            'assign permissions'
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        // Asignar permisos al Líder de Jóvenes (Solo Participantes y Pagos)
        $liderPermissions = Permission::whereIn('module', [
            'participantes',
            'pagos'
        ])->get();
        $liderRole->syncPermissions($liderPermissions);
    }
}