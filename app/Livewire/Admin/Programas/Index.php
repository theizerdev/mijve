<?php

namespace App\Livewire\Admin\Programas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Programa;
use App\Models\NivelEducativo;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $nivel_educativo_id = '';
    public $status = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'nivel_educativo_id' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedNivelEducativoId()
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

    public function delete(Programa $programa)
    {
        // Verificar permiso para eliminar programas
        if (!auth()->user()->can('delete programas')) {
            session()->flash('error', 'No tienes permiso para eliminar programas.');
            return;
        }

        try {
            $programa->delete();
            session()->flash('message', 'Programa eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el programa: ' . $e->getMessage());
        }

        $this->resetPage();
    }

    public function toggleStatus(Programa $programa)
    {
        // Verificar permiso para editar programas
        if (!auth()->user()->can('edit programas')) {
            session()->flash('error', 'No tienes permiso para editar programas.');
            return;
        }

        try {
            $programa->activo = !$programa->activo;
            $programa->save();
            session()->flash('message', 'Estado del programa actualizado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el estado del programa: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->nivel_educativo_id = '';
        $this->status = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function render()
    {
        $programas = Programa::with('nivelEducativo')
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->when($this->nivel_educativo_id, function ($query) {
                $query->where('nivel_educativo_id', $this->nivel_educativo_id);
            })
            ->when($this->status !== '', function ($query) {
                $query->where('activo', $this->status);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $nivelesEducativos = NivelEducativo::where('status', true)->get();

        return view('livewire.admin.programas.index', compact('programas', 'nivelesEducativos'))
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Programas',
                'description' => 'Gestión de programas académicos'
            ]);
    }
}