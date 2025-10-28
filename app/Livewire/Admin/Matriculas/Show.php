<?php

namespace App\Livewire\Admin\Matriculas;

use Livewire\Component;
use App\Models\Matricula;

class Show extends Component
{
    public $matricula;

    public function mount(Matricula $matricula)
    {
        $this->matricula = $matricula->load(['student', 'programa', 'periodo']);
    }

    public function render()
    {
        return view('livewire.admin.matriculas.show')
            ->layout('components.layouts.admin', [
                'title' => 'Ver Matrícula',
                'description' => 'Detalles de la matrícula del estudiante'
            ]);
    }
}