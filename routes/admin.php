<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;

// Proteger todas las rutas de administración con el middleware de autenticación
Route::prefix('admin')
    ->middleware(['auth', 'verified'])
    ->name('admin.')
    ->group(function () {
        // Dashboard de administración
        Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');

        // Ruta para sesiones activas usando Livewire
        Route::get('/active-sessions', \App\Livewire\Admin\ActiveSessions::class)->name('active-sessions.index');

        // Rutas para empresas
        Route::prefix('empresas')->group(function () {
            Route::get('/', \App\Livewire\Admin\Empresas\Index::class)->name('empresas.index');
            Route::get('/create', \App\Livewire\Admin\Empresas\Create::class)->name('empresas.create');
            Route::get('/{empresa}', \App\Livewire\Admin\Empresas\Show::class)->name('empresas.show');
            Route::get('/{empresa}/edit', \App\Livewire\Admin\Empresas\Edit::class)->name('empresas.edit');
        });

        // Rutas para sucursales
        Route::prefix('sucursales')->group(function () {
            Route::get('/', \App\Livewire\Admin\Sucursales\Index::class)->name('sucursales.index');
            Route::get('/create', \App\Livewire\Admin\Sucursales\Create::class)->name('sucursales.create');
            Route::get('/{sucursal}', \App\Livewire\Admin\Sucursales\Show::class)->name('sucursales.show');
            Route::get('/{sucursal}/edit', \App\Livewire\Admin\Sucursales\Edit::class)->name('sucursales.edit');
        });

        // Rutas para usuarios
        Route::prefix('users')->group(function () {
            Route::get('/', \App\Livewire\Admin\Users\Index::class)->name('users.index');
            Route::get('/create', \App\Livewire\Admin\Users\Create::class)->name('users.create');
            Route::get('/{user}', \App\Livewire\Admin\Users\Show::class)->name('users.show');
            Route::get('/{user}/edit', \App\Livewire\Admin\Users\Edit::class)->name('users.edit');
        });

        // Rutas para roles
        Route::prefix('roles')->group(function () {
            Route::get('/', \App\Livewire\Admin\Roles\Index::class)->name('roles.index');
            Route::get('/create', \App\Livewire\Admin\Roles\Create::class)->name('roles.create');
            Route::get('/{role}', \App\Livewire\Admin\Roles\Show::class)->name('roles.show');
            Route::get('/{role}/edit', \App\Livewire\Admin\Roles\Edit::class)->name('roles.edit');
        });
        // Rutas para roles
        Route::prefix('school-periods')->group(function () {
            Route::get('/', \App\Livewire\Admin\SchoolPeriods\Index::class)->name('school-periods.index');
            Route::get('/create', \App\Livewire\Admin\SchoolPeriods\Create::class)->name('school-periods.create');
            Route::get('/{schoolPeriod}/edit', \App\Livewire\Admin\SchoolPeriods\Edit::class)->name('school-periods.edit');
            Route::get('/{schoolPeriod}', \App\Livewire\Admin\SchoolPeriods\Show::class)->name('school-periods.show');
        });

        // Rutas para turnos
        Route::prefix('turnos')->group(function () {
            Route::get('/', \App\Livewire\Admin\Turnos\Index::class)->name('turnos.index');
            Route::get('/create', \App\Livewire\Admin\Turnos\Create::class)->name('turnos.create');
            Route::get('/{turno}', \App\Livewire\Admin\Turnos\Show::class)->name('turnos.show');
            Route::get('/{turno}/edit', \App\Livewire\Admin\Turnos\Edit::class)->name('turnos.edit');
        });

        // Perfil de usuario
        Route::prefix('profile')->group(function () {
            Route::get('/', \App\Livewire\Admin\Users\Profile\Index::class)->name('users.profile');
            Route::get('/{user_id}/password', \App\Livewire\Admin\Users\Profile\ChangePassword::class)->name('users.password');
            Route::get('/{user_id}/history', \App\Livewire\Admin\Users\Profile\HistoryUser::class)->name('users.history');
        });

        // Rutas para niveles educativos
        Route::prefix('niveles-educativos')->group(function () {
            Route::get('/', \App\Livewire\Admin\NivelesEducativos\Index::class)->name('niveles-educativos.index');
            Route::get('/create', \App\Livewire\Admin\NivelesEducativos\Create::class)->name('niveles-educativos.create');
            Route::get('/{nivel}', \App\Livewire\Admin\NivelesEducativos\Show::class)->name('niveles-educativos.show');
            Route::get('/{nivel}/edit', \App\Livewire\Admin\NivelesEducativos\Edit::class)->name('niveles-educativos.edit');
        });
        // Rutas para niveles educativos
        Route::prefix('students')->group(function () {
            Route::get('/', \App\Livewire\Admin\Students\Index::class)->name('students.index');
            Route::get('/create', \App\Livewire\Admin\Students\Create::class)->name('students.create');
            Route::get('/{student}', \App\Livewire\Admin\Students\Show::class)->name('students.show');
            Route::get('/{student}/edit', \App\Livewire\Admin\Students\Edit::class)->name('students.edit');
        });

    });
