<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\Actividad;
use Livewire\Livewire;

class ActividadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $empresa;
    protected $sucursal;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear datos base
        // Asumiendo que existen las tablas y modelos, creamos datos simples
        // Si hay factories, mejor usarlos, pero esto es más seguro si no conocemos los factories
        $this->empresa = new Empresa();
        
        // Usamos forceCreate con los campos mínimos necesarios según create_empresas_table
        $this->empresa = Empresa::forceCreate([
            'razon_social' => 'Empresa Test',
            'documento' => 'DOC123456', // Obligatorio y único
            'status' => true,
            'direccion' => 'Calle Falsa 123',
        ]);

        $this->sucursal = Sucursal::forceCreate([
            'empresa_id' => $this->empresa->id,
            'nombre' => 'Sucursal Test',
            'status' => true,
            'direccion' => 'Calle Sucursal 123',
            'telefono' => '0987654321'
        ]);
        
        // Crear usuario admin
        $this->user = User::factory()->create([
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
        ]);
        
        // Bypassear permisos para el test
        $this->actingAs($this->user);
    }

    /** @test */
    public function puede_ver_listado_actividades()
    {
        Actividad::create([
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'nombre' => 'Actividad 1',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(5),
            'descripcion' => 'Desc',
            'status' => 'Activo',
            'edad_desde' => 10,
            'edad_hasta' => 20
        ]);

        $response = $this->get(route('admin.actividades.index'));
        $response->assertStatus(200);
        $response->assertSee('Actividad 1');
    }

    /** @test */
    public function puede_crear_actividad()
    {
        Livewire::test(\App\Livewire\Admin\Actividades\Create::class)
            ->set('nombre', 'Nueva Actividad')
            ->set('fecha_inicio', now()->format('Y-m-d'))
            ->set('fecha_fin', now()->addDays(5)->format('Y-m-d'))
            ->set('descripcion', 'Descripción de prueba')
            ->set('status', 'Activo')
            ->set('edad_desde', 5)
            ->set('edad_hasta', 10)
            ->set('empresa_id', $this->empresa->id)
            ->set('sucursal_id', $this->sucursal->id)
            ->call('save')
            ->assertRedirect(route('admin.actividades.index'));

        $this->assertDatabaseHas('actividads', [
            'nombre' => 'Nueva Actividad',
            'status' => 'Activo'
        ]);
    }

    /** @test */
    public function valida_campos_obligatorios()
    {
        Livewire::test(\App\Livewire\Admin\Actividades\Create::class)
            ->set('nombre', '')
            ->call('save')
            ->assertHasErrors(['nombre' => 'required']);
    }

    /** @test */
    public function valida_fechas()
    {
        // Fecha fin antes de inicio
        Livewire::test(\App\Livewire\Admin\Actividades\Create::class)
            ->set('fecha_inicio', now()->addDays(5)->format('Y-m-d'))
            ->set('fecha_fin', now()->format('Y-m-d'))
            ->call('save')
            ->assertHasErrors(['fecha_fin' => 'after']);
    }

    /** @test */
    public function valida_edades()
    {
        // Edad hasta menor que edad desde
        Livewire::test(\App\Livewire\Admin\Actividades\Create::class)
            ->set('edad_desde', 20)
            ->set('edad_hasta', 10)
            ->call('save')
            ->assertHasErrors(['edad_hasta' => 'gte']);
    }

    /** @test */
    public function puede_editar_actividad()
    {
        $actividad = Actividad::create([
            'empresa_id' => $this->empresa->id,
            'sucursal_id' => $this->sucursal->id,
            'nombre' => 'Actividad Original',
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(5),
            'descripcion' => 'Desc',
            'status' => 'Activo',
            'edad_desde' => 10,
            'edad_hasta' => 20
        ]);

        Livewire::test(\App\Livewire\Admin\Actividades\Edit::class, ['actividad' => $actividad])
            ->set('nombre', 'Actividad Editada')
            ->call('save')
            ->assertRedirect(route('admin.actividades.index'));

        $this->assertDatabaseHas('actividads', [
            'id' => $actividad->id,
            'nombre' => 'Actividad Editada'
        ]);
    }
}
