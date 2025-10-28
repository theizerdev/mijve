<?php

namespace App\Livewire\Admin\Matriculas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Matricula;

class Index extends Component
{
    use WithPagination;

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

        return view('livewire.admin.matriculas.index', compact('matriculas'))
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Matrículas',
                'description' => 'Gestión de matrículas de estudiantes'
            ]);
    }
}