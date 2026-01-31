<?php

namespace App\Livewire\Admin\Teachers;

use App\Models\Teacher;
use App\Traits\HasDynamicLayout;
use Livewire\Component;

class Show extends Component
{
    use HasDynamicLayout;

    public Teacher $teacher;

    public function mount(Teacher $teacher)
    {
        if (!auth()->check()) {
            abort(403, 'Debes estar autenticado para acceder a esta página.');
        }
        
        if (!auth()->user()->can('view teachers')) {
            abort(403, 'No tienes permiso para ver profesores.');
        }

        $this->teacher = $teacher->load(['subjects.programa', 'subjects.educationalLevel', 'createdBy', 'updatedBy']);
    }

    public function render()
    {
        $currentAssignments = $this->teacher->subjects()
            ->wherePivot('is_primary', true)
            ->with(['programa', 'educationalLevel'])
            ->get();

        $allAssignments = $this->teacher->subjects()
            ->with(['programa', 'educationalLevel'])
            ->orderBy('name')
            ->get();

        return view('livewire.admin.teachers.show', compact('currentAssignments', 'allAssignments'))
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Detalles del Profesor: ' . $this->teacher->name;
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.teachers.index' => 'Profesores',
            '' => $this->teacher->name
        ];
    }
}