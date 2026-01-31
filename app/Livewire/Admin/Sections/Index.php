<?php

namespace App\Livewire\Admin\Sections;

use App\Models\Section;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\SchoolPeriod;
use App\Models\Empresa;
use App\Models\Sucursal;
use Livewire\Component;
use Livewire\WithPagination;
use App\Traits\HasDynamicLayout;
use Livewire\Attributes\Layout;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $empresa_id = '';
    public $sucursal_id = '';
    public $school_period_id = '';
    public $classroom_id = '';
    public $subject_id = '';
    public $teacher_id = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'sucursal_id' => ['except' => ''],
        'school_period_id' => ['except' => ''],
        'classroom_id' => ['except' => ''],
        'subject_id' => ['except' => ''],
        'teacher_id' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEmpresaId()
    {
        $this->resetPage();
        $this->sucursal_id = '';
    }

    public function updatingSucursalId()
    {
        $this->resetPage();
    }

    public function updatingSchoolPeriodId()
    {
        $this->resetPage();
    }

    public function updatingClassroomId()
    {
        $this->resetPage();
    }

    public function updatingSubjectId()
    {
        $this->resetPage();
    }

    public function updatingTeacherId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
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

    public function deleteSection($id)
    {
        $section = Section::findOrFail($id);
        
        if ($section->schedules()->exists()) {
            session()->flash('error', 'No se puede eliminar la sección porque tiene horarios asociados.');
            return;
        }

        if ($section->enrollments()->exists()) {
            session()->flash('error', 'No se puede eliminar la sección porque tiene estudiantes inscritos.');
            return;
        }

        $section->delete();
        session()->flash('success', 'Sección eliminada exitosamente.');
    }

    public function toggleStatus($id)
    {
        $section = Section::findOrFail($id);
        $section->status = $section->status === 'active' ? 'inactive' : 'active';
        $section->save();
        session()->flash('success', 'Estado de la sección actualizado exitosamente.');
    }

    public function render()
    {
        $query = Section::with(['classroom', 'subject', 'teacher', 'schoolPeriod', 'empresa', 'sucursal'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', '%' . $this->search . '%')
                      ->orWhere('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('subject', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      })
                      ->orWhereHas('teacher', function ($q) {
                          $q->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })
            ->when($this->sucursal_id, function ($query) {
                $query->where('sucursal_id', $this->sucursal_id);
            })
            ->when($this->school_period_id, function ($query) {
                $query->where('school_period_id', $this->school_period_id);
            })
            ->when($this->classroom_id, function ($query) {
                $query->where('classroom_id', $this->classroom_id);
            })
            ->when($this->subject_id, function ($query) {
                $query->where('subject_id', $this->subject_id);
            })
            ->when($this->teacher_id, function ($query) {
                $query->where('teacher_id', $this->teacher_id);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortField, $this->sortDirection);

        return $this->renderWithLayout('livewire.admin.sections.index', [
            'sections' => $query->paginate(10),
            'empresas' => Empresa::all(),
            'sucursales' => Sucursal::when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })->get(),
            'school_periods' => SchoolPeriod::all(),
            'classrooms' => Classroom::when($this->sucursal_id, function ($query) {
                $query->where('sucursal_id', $this->sucursal_id);
            })->get(),
            'subjects' => Subject::all(),
            'teachers' => Teacher::all(),
        ], [
            'title' => 'Gestión de Secciones',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.sections.index' => 'Secciones'
            ]
        ]);
    }
}