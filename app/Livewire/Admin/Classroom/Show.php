<?php

namespace App\Livewire\Admin\Classroom;

use App\Models\Classroom;
use Livewire\Component;
use App\Traits\HasDynamicLayout;

class Show extends Component
{
    use HasDynamicLayout;
    public $classroom;

    public function mount(Classroom $classroom)
    {
        $this->classroom = $classroom->load(['empresa', 'sucursal', 'sections', 'schedules']);
    }

    public function render()
    {
        $schedules = $this->classroom->schedules()
            ->with(['section', 'subject', 'teacher'])
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('dia_semana');

        return $this->renderWithLayout('livewire.admin.classroom.show', [
            'schedules' => $schedules,
        ], [
            'title' => 'Detalles del Aula',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.classrooms.index' => 'Aulas',
                'admin.classrooms.show' => 'Detalles'
            ]
        ]);
    }
}