<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Admin\Empresas\Index as EmpresasIndex;
use App\Livewire\Admin\Empresas\Create as EmpresasCreate;
use App\Livewire\Admin\Empresas\Edit as EmpresasEdit;
use App\Livewire\Admin\Sucursales\Index as SucursalesIndex;
use App\Livewire\Admin\Sucursales\Create as SucursalesCreate;
use App\Livewire\Admin\Sucursales\Edit as SucursalesEdit;
use App\Livewire\Admin\Sucursales\Show as SucursalesShow;
use App\Livewire\Admin\Users\Index as UsersIndex;
use App\Livewire\Admin\Users\Create as UsersCreate;
use App\Livewire\Admin\Users\Edit as UsersEdit;
use App\Livewire\Admin\Roles\Index as RolesIndex;
use App\Livewire\Admin\Roles\Create as RolesCreate;
use App\Livewire\Admin\Roles\Edit as RolesEdit;
use App\Livewire\Admin\Roles\Show as RolesShow;
use App\Livewire\Admin\Permissions\Index as PermissionsIndex;
use App\Livewire\Admin\Permissions\Create as PermissionsCreate;
use App\Livewire\Admin\Permissions\Edit as PermissionsEdit;
use App\Livewire\Admin\SchoolPeriods\Index as SchoolYearsIndex;
use App\Livewire\Admin\SchoolPeriods\Create as SchoolYearsCreate;
use App\Livewire\Admin\SchoolPeriods\Edit as SchoolYearsEdit;
use App\Livewire\Admin\SchoolPeriods\Show as SchoolYearsShow;
use App\Livewire\Admin\SchoolPeriods\Index as SchoolPeriodsIndex;
use App\Livewire\Admin\SchoolPeriods\Create as SchoolPeriodsCreate;
use App\Livewire\Admin\SchoolPeriods\Edit as SchoolPeriodsEdit;
use App\Livewire\Admin\SchoolPeriods\Show as SchoolPeriodsShow;
use App\Livewire\Admin\NivelesEducativos\Index as NivelesEducativosIndex;
use App\Livewire\Admin\NivelesEducativos\Create as NivelesEducativosCreate;
use App\Livewire\Admin\NivelesEducativos\Edit as NivelesEducativosEdit;
use App\Livewire\Admin\Turnos\Index as TurnosIndex;
use App\Livewire\Admin\Turnos\Create as TurnosCreate;
use App\Livewire\Admin\Turnos\Edit as TurnosEdit;
use App\Livewire\Admin\Students\Index as StudentsIndex;
use App\Livewire\Admin\Students\Create as StudentsCreate;
use App\Livewire\Admin\Students\Edit as StudentsEdit;
use App\Livewire\Admin\Students\Show as StudentsShow;
use App\Livewire\Admin\Students\QrAccess;
use App\Livewire\Admin\ActiveSessions;



// Empresas
Route::get('/empresas', EmpresasIndex::class)->name('empresas.index');
Route::get('/empresas/crear', EmpresasCreate::class)->name('empresas.create');
Route::get('/empresas/{empresa}/editar', EmpresasEdit::class)->name('empresas.edit');

// Sucursales
Route::get('/sucursales', SucursalesIndex::class)->name('sucursales.index');
Route::get('/sucursales/crear', SucursalesCreate::class)->name('sucursales.create');
Route::get('/sucursales/{sucursal}/editar', SucursalesEdit::class)->name('sucursales.edit');
Route::get('/sucursales/{sucursal}', SucursalesShow::class)->name('sucursales.show');

// Usuarios
Route::get('/usuarios', UsersIndex::class)->name('users.index');
Route::get('/usuarios/crear', UsersCreate::class)->name('users.create');
Route::get('/usuarios/{user}/editar', UsersEdit::class)->name('users.edit');
 // Perfil de usuario
Route::prefix('profile')->group(function () {
    Route::get('/', \App\Livewire\Admin\Users\Profile\Index::class)->name('users.profile');
    Route::get('/{user_id}/password', \App\Livewire\Admin\Users\Profile\ChangePassword::class)->name('users.password');
    Route::get('/{user_id}/history', \App\Livewire\Admin\Users\Profile\HistoryUser::class)->name('users.history');
});

// Roles
Route::get('/roles', RolesIndex::class)->name('roles.index');
Route::get('/roles/crear', RolesCreate::class)->name('roles.create');
Route::get('/roles/{role}/editar', RolesEdit::class)->name('roles.edit');
Route::get('/roles/{role}', RolesShow::class)->name('roles.show');

// Permisos
Route::get('/permisos', PermissionsIndex::class)->name('permissions.index');
Route::get('/permisos/crear', PermissionsCreate::class)->name('permissions.create');
Route::get('/permisos/{permission}/editar', PermissionsEdit::class)->name('permissions.edit');

// Años escolares
Route::get('/school-years', SchoolYearsIndex::class)->name('school-years.index');
Route::get('/school-years/crear', SchoolYearsCreate::class)->name('school-years.create');
Route::get('/school-years/{schoolYear}/editar', SchoolYearsEdit::class)->name('school-years.edit');
Route::get('/school-years/{schoolYear}', SchoolYearsShow::class)->name('school-years.show');

// Periodos escolares
Route::get('/school-periods', SchoolPeriodsIndex::class)->name('school-periods.index');
Route::get('/school-periods/crear', SchoolPeriodsCreate::class)->name('school-periods.create');
Route::get('/school-periods/{schoolPeriod}/editar', SchoolPeriodsEdit::class)->name('school-periods.edit');
Route::get('/school-periods/{schoolPeriod}', SchoolPeriodsShow::class)->name('school-periods.show');

// Niveles Educativos
Route::get('/niveles-educativos', NivelesEducativosIndex::class)->name('niveles-educativos.index');
Route::get('/niveles-educativos/crear', NivelesEducativosCreate::class)->name('niveles-educativos.create');
Route::get('/niveles-educativos/{nivel}/editar', NivelesEducativosEdit::class)->name('niveles-educativos.edit');

// Turnos
Route::get('/turnos', TurnosIndex::class)->name('turnos.index');
Route::get('/turnos/crear', TurnosCreate::class)->name('turnos.create');
Route::get('/turnos/{turno}/editar', TurnosEdit::class)->name('turnos.edit');

// Estudiantes
Route::get('/students', StudentsIndex::class)->name('students.index');
Route::get('/students/crear', StudentsCreate::class)->name('students.create');
Route::get('/students/{student}/editar', StudentsEdit::class)->name('students.edit');
Route::get('/students/{student}', StudentsShow::class)->name('students.show');
Route::get('/students/qr-access', QrAccess::class)->name('students.qr-access');
Route::get('/access/students', QrAccess::class)->name('access.students');


// Sesiones activas
Route::get('/active-sessions', ActiveSessions::class)->name('active-sessions.index');

// Monitoreo
Route::prefix('monitoreo')->as('monitoreo.')->group(function () {
    Route::get('/servidor', \App\Livewire\Admin\Monitoreo\Servidor::class)->name('servidor');
    Route::get('/base-datos', \App\Livewire\Admin\Monitoreo\BaseDatos::class)->name('base-datos');
    Route::get('/estudiantes', \App\Livewire\Admin\Monitoreo\Estudiantes::class)->name('estudiantes');
    Route::get('/accesos', \App\Livewire\Admin\Monitoreo\Accesos::class)->name('accesos');
});
