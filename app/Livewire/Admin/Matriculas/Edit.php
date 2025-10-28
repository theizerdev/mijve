<?php

namespace App\Livewire\Admin\Matriculas;

use Livewire\Component;
use App\Models\Matricula;
use App\Models\Student;
use App\Models\Programa;
use App\Models\SchoolPeriod;

class Edit extends Component
{
    public $matricula;
    public $student_id;
    public $programa_id;
    public $periodo_id;
    public $fecha_matricula;
    public $estado;
    
    // Campos de costos
    public $costo;
    public $cuota_inicial;
    public $numero_cuotas;

    public $students = [];
    public $programas = [];
    public $periodos = [];

    protected $rules = [
        'student_id' => 'required|exists:students,id',
        'programa_id' => 'required|exists:programas,id',
        'periodo_id' => 'required|exists:school_periods,id',
        'fecha_matricula' => 'required|date',
        'estado' => 'required|in:activo,inactivo,graduado',
        'costo' => 'required|numeric|min:0',
        'cuota_inicial' => 'required|numeric|min:0',
        'numero_cuotas' => 'required|integer|min:0'
    ];

    public function mount(Matricula $matricula)
    {
        $this->matricula = $matricula;
        $this->student_id = $matricula->estudiante_id;
        $this->programa_id = $matricula->programa_id;
        $this->periodo_id = $matricula->periodo_id;
        $this->fecha_matricula = $matricula->fecha_matricula->format('Y-m-d');
        $this->estado = $matricula->estado;
        $this->costo = $matricula->costo;
        $this->cuota_inicial = $matricula->cuota_inicial;
        $this->numero_cuotas = $matricula->numero_cuotas;
        
        $this->loadData();
    }

    public function loadData()
    {
        $this->students = Student::orderBy('nombres')->orderBy('apellidos')->get();
        $this->programas = Programa::where('activo', true)->orderBy('nombre')->get();
        $this->periodos = SchoolPeriod::orderBy('name')->get();
    }

    public function update()
    {
        // Verificar permiso para editar matrículas
        if (!auth()->user()->can('edit matriculas')) {
            session()->flash('error', 'No tienes permiso para editar matrículas.');
            return;
        }

        $this->validate();

        try {
            $this->matricula->update([
                'estudiante_id' => $this->student_id,
                'programa_id' => $this->programa_id,
                'periodo_id' => $this->periodo_id,
                'fecha_matricula' => $this->fecha_matricula,
                'estado' => $this->estado,
                'costo' => $this->costo,
                'cuota_inicial' => $this->cuota_inicial,
                'numero_cuotas' => $this->numero_cuotas
            ]);

            session()->flash('message', 'Matrícula actualizada correctamente.');
            return redirect()->route('admin.matriculas.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar la matrícula: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.matriculas.edit')
            ->layout('components.layouts.admin', [
                'title' => 'Editar Matrícula',
                'description' => 'Actualizar datos de matrícula de estudiante'
            ]);
    }
}