<?php

namespace App\Livewire\Admin\Matriculas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Matricula;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination, Exportable;

    public $search = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
        $this->resetPage();
    }

    public function toggleStatus($matriculaId)
    {
        if (!auth()->user()->can('edit matriculas')) {
            session()->flash('error', 'No tienes permiso para cambiar el estado.');
            return;
        }

        $matricula = Matricula::find($matriculaId);
        if ($matricula) {
            $matricula->estado = $matricula->estado === 'activo' ? 'inactivo' : 'activo';
            $matricula->save();
        }
    }

    public function delete(Matricula $matricula)
    {
        // Verificar permiso para eliminar matrículas
        if (!auth()->user()->can('delete matriculas')) {
            session()->flash('error', 'No tienes permiso para eliminar matrículas.');
            return;
        }

        try {
            $matricula->delete();
            session()->flash('message', 'Matrícula eliminada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la matrícula: ' . $e->getMessage());
        }

        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    protected function getExportQuery()
    {
        return Matricula::with(['student', 'programa', 'periodo'])
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($subQuery) {
                    $subQuery->where('nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                        ->orWhere('documento_identidad', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('matriculas.estado', $this->status);
            })
            ->orderBy('matriculas.' . $this->sortBy, $this->sortDirection);
    }

    protected function getExportHeaders(): array
    {
        return [
            'ID',
            'Estudiante',
            'Documento',
            'Programa',
            'Período',
            'Fecha Matrícula',
            'Costo',
            'Cuota Inicial',
            'Número Cuotas',
            'Estado'
        ];
    }

    protected function formatExportRow($matricula): array
    {
        return [
            $matricula->id,
            $matricula->student->nombres . ' ' . $matricula->student->apellidos,
            $matricula->student->documento_identidad,
            $matricula->programa->nombre ?? 'N/A',
            $matricula->periodo->name ?? 'N/A',
            $matricula->fecha_matricula->format('d/m/Y'),
            $matricula->costo,
            $matricula->cuota_inicial,
            $matricula->numero_cuotas,
            ucfirst($matricula->estado)
        ];
    }

    public function render()
    {
        $matriculas = Matricula::with(['student', 'programa', 'periodo'])
            ->when($this->search, function ($query) {
                $query->whereHas('student', function ($subQuery) {
                    $subQuery->where('nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                        ->orWhere('documento_identidad', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status !== '', function ($query) {
                $query->where('matriculas.estado', $this->status);
            })
            ->orderBy('matriculas.' . $this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Estadísticas
        $totalMatriculas = Matricula::count();
        $matriculasActivas = Matricula::where('estado', 'activo')->count();
        $matriculasInactivas = Matricula::where('estado', 'inactivo')->count();
        $matriculasGraduadas = Matricula::where('estado', 'graduado')->count();
        $ingresosTotales = Matricula::sum('costo');

        return view('livewire.admin.matriculas.index', compact(
            'matriculas',
            'totalMatriculas',
            'matriculasActivas', 
            'matriculasInactivas',
            'matriculasGraduadas',
            'ingresosTotales'
        ))->layout('components.layouts.admin', [
            'title' => 'Lista de Matrículas',
            'description' => 'Gestión de matrículas de estudiantes'
        ]);
    }
}