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
                'export students',
                'import students',
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
            ],
            // Módulo de pagos
            'pagos' => [
                'access pagos',
                'create pagos',
                'edit pagos',
                'delete pagos',
                'view pagos',
            ],
            // Módulo de conceptos de pago
            'conceptos_pago' => [
                'access conceptos pago',
                'create conceptos pago',
                'edit conceptos pago',
                'delete conceptos pago',
                'view conceptos pago',
            ],
            // Módulo de programas
            'programas' => [
                'access programas',
                'create programas',
                'edit programas',
                'delete programas',
                'view programas',
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
            ],
            // Módulo de actividad
            'activity_log' => [
                'access activity log',
                'view activity log',
                'delete activity log',
                'export activity log',
            ],
            // Módulo de mensajería interna
            'mensajeria' => [
                'access mensajeria',
                'create mensajeria',
                'edit mensajeria',
                'delete mensajeria',
                'send mensajeria',
                'receive mensajeria',
                'manage mensajeria templates',
            ],
            // Módulo de biblioteca digital
            'biblioteca' => [
                'access biblioteca',
                'create biblioteca',
                'edit biblioteca',
                'delete biblioteca',
                'upload biblioteca',
                'download biblioteca',
                'share biblioteca',
                'manage biblioteca categories',
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
        $recepcionistaRole = Role::firstOrCreate(['name' => 'Recepcionista']);

        // Asignar todos los permisos al Super Administrador
        $superAdminRole->syncPermissions(Permission::all());

        // Asignar permisos al Administrador (todos menos los de super admin)
        $adminPermissions = Permission::whereNotIn('name', [
            'assign roles',
            'assign permissions'
        ])->get();
        $adminRole->syncPermissions($adminPermissions);

        // Asignar permisos al Recepcionista (solo de estudiantes, matrículas, pagos y dashboard básico)
        $recepcionistaPermissions = Permission::whereIn('module', [
            'students',
            'matriculas',
            'pagos'
        ])->orWhereIn('name', [
            'access dashboard',
            'dashboard.alerts',
            'dashboard.academic',
            'dashboard.access'
        ])->get();
        $recepcionistaRole->syncPermissions($recepcionistaPermissions);

        // Asignar permisos de mensajería, biblioteca, series y cajas a Administradores y Super Administradores
        $mensajeriaBibliotecaSeriesCajasPermissions = Permission::whereIn('module', [
            'mensajeria',
            'biblioteca',
            'series',
            'cajas'
        ])->get();
        
        $superAdminRole->givePermissionTo($mensajeriaBibliotecaSeriesCajasPermissions);
        $adminRole->givePermissionTo($mensajeriaBibliotecaSeriesCajasPermissions);
    }
}