<?php

namespace App\Livewire\Admin\StudyPlans;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\StudyPlan;
use App\Models\Subject;
use Livewire\WithPagination;

class Show extends Component
{
    use HasDynamicLayout, WithPagination;

    public StudyPlan $studyPlan;
    public $showInactive = false;
    
    public $availableSubjects = [];
    public $selectedSubject = '';
    public $semester = 1;
    public $year = 1;
    public $subject_type = 'mandatory';
    public $order = 1;

    protected $rules = [
        'selectedSubject' => 'required|exists:subjects,id',
        'semester' => 'required|integer|min:1',
        'year' => 'required|integer|min:1',
        'subject_type' => 'required|in:mandatory,elective',
        'order' => 'required|integer|min:1',
    ];

    public function mount(StudyPlan $studyPlan)
    {
        $this->studyPlan = $studyPlan->load(['program', 'educationalLevel', 'subjects', 'createdBy', 'updatedBy']);
        $this->loadAvailableSubjects();
    }

    public function loadAvailableSubjects()
    {
        // Obtener materias que no están ya asignadas a este plan
        $assignedSubjectIds = $this->studyPlan->subjects()->pluck('subjects.id');
        
        $this->availableSubjects = Subject::where('program_id', $this->studyPlan->program_id)
            ->where('educational_level_id', $this->studyPlan->educational_level_id)
            ->whereNotIn('id', $assignedSubjectIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function addSubject()
    {
        $this->validate();

        if (!auth()->user()->can('edit study_plans')) {
            session()->flash('error', 'No tienes permiso para editar planes de estudio.');
            return;
        }

        try {
            // Verificar si la materia ya está asignada
            $exists = $this->studyPlan->subjects()
                ->where('subject_id', $this->selectedSubject)
                ->exists();

            if ($exists) {
                session()->flash('error', 'La materia ya está asignada a este plan de estudio.');
                return;
            }

            $this->studyPlan->subjects()->attach($this->selectedSubject, [
                'semester' => $this->semester,
                'year' => $this->year,
                'subject_type' => $this->subject_type,
                'order' => $this->order,
                'is_active' => true,
            ]);

            session()->flash('message', 'Materia agregada al plan de estudio correctamente.');
            
            // Resetear formulario y recargar datos
            $this->reset(['selectedSubject', 'semester', 'year', 'subject_type', 'order']);
            $this->semester = 1;
            $this->year = 1;
            $this->subject_type = 'mandatory';
            $this->order = 1;
            $this->studyPlan->refresh();
            $this->loadAvailableSubjects();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al agregar la materia: ' . $e->getMessage());
        }
    }

    public function removeSubject($subjectId)
    {
        if (!auth()->user()->can('edit study_plans')) {
            session()->flash('error', 'No tienes permiso para editar planes de estudio.');
            return;
        }

        try {
            $this->studyPlan->subjects()->detach($subjectId);
            session()->flash('message', 'Materia removida del plan de estudio correctamente.');
            
            $this->studyPlan->refresh();
            $this->loadAvailableSubjects();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al remover la materia: ' . $e->getMessage());
        }
    }

    public function toggleSubjectStatus($subjectId)
    {
        if (!auth()->user()->can('edit study_plans')) {
            session()->flash('error', 'No tienes permiso para editar planes de estudio.');
            return;
        }

        try {
            $pivot = $this->studyPlan->subjects()->where('subject_id', $subjectId)->first()->pivot;
            $pivot->is_active = !$pivot->is_active;
            $pivot->save();
            
            session()->flash('message', 'Estado de la materia actualizado correctamente.');
            $this->studyPlan->refresh();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $query = $this->studyPlan->subjects()
            ->withPivot('semester', 'year', 'subject_type', 'order', 'is_active');

        if (!$this->showInactive) {
            $query->wherePivot('is_active', true);
        }

        $subjects = $query->orderBy('pivot_year')
            ->orderBy('pivot_semester')
            ->orderBy('pivot_order')
            ->paginate(10);

        // Agrupar materias por año y semestre para mejor visualización
        $groupedSubjects = $subjects->getCollection()->groupBy([
            function ($item) {
                return $item->pivot->year;
            },
            function ($item) {
                return $item->pivot->semester;
            }
        ]);

        return view('livewire.admin.study-plans.show', [
            'subjects' => $subjects,
            'groupedSubjects' => $groupedSubjects,
        ])->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Plan de Estudio: ' . $this->studyPlan->name;
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.study-plans.index' => 'Planes de Estudio',
            '#' => 'Detalles'
        ];
    }
}