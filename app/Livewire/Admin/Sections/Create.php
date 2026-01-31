<?php

namespace App\Livewire\Admin\Sections;

use App\Models\Section;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\SchoolPeriod;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Traits\HasDynamicLayout;

class Create extends Component
{
    use HasDynamicLayout;
    // Propiedades del formulario
    public $code;
    public $name;
    public $school_period_id;
    public $subject_id;
    public $teacher_id;
    public $classroom_id;
    public $capacity;
    public $empresa_id;
    public $sucursal_id;
    public $description;
    public $status = 'active';

    // Listas para select
    public $empresas = [];
    public $sucursales = [];
    public $school_periods = [];
    public $classrooms = [];
    public $subjects = [];
    public $teachers = [];

    protected $rules = [
        'code' => 'required|string|max:50|unique:sections,code',
        'name' => 'required|string|max:255',
        'school_period_id' => 'required|exists:school_periods,id',
        'subject_id' => 'required|exists:subjects,id',
        'teacher_id' => 'required|exists:teachers,id',
        'classroom_id' => 'required|exists:classrooms,id',
        'capacity' => 'required|integer|min:1|max:100',
        'empresa_id' => 'required|exists:empresas,id',
        'sucursal_id' => 'required|exists:sucursales,id',
        'description' => 'nullable|string|max:1000',
        'status' => 'required|in:active,inactive',
    ];

    protected $messages = [
        'code.required' => 'El código es requerido.',
        'code.unique' => 'Este código ya está en uso.',
        'name.required' => 'El nombre es requerido.',
        'school_period_id.required' => 'El período escolar es requerido.',
        'subject_id.required' => 'La materia es requerida.',
        'teacher_id.required' => 'El profesor es requerido.',
        'classroom_id.required' => 'El aula es requerida.',
        'capacity.required' => 'La capacidad es requerida.',
        'capacity.min' => 'La capacidad debe ser al menos 1.',
        'capacity.max' => 'La capacidad no puede exceder 100 estudiantes.',
        'empresa_id.required' => 'La empresa es requerida.',
        'sucursal_id.required' => 'La sucursal es requerida.',
    ];

    public function mount()
    {
        $this->loadFilters();
        $this->updateSucursales();
        $this->updateClassrooms();
    }

    public function loadFilters()
    {
        // Cargar empresas según el rol del usuario
        if (Auth::user()->hasRole('Super Administrador')) {
            $this->empresas = Empresa::all();
        } else {
            $this->empresas = Empresa::where('id', Auth::user()->empresa_id)->get();
            if (count($this->empresas) === 1) {
                $this->empresa_id = $this->empresas->first()->id;
            }
        }

        // Cargar períodos escolares
        $this->school_periods = SchoolPeriod::all();

        // Cargar materias
        $this->subjects = Subject::all();

        // Cargar profesores
        $this->teachers = Teacher::with('user')->get();
        dd($this->teachers);
    }

    public function updatedEmpresaId($value)
    {
        $this->sucursal_id = '';
        $this->classroom_id = '';
        $this->updateSucursales();
        $this->updateClassrooms();
    }

    public function updatedSucursalId($value)
    {
        $this->classroom_id = '';
        $this->updateClassrooms();
    }

    public function updateSucursales()
    {
        if ($this->empresa_id) {
            $this->sucursales = Sucursal::where('empresa_id', $this->empresa_id)->get();
        } else {
            $this->sucursales = [];
        }
    }

    public function updateClassrooms()
    {
        
        $this->classrooms = Classroom::where('empresa_id', auth()->user()->empresa_id)
            ->where('sucursal_id', auth()->user()->sucursal_id)
            ->get();
           
       
    }

    public function generateCode()
    {
        $subject = Subject::find($this->subject_id);
        $teacher = Teacher::find($this->teacher_id);
        $schoolPeriod = SchoolPeriod::find($this->school_period_id);

        if ($subject && $teacher && $schoolPeriod) {
            $baseCode = Str::upper(Str::substr($subject->name, 0, 3)) . '-' . 
                       Str::upper(Str::substr($teacher->name, 0, 3)) . '-' . 
                       $schoolPeriod->code;
            
            // Verificar si el código existe y generar uno único
            $counter = 1;
            $finalCode = $baseCode;
            while (Section::where('code', $finalCode)->exists()) {
                $finalCode = $baseCode . '-' . $counter;
                $counter++;
            }
            
            $this->code = $finalCode;
        }
    }

    public function updatedSubjectId()
    {
        $this->generateCode();
    }

    public function updatedTeacherId()
    {
        $this->generateCode();
    }

    public function updatedSchoolPeriodId()
    {
        $this->generateCode();
    }

    public function store()
    {
        $this->validate();

        try {
            Section::create([
                'code' => $this->code,
                'name' => $this->name,
                'school_period_id' => $this->school_period_id,
                'subject_id' => $this->subject_id,
                'teacher_id' => $this->teacher_id,
                'classroom_id' => $this->classroom_id,
                'capacity' => $this->capacity,
                'empresa_id' => $this->empresa_id,
                'sucursal_id' => $this->sucursal_id,
                'description' => $this->description,
                'status' => $this->status,
                'created_by' => Auth::id(),
            ]);

            session()->flash('success', 'Sección creada exitosamente.');
            return redirect()->route('admin.sections.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear la sección: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.sections.create', [
            'empresas' => $this->empresas,
            'sucursales' => $this->sucursales,
            'school_periods' => $this->school_periods,
            'classrooms' => $this->classrooms,
            'subjects' => $this->subjects,
            'teachers' => $this->teachers,
        ], [
            'title' => 'Crear Sección',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.sections.index' => 'Secciones',
                'admin.sections.create' => 'Crear'
            ]
        ]);
    }
}