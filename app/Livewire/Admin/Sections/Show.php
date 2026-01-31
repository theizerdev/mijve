<?php

namespace App\Livewire\Admin\Sections;

use App\Models\Section;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Show extends Component
{
    public $section;

    public function mount($id)
    {
        $this->section = Section::with([
            'schoolPeriod', 
            'subject', 
            'teacher', 
            'classroom', 
            'empresa', 
            'sucursal', 
            'createdBy',
            'schedules',
            'enrollments.student'
        ])->findOrFail($id);

        // Verificar permisos
        if (!Auth::user()->hasRole('Super Administrador') && 
            $this->section->empresa_id != Auth::user()->empresa_id) {
            abort(403, 'No tienes permisos para ver esta sección.');
        }
    }

    public function render()
    {
        return view('livewire.admin.sections.show', [
            'section' => $this->section,
        ]);
    }
}