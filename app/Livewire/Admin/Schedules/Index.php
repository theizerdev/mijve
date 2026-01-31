<?php

namespace App\Livewire\Admin\Schedules;

use App\Models\Schedule;
use App\Models\Section;
use App\Models\Classroom;
use App\Models\Company;
use App\Models\Branch;
use App\Models\SchoolPeriod;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Traits\HasDynamicLayout;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $company_id = '';
    public $branch_id = '';
    public $school_period_id = '';
    public $section_id = '';
    public $classroom_id = '';
    public $day = '';
    public $status = '';
    public $sortField = 'start_time';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'company_id' => ['except' => ''],
        'branch_id' => ['except' => ''],
        'school_period_id' => ['except' => ''],
        'section_id' => ['except' => ''],
        'classroom_id' => ['except' => ''],
        'day' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'start_time'],
        'sortDirection' => ['except' => 'asc'],
    ];

    protected $listeners = [
        'deleteSchedule',
        'confirmDelete'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCompanyId()
    {
        $this->resetPage();
        $this->branch_id = '';
        $this->section_id = '';
        $this->classroom_id = '';
    }

    public function updatingBranchId()
    {
        $this->resetPage();
        $this->section_id = '';
        $this->classroom_id = '';
    }

    public function updatingSchoolPeriodId()
    {
        $this->resetPage();
        $this->section_id = '';
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function deleteSchedule($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        
       

        // Verificar si hay conflictos de horario con otras secciones
        $conflictingSchedules = Schedule::where('classroom_id', $schedule->classroom_id)
            ->where('day', $schedule->day)
            ->where('id', '!=', $schedule->id)
            ->where(function ($query) use ($schedule) {
                $query->whereBetween('start_time', [$schedule->start_time, $schedule->end_time])
                    ->orWhereBetween('end_time', [$schedule->start_time, $schedule->end_time])
                    ->orWhere(function ($q) use ($schedule) {
                        $q->where('start_time', '<=', $schedule->start_time)
                            ->where('end_time', '>=', $schedule->end_time);
                    });
            })
            ->exists();

        if ($conflictingSchedules) {
            session()->flash('warning', 'Este horario tiene conflictos con otros. Se eliminará el horario actual.');
        }

        $schedule->delete();
        
        session()->flash('message', 'Horario eliminado exitosamente.');
    }

    public function confirmDelete($scheduleId)
    {
        $this->dispatch('confirm-delete', [
            'title' => '¿Estás seguro?',
            'text' => '¿Deseas eliminar este horario?',
            'method' => 'deleteSchedule',
            'params' => [$scheduleId]
        ]);
    }

    public function toggleStatus($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        
        // Verificar permisos por empresa
        if (!Gate::allows('manage-company', $schedule->section->empresa_id)) {
            session()->flash('error', 'No tienes permisos para modificar este horario.');
            return;
        }

        $schedule->update(['status' => !$schedule->status]);
        
        session()->flash('message', 'Estado del horario actualizado exitosamente.');
    }

    public function getCompaniesProperty()
    {
        // Si el usuario tiene empresa asignada, solo mostrar esa empresa
        if (Auth::user()->empresa_id) {
            return \App\Models\Empresa::where('id', Auth::user()->empresa_id)->pluck('razon_social', 'id')->toArray();
        }
        
        // Si no tiene empresa asignada, no mostrar ninguna (seguridad)
        return [];
    }

    public function getBranchesProperty()
    {
        if (!$this->company_id) {
            return [];
        }

        // Verificar que el usuario tenga acceso a esta empresa
        if (Auth::user()->empresa_id && Auth::user()->empresa_id != $this->company_id) {
            return [];
        }

        return Branch::where('empresa_id', $this->company_id)
            ->pluck('nombre', 'id')->toArray();
    }

    public function getSchoolPeriodsProperty()
    {
        // Si el usuario tiene empresa asignada, mostrar solo periodos de esa empresa
        if (Auth::user()->empresa_id) {
       
            return SchoolPeriod::orderBy('start_date', 'desc')->pluck('name', 'id')->toArray();
        }
        
        // Si no tiene empresa asignada, no mostrar ninguno (seguridad)
        return [];
    }

    public function getSectionsProperty()
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

        return $query->pluck('nombre', 'id')->toArray();
    }

    public function getClassroomsProperty()
    {
        $query = Classroom::query();

        if ($this->company_id) {
            $query->where('empresa_id', $this->company_id);
        }

        if ($this->branch_id) {
            $query->where('sucursal_id', $this->branch_id);
        }

        // Verificar que el usuario tenga acceso a las aulas de la empresa
        if (Auth::user()->empresa_id) {
            $query->where('empresa_id', Auth::user()->empresa_id);
        }

        return $query->pluck('nombre', 'id')->toArray();
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

    public function render()
    {
        $query = Schedule::with([
            'section' => function ($query) {
                $query->select('id', 'codigo', 'nombre', 'empresa_id', 'sucursal_id', 'periodo_escolar_id', 'programa_id', 'profesor_guia_id')
                    ->with(['programa:id,nombre', 'profesorGuia:id,nombre']);
            },
            'aula' => function ($query) {
                $query->select('id', 'nombre', 'capacidad', 'empresa_id', 'sucursal_id');
            }
        ]);

        // Aplicar filtros
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('section', function ($query) {
                    $query->where('codigo', 'like', '%' . $this->search . '%')
                        ->orWhere('nombre', 'like', '%' . $this->search . '%')
                        ->orWhereHas('programa', function ($q) {
                            $q->where('nombre', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('profesorGuia', function ($q) {
                            $q->where('nombre', 'like', '%' . $this->search . '%');
                        });
                })
                ->orWhereHas('aula', function ($query) {
                    $query->where('nombre', 'like', '%' . $this->search . '%');
                });
            });
        }

        if ($this->company_id) {
            $query->whereHas('section', function ($q) {
                $q->where('empresa_id', $this->company_id);
            });
        }

        if ($this->branch_id) {
            $query->whereHas('section', function ($q) {
                $q->where('sucursal_id', $this->branch_id);
            });
        }

        if ($this->school_period_id) {
            $query->whereHas('section', function ($q) {
                $q->where('periodo_escolar_id', $this->school_period_id);
            });
        }

        if ($this->section_id) {
            $query->where('section_id', $this->section_id);
        }

        if ($this->classroom_id) {
            $query->where('aula_id', $this->classroom_id);
        }

        if ($this->day) {
            $query->where('day', $this->day);
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        // Filtrar por permisos de empresa
        $query->whereHas('section.empresa.users', function ($q) {
            $q->where('users.id', Auth::id());
        });

        $schedules = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return $this->renderWithLayout('livewire.admin.schedules.index', [
            'schedules' => $schedules,
            'companies' => $this->companies,
            'branches' => $this->branches,
            'schoolPeriods' => $this->schoolPeriods,
            'sections' => $this->sections,
            'classrooms' => $this->classrooms,
            'days' => $this->days,
        ], [
            'title' => 'Gestión de Horarios',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.schedules.index' => 'Horarios'
            ]
        ]);
    }
}