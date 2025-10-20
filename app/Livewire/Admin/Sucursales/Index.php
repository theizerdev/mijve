<?php

namespace App\Livewire\Admin\Sucursales;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sucursal;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $empresa_id = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        // Verificar permiso para ver sucursales
        if (!Auth::user()->can('view sucursales')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingEmpresaId()
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
    }

    public function render()
    {
        $sucursales = Sucursal::with('empresa')
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $empresas = Empresa::where('status', 'active')->get();

        // Calcular estadísticas
        $totalSucursales = Sucursal::count();
        $sucursalesActivas = Sucursal::where('status', 'active')->count();
        $sucursalesInactivas = Sucursal::where('status', 'inactive')->count();

        return view('livewire.admin.sucursales.index', compact('sucursales', 'empresas', 'totalSucursales', 'sucursalesActivas', 'sucursalesInactivas'))
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Sucursales'
            ]);
    }

    public function toggleStatus(Sucursal $sucursal)
    {
        // Verificar permiso para editar sucursales
        if (!Auth::user()->can('edit sucursales')) {
            session()->flash('error', 'No tienes permiso para editar sucursales.');
            return;
        }

        $sucursal->status = $sucursal->status === 'active' ? 'inactive' : 'active';
        $sucursal->save();

        session()->flash('message', 'Estado de sucursal actualizado correctamente.');
    }

    public function delete(Sucursal $sucursal)
    {
        // Verificar permiso para eliminar sucursales
        if (!Auth::user()->can('delete sucursales')) {
            session()->flash('error', 'No tienes permiso para eliminar sucursales.');
            return;
        }

        $sucursal->delete();
        session()->flash('message', 'Sucursal eliminada correctamente.');
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->empresa_id = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }
}
