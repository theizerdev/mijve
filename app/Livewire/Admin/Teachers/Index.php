<?php

namespace App\Livewire\Admin\Teachers;

use App\Models\Teacher;
use App\Models\Subject;
use App\Traits\Exportable;
use App\Traits\HasDynamicLayout;
use App\Traits\HasRegionalFormatting;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout, HasRegionalFormatting;

    public $search = '';
    public $specialization = '';
    public $is_active = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'specialization' => ['except' => ''],
        'is_active' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSpecialization()
    {
        $this->resetPage();
    }

    public function updatedIsActive()
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

    public function delete(Teacher $teacher)
    {
        if (!auth()->user()->can('delete teachers')) {
            session()->flash('error', 'No tienes permiso para eliminar profesores.');
            return;
        }

        try {
            // Verificar si tiene asignaciones activas
            if ($teacher->subjects()->wherePivot('academic_period', $teacher->getCurrentAcademicPeriod())->exists()) {
                session()->flash('error', 'No se puede eliminar el profesor porque tiene materias asignadas en el período actual.');
                return;
            }

            $teacher->delete();
            session()->flash('message', 'Profesor eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el profesor: ' . $e->getMessage());
        }

        $this->resetPage();
    }

    public function toggleStatus(Teacher $teacher)
    {
        if (!auth()->user()->can('edit teachers')) {
            session()->flash('error', 'No tienes permiso para editar profesores.');
            return;
        }

        $teacher->is_active = !$teacher->is_active;
        $teacher->save();
        
        session()->flash('message', 'Estado del profesor actualizado correctamente.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->specialization = '';
        $this->is_active = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function getStatsProperty()
    {
        $baseQuery = $this->getBaseQuery();

        return [
            'total' => (clone $baseQuery)->count(),
            'activos' => (clone $baseQuery)->where('is_active', true)->count(),
            'inactivos' => (clone $baseQuery)->where('is_active', false)->count(),
        ];
    }

    public function getExportQuery()
    {
        return $this->getQuery();
    }

    public function getExportHeaders()
    {
        return [
            'Código', 'Nombre', 'Email', 'Especialización', 'Título', 'Años Exp.', 'Estado', 'Fecha Contratación'
        ];
    }

    public function formatExportRow($teacher)
    {
        return [
            $teacher->employee_code,
            $teacher->full_name,
            $teacher->email,
            $teacher->specialization,
            $teacher->degree,
            $teacher->years_experience,
            $teacher->is_active ? 'Activo' : 'Inactivo',
            $teacher->hire_date?->format('d/m/Y') ?? ''
        ];
    }

    private function getBaseQuery()
    {
        return Teacher::with(['user', 'createdBy', 'updatedBy']);
    }

    private function getQuery()
    {
        return $this->getBaseQuery()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('employee_code', 'like', '%' . $this->search . '%')
                        ->orWhere('specialization', 'like', '%' . $this->search . '%')
                        ->orWhere('name', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->specialization !== '', function ($query) {
                $query->where('specialization', 'like', '%' . $this->specialization . '%');
            })
            ->when($this->is_active !== '', function ($query) {
                $query->where('is_active', $this->is_active === '1');
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    public function mount()
    {
        if (!auth()->check()) {
            abort(403, 'Debes estar autenticado para acceder a esta página.');
        }
        
        if (!auth()->user()->can('access teachers')) {
            abort(403, 'No tienes permiso para acceder a profesores.');
        }
    }

    public function render()
    {
        $teachers = $this->getQuery()->paginate($this->perPage);
        
        return view('livewire.admin.teachers.index', compact('teachers'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Gestión de Profesores';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.teachers.index' => 'Profesores'
        ];
    }
}