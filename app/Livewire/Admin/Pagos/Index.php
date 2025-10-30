<?php

namespace App\Livewire\Admin\Pagos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pago;

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

    public function delete(Pago $pago)
    {
        // Verificar permiso para eliminar pagos
        if (!auth()->user()->can('delete pagos')) {
            session()->flash('error', 'No tienes permiso para eliminar pagos.');
            return;
        }

        try {
            $pago->delete();
            session()->flash('message', 'Pago eliminado correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar el pago: ' . $e->getMessage());
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
        $pagos = Pago::with(['matricula.student', 'concepto', 'user', 'comprobante'])
            ->when($this->search, function ($query) {
                $query->whereHas('matricula.student', function ($subQuery) {
                    $subQuery->where('nombres', 'like', '%' . $this->search . '%')
                        ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                        ->orWhere('documento_identidad', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('concepto', function($subQuery) {
                    $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                })
                ->orWhere('referencia', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('estado', $this->status);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.pagos.index', compact('pagos'))
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Pagos',
                'description' => 'Gestión de pagos de matrículas'
            ]);
    }
}