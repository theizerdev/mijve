<?php

namespace App\Livewire\Admin\Subjects;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Subject;

class Show extends Component
{
    use HasDynamicLayout;

    public Subject $subject;

    public function mount(Subject $subject)
    {
        $this->subject = $subject->load(['programa', 'educationalLevel', 'teachers.user', 'createdBy', 'updatedBy', 'prerequisites.prerequisiteSubject']);
    }

    public function render()
    {
        return view('livewire.admin.subjects.show')
            ->layout($this->getLayout());
    }

    protected function getPageTitle(): string
    {
        return 'Detalles de Materia: ' . $this->subject->name;
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.subjects.index' => 'Materias',
            '#' => 'Detalles'
        ];
    }
}