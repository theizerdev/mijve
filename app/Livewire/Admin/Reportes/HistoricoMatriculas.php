<?php

namespace App\Livewire\Admin\Reportes;

use Livewire\Component;
use App\Models\Matricula;
use App\Models\SchoolPeriod;
use App\Models\EducationalLevel;
use App\Models\Programa;
use Illuminate\Support\Facades\DB;

class HistoricoMatriculas extends Component
{
    public $matriculas = [];
    public $periodos;
    public $nivelesEducativos;
    public $programas;
    
    public $periodo_id;
    public $nivel_educativo_id;
    public $programa_id;
    public $fecha_inicio;
    public $fecha_fin;
    
    public $estadisticas = [];

    public function mount()
    {
        $this->periodos = SchoolPeriod::all();
        $this->nivelesEducativos = EducationalLevel::all();
        $this->programas = collect();
        $this->fecha_inicio = now()->startOfMonth()->format('Y-m-d');
        $this->fecha_fin = now()->endOfMonth()->format('Y-m-d');
        $this->cargarReporte();
    }

    public function updatedNivelEducativoId()
    {
        if ($this->nivel_educativo_id) {
            $this->programas = Programa::where('nivel_educativo_id', $this->nivel_educativo_id)->get();
        } else {
            $this->programas = collect();
        }
        
        $this->programa_id = '';
    }

    public function updatedPeriodoId()
    {
        if ($this->periodo_id) {
            $periodo = SchoolPeriod::find($this->periodo_id);
            if ($periodo) {
                $this->fecha_inicio = $periodo->fecha_inicio ? $periodo->fecha_inicio->format('Y-m-d') : now()->startOfMonth()->format('Y-m-d');
                $this->fecha_fin = $periodo->fecha_fin ? $periodo->fecha_fin->format('Y-m-d') : now()->endOfMonth()->format('Y-m-d');
                $this->cargarReporte();
            }
        }
    }

    public function cargarReporte()
    {
        $query = Matricula::with(['student', 'programa.nivelEducativo', 'periodo'])
            ->whereBetween('fecha_matricula', [$this->fecha_inicio, $this->fecha_fin]);

        if ($this->programa_id) {
            $query->where('programa_id', $this->programa_id);
        } elseif ($this->nivel_educativo_id) {
            $query->join('programas', 'matriculas.programa_id', '=', 'programas.id')
                  ->where('programas.nivel_educativo_id', $this->nivel_educativo_id);
        }

        $this->matriculas = $query->get();

        // Calcular estadísticas
        $this->calcularEstadisticas();
    }

    private function calcularEstadisticas()
    {
        $total = $this->matriculas->count();
        
        // Agrupar por estado
        $porEstado = $this->matriculas->groupBy('estado')->map->count();
        
        // Agrupar por nivel educativo
        $porNivel = $this->matriculas->groupBy(function($matricula) {
            return $matricula->programa->nivelEducativo->nombre ?? 'Sin nivel';
        })->map->count();
        
        // Agrupar por programa
        $porPrograma = $this->matriculas->groupBy(function($matricula) {
            return $matricula->programa->nombre ?? 'Sin programa';
        })->map->count();
        
        // Agrupar por período
        $porPeriodo = $this->matriculas->groupBy(function($matricula) {
            return $matricula->periodo->nombre ?? 'Sin período';
        })->map->count();

        $this->estadisticas = [
            'total' => $total,
            'por_estado' => $porEstado,
            'por_nivel' => $porNivel,
            'por_programa' => $porPrograma,
            'por_periodo' => $porPeriodo
        ];
    }

    public function exportarExcel()
    {
        // Lógica para exportar a Excel
        session()->flash('message', 'Funcionalidad de exportación en desarrollo.');
    }

    public function exportarPDF()
    {
        // Lógica para exportar a PDF
        session()->flash('message', 'Funcionalidad de exportación en desarrollo.');
    }

    public function render()
    {
        return view('livewire.admin.reportes.historico-matriculas')
            ->layout('components.layouts.admin', [
                'title' => 'Histórico de Matrículas',
                'description' => 'Historial de matrículas por estudiante'
            ]);
    }
}