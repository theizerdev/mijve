<?php

namespace App\Livewire\Admin\ConceptosPago;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ConceptoPago;

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

    public function delete(ConceptoPago $concepto)
    {
        // Verificar permiso para eliminar conceptos de pago
        if (!auth()->user()->can('delete conceptos_pago')) {
            session()->flash('error', 'No tienes permiso para eliminar conceptos de pago.');
            return;
        }

        try {
            $concepto->delete();
            session()->flash('message', 'Concepto de pago eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el concepto de pago: ' . $e->getMessage());
        }

        $this->resetPage();
    }

    public function toggleStatus(ConceptoPago $concepto)
    {
        // Verificar permiso para editar conceptos de pago
        if (!auth()->user()->can('edit conceptos_pago')) {
            session()->flash('error', 'No tienes permiso para editar conceptos de pago.');
            return;
        }

        try {
            $concepto->activo = !$concepto->activo;
            $concepto->save();
            session()->flash('message', 'Estado del concepto de pago actualizado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar el estado del concepto de pago: ' . $e->getMessage());
        }
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
        $conceptos = ConceptoPago::when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('activo', $this->status);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.conceptos-pago.index', compact('conceptos'))
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Conceptos de Pago',
                'description' => 'Gestión de conceptos de pago'
            ]);
    }
}