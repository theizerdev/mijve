<?php

namespace App\Livewire\Admin\Students;

use Livewire\Component;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public $student;

    public function mount(Student $student)
    {
        // Verificar permiso para ver estudiantes
        if (!Auth::user()->can('view students')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $this->student = $student;
    }

    public function render()
    {
        return view('livewire.admin.students.show')
            ->layout('components.layouts.admin');
    }
}