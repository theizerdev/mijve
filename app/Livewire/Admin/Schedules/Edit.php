<?php

namespace App\Livewire\Admin\Schedules;

use App\Models\Schedule;
use App\Models\Section;
use App\Models\Classroom;
use App\Models\Company;
use App\Models\Branch;
use App\Models\SchoolPeriod;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\HasDynamicLayout;

class Edit extends Component
{
    use HasDynamicLayout;
    
    public $schedule;
    
    public $section_id;
    public $classroom_id;
    public $day;
    public $start_time;
    public $end_time;
    public $status;
    public $notes;

    public $companies = [];
    public $branches = [];
    public $schoolPeriods = [];
    public $sections = [];
    public $classrooms = [];

    public $company_id;
    public $branch_id;
    public $school_period_id;

    protected $rules = [
        'section_id' => 'required|exists:sections,id',
        'classroom_id' => 'required|exists:classrooms,id',
        'day' => 'required|integer|between:1,7',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'status' => 'boolean',
        'notes' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'section_id.required' => 'La sección es requerida.',
        'section_id.exists' => 'La sección seleccionada no es válida.',
        'classroom_id.required' => 'El aula es requerida.',
        'classroom_id.exists' => 'El aula seleccionada no es válida.',
        'day.required' => 'El día es requerido.',
        'day.integer' => 'El día debe ser un número válido.',
        'day.between' => 'El día debe estar entre 1 y 7.',
        'start_time.required' => 'La hora de inicio es requerida.',
        'start_time.date_format' => 'La hora de inicio debe estar en formato HH:MM.',
        'end_time.required' => 'La hora de fin es requerida.',
        'end_time.date_format' => 'La hora de fin debe estar en formato HH:MM.',
        'end_time.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        'status.boolean' => 'El estado debe ser verdadero o falso.',
        'notes.max' => 'Las notas no pueden exceder 500 caracteres.',
    ];

    public function mount($schedule)
    {
        // Buscar el horario por ID si es un string/número
        if (is_numeric($schedule)) {
            $this->schedule = Schedule::findOrFail($schedule);
        } else {
            $this->schedule = $schedule;
        }
        
       
        
        // Cargar datos actuales
        $this->section_id = $this->schedule->section_id;
        $this->classroom_id = $this->schedule->classroom_id;
        $this->day = $this->schedule->day;
        $this->start_time = $this->schedule->start_time;
        $this->end_time = $this->schedule->end_time;
        $this->status = $this->schedule->status;
        $this->notes = $this->schedule->notes;

        // Cargar relaciones
        $this->company_id = $this->schedule->section->empresa_id;
        $this->branch_id = $this->schedule->section->sucursal_id;
        $this->school_period_id = $this->schedule->section->periodo_escolar_id;

        $this->loadCompanies();
        $this->loadBranches();
        $this->loadSchoolPeriods();
        $this->loadSections();
        $this->loadClassrooms();
    }

    public function loadCompanies()
    {
        $this->companies = Company::whereHas('users', function ($query) {
            $query->where('users.id', Auth::id());
        })->pluck('name', 'id')->toArray();
    }

    public function loadBranches()
    {
        if (!$this->company_id) {
            $this->branches = [];
            $this->sections = [];
            $this->classrooms = [];
            return;
        }

        $this->branches = Branch::where('empresa_id', $this->company_id)
            ->whereHas('users', function ($query) {
                $query->where('users.id', Auth::id());
            })
            ->pluck('nombre', 'id')->toArray();
    }

    public function loadSchoolPeriods()
    {
        $this->schoolPeriods = SchoolPeriod::where('empresa_id', Auth::user()->empresa_id)
            ->orderBy('fecha_inicio', 'desc')
            ->pluck('nombre', 'id')->toArray();
    }

    public function loadSections()
    {
        $query = Section::query();

        if ($this->company_id) {
            $query->where('empresa_id', $this->company_id);
        }

        if ($this->branch_id) {
            $query->where('sucursal_id', $this->branch_id);
        }

        if ($this->school_period_id) {
            $query->where('periodo_escolar_id', $this->school_period_id);
        }

        // Verificar que el usuario tenga acceso a las secciones de la empresa
        if (Auth::user()->empresa_id) {
            $query->where('empresa_id', Auth::user()->empresa_id);
        }

        $this->sections = $query->pluck('nombre', 'id')->toArray();
    }

    public function loadClassrooms()
    {
        $query = Classroom::query();

        if ($this->company_id) {
            $query->where('empresa_id', $this->company_id);
        }

        if ($this->branch_id) {
            $query->where('sucursal_id', $this->branch_id);
        }

        $this->classrooms = $query->pluck('nombre', 'id')->toArray();
    }

    public function updatedCompanyId()
    {
        $this->loadBranches();
        $this->section_id = '';
        $this->classroom_id = '';
    }

    public function updatedBranchId()
    {
        $this->loadSections();
        $this->loadClassrooms();
        $this->section_id = '';
        $this->classroom_id = '';
    }

    public function updatedSchoolPeriodId()
    {
        $this->loadSections();
        $this->section_id = '';
    }

    public function getDaysProperty()
    {
        return [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }

    public function checkScheduleConflicts()
    {
        if (!$this->classroom_id || !$this->day || !$this->start_time || !$this->end_time) {
            return;
        }

        $conflicts = Schedule::where('aula_id', $this->classroom_id)
            ->where('dia', $this->day)
            ->where('id', '!=', $this->schedule->id) // Excluir el horario actual
            ->where(function ($query) {
                $query->whereBetween('hora_inicio', [$this->start_time, $this->end_time])
                    ->orWhereBetween('hora_fin', [$this->start_time, $this->end_time])
                    ->orWhere(function ($q) {
                        $q->where('hora_inicio', '<=', $this->start_time)
                            ->where('hora_fin', '>=', $this->end_time);
                    });
            })
            ->with(['section' => function ($query) {
                $query->select('id', 'nombre', 'codigo');
            }])
            ->get();

        if ($conflicts->isNotEmpty()) {
            $this->dispatch('schedule-conflicts', [
                'conflicts' => $conflicts->map(function ($conflict) {
                    return [
                        'section_name' => $conflict->section->nombre,
                        'section_code' => $conflict->section->codigo,
                        'start_time' => $conflict->start_time,
                        'end_time' => $conflict->end_time,
                    ];
                })->toArray()
            ]);
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        if (in_array($propertyName, ['classroom_id', 'day', 'start_time', 'end_time'])) {
            $this->checkScheduleConflicts();
        }
    }

    public function update()
    {
        $this->validate();

        // Verificar permisos por empresa
        $section = Section::findOrFail($this->section_id);
        if (!Gate::allows('manage-company', $section->empresa_id)) {
            session()->flash('error', 'No tienes permisos para modificar horarios en esta empresa.');
            return;
        }

        // Verificar si el aula pertenece a la misma empresa que la sección
        $classroom = Classroom::findOrFail($this->classroom_id);
        if ($classroom->empresa_id !== $section->empresa_id) {
            session()->flash('error', 'El aula debe pertenecer a la misma empresa que la sección.');
            return;
        }

        // Verificar conflictos de horario (excluyendo el horario actual)
        $conflicts = Schedule::where('aula_id', $this->classroom_id)
            ->where('dia', $this->day)
            ->where('id', '!=', $this->schedule->id)
            ->where(function ($query) {
                $query->whereBetween('hora_inicio', [$this->start_time, $this->end_time])
                    ->orWhereBetween('hora_fin', [$this->start_time, $this->end_time])
                    ->orWhere(function ($q) {
                        $q->where('hora_inicio', '<=', $this->start_time)
                            ->where('hora_fin', '>=', $this->end_time);
                    });
            })
            ->exists();

        if ($conflicts) {
            session()->flash('error', 'Existe un conflicto de horario con otro registro en el mismo aula.');
            return;
        }

        try {
            $this->schedule->update([
                'seccion_id' => $this->section_id,
                'aula_id' => $this->classroom_id,
                'dia' => $this->day,
                'hora_inicio' => $this->start_time,
                'hora_fin' => $this->end_time,
                'estado' => $this->status,
                'notas' => $this->notes,
            ]);

            session()->flash('message', 'Horario actualizado exitosamente.');
            
            return redirect()->route('admin.schedules.index');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el horario: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.schedules.edit', [
            'companies' => $this->companies,
            'branches' => $this->branches,
            'schoolPeriods' => $this->schoolPeriods,
            'sections' => $this->sections,
            'classrooms' => $this->classrooms,
            'days' => $this->days,
        ], [
            'title' => 'Editar Horario',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.schedules.index' => 'Horarios',
                'admin.schedules.edit' => 'Editar'
            ]
        ]);
    }
}