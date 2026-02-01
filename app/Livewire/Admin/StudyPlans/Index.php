<?php

namespace App\Livewire\Admin\StudyPlans;

use App\Traits\HasDynamicLayout;
use App\Traits\Exportable;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StudyPlan;
use App\Models\Programa;
use App\Models\NivelEducativo;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $program_id = '';
    public $educational_level_id = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'program_id' => ['except' => ''],
        'educational_level_id' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedProgramId()
    {
        $this->resetPage();
    }

    public function updatedEducationalLevelId()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
        $this->resetPage();
    }

    public function delete(StudyPlan $studyPlan)
    {
        if (!auth()->user()->can('delete study_plans')) {
            session()->flash('error', 'No tienes permiso para eliminar planes de estudio.');
            return;
        }

        try {
            // Verificar si hay materias asignadas
            if ($studyPlan->subjects()->count() > 0) {
                session()->flash('error', 'No se puede eliminar el plan de estudio porque tiene materias asignadas.');
                return;
            }

            $studyPlan->delete();
            session()->flash('message', 'Plan de estudio eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el plan de estudio: ' . $e->getMessage());
        }

        $this->resetPage();
    }

    public function toggleStatus(StudyPlan $studyPlan)
    {
        if (!auth()->user()->can('edit study_plans')) {
            session()->flash('error', 'No tienes permiso para editar planes de estudio.');
            return;
        }

        $studyPlan->status = $studyPlan->status === 'active' ? 'inactive' : 'active';
        $studyPlan->save();
        
        session()->flash('message', 'Estado del plan de estudio actualizado correctamente.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->program_id = '';
        $this->educational_level_id = '';
        $this->status = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function getExportQuery()
    {
        return $this->getQuery();
    }

    public function getExportHeaders()
    {
        return [
            'Código', 'Nombre', 'Descripción', 'Programa', 'Nivel Educativo', 
            'Créditos Totales', 'Horas Totales', 'Años', 'Semestres', 'Estado'
        ];
    }

    public function formatExportRow($studyPlan)
    {
        return [
            $studyPlan->code,
            $studyPlan->name,
            $studyPlan->description ?? '',
            $studyPlan->program->nombre ?? '',
            $studyPlan->educationalLevel->nombre ?? '',
            $studyPlan->total_credits ?? 0,
            $studyPlan->total_hours ?? 0,
            $studyPlan->duration_years ?? 0,
            $studyPlan->duration_semesters ?? 0,
            ucfirst($studyPlan->status)
        ];
    }

    private function getBaseQuery()
    {
        return StudyPlan::with(['program', 'educationalLevel', 'createdBy', 'updatedBy']);
    }

    public function getStatsProperty()
    {
        $baseQuery = $this->getBaseQuery();
        
        return [
            'total' => $baseQuery->count(),
            'activos' => (clone $baseQuery)->where('status', 'active')->count(),
            'inactivos' => (clone $baseQuery)->where('status', 'inactive')->count(),
            'por_defecto' => (clone $baseQuery)->where('is_default', true)->count()
        ];
    }

    private function getQuery()
    {
        return $this->getBaseQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('code', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('program', function ($subQuery) {
                            $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                        })
                        ->orWhereHas('educationalLevel', function ($subQuery) {
                            $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->program_id !== '', function ($query) {
                $query->where('program_id', $this->program_id);
            })
            ->when($this->educational_level_id !== '', function ($query) {
                $query->where('educational_level_id', $this->educational_level_id);
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function render()
    {
        $studyPlans = $this->getQuery()->paginate($this->perPage);
        $programs = Programa::orderBy('nombre')->get();
        $educationalLevels = NivelEducativo::orderBy('nombre')->get();

        return view('livewire.admin.study-plans.index', compact('studyPlans', 'programs', 'educationalLevels'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Gestión de Planes de Estudio';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.study-plans.index' => 'Planes de Estudio'
        ];
    }
}