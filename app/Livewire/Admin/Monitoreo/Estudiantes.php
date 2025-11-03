<?php

namespace App\Livewire\Admin\Monitoreo;

use Livewire\Component;
use App\Models\Student;
use App\Models\NivelEducativo;
use Livewire\Attributes\On;

class Estudiantes extends Component
{
    public $lastUpdate;
    
    public function mount()
    {
        abort_unless(auth()->user()->can('view monitoreo estudiantes'), 403);
        $this->lastUpdate = now()->format('H:i:s');
    }
    
    #[On('refresh-estudiantes')]
    public function refreshData()
    {
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function render()
    {
        $stats = [
            'total' => Student::count(),
            'activos' => Student::where('status', 1)->count(),
            'inactivos' => Student::where('status', 0)->count(),
        ];
        
        $byLevel = Student::join('niveles_educativos', 'students.nivel_educativo_id', '=', 'niveles_educativos.id')
            ->selectRaw('niveles_educativos.nombre as nivel, COUNT(students.id) as count')
            ->groupBy('niveles_educativos.nombre')
            ->get();
            
        $byGrade = Student::selectRaw('grado, COUNT(*) as count')
            ->where('status', 1)
            ->groupBy('grado')
            ->orderBy('grado')
            ->get();
            
        $bySection = Student::selectRaw('seccion, COUNT(*) as count')
            ->where('status', 1)
            ->groupBy('seccion')
            ->orderBy('seccion')
            ->get();
            
        $recent = Student::orderBy('created_at', 'desc')->take(10)->get();
        
        return view('livewire.admin.monitoreo.estudiantes', compact('stats', 'byLevel', 'byGrade', 'bySection', 'recent'))
            ->layout('components.layouts.admin', ['title' => 'Monitoreo de Estudiantes']);
    }
}
