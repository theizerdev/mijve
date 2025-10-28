<?php

namespace App\Livewire\Admin\Matriculas;

use Livewire\Component;
use App\Models\Matricula;
use App\Models\Student;
use App\Models\Programa;
use App\Models\SchoolPeriod;

class Create extends Component
{
    public $student_id;
    public $programa_id;
    public $periodo_id;
    public $fecha_matricula;
    public $estado = 'activo';

    // Campos de costos
    public $costo = 0;
    public $cuota_inicial = 0;
    public $numero_cuotas = 0;

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

    public function mount()
    {
        $this->fecha_matricula = now()->format('Y-m-d');
        $this->loadData();
    }

    public function loadData()
    {
        $this->students = Student::with('nivelEducativo')->orderBy('nombres')->orderBy('apellidos')->get();
        $this->programas = Programa::where('activo', true)->orderBy('nombre')->get();
        $this->periodos = SchoolPeriod::orderBy('name')->get();
    }

    public function updatedStudentId($value)
    {
        if ($value) {
            $student = Student::with('nivelEducativo')->find($value);

            if ($student && $student->nivelEducativo) {
                $this->costo = $student->nivelEducativo->costo;
                $this->cuota_inicial = $student->nivelEducativo->cuota_inicial;
                $this->numero_cuotas = $student->nivelEducativo->numero_cuotas;
            }
        }
    }

    public function store()
    {
        // Verificar permiso para crear matrículas
        if (!auth()->user()->can('create matriculas')) {
            session()->flash('error', 'No tienes permiso para crear matrículas.');
            return;
        }

        $this->validate();

        try {
            Matricula::create([
                'estudiante_id' => $this->student_id,
                'programa_id' => $this->programa_id,
                'periodo_id' => $this->periodo_id,
                'fecha_matricula' => $this->fecha_matricula,
                'estado' => $this->estado,
                'costo' => $this->costo,
                'cuota_inicial' => $this->cuota_inicial,
                'numero_cuotas' => $this->numero_cuotas
            ]);

            session()->flash('message', 'Matrícula creada correctamente.');
            return redirect()->route('admin.matriculas.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la matrícula: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.matriculas.create')
            ->layout('components.layouts.admin', [
                'title' => 'Crear Matrícula',
                'description' => 'Registrar una nueva matrícula de estudiante'
            ]);
    }
}
