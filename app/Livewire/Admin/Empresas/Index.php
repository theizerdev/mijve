<?php

namespace App\Livewire\Admin\Empresas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Empresa;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sortBy = 'razon_social';
    public $sortDirection = 'asc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'razon_social'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        if (!Auth::user()->can('access empresas')) {
            abort(403, 'No tienes permiso para acceder a empresas.');
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

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleStatus($empresaId)
    {
        if (!Auth::user()->can('edit empresas')) {
            session()->flash('error', 'No tienes permiso para editar empresas.');
            return;
        }

        $empresa = Empresa::findOrFail($empresaId);

        if ($empresa->status === 'activo') {
            $empresa->status = 'inactivo';
            $message = 'Empresa desactivada exitosamente.';
        } else {
            $empresa->status = 'activo';
            $message = 'Empresa activada exitosamente.';
        }

        $empresa->save();
        session()->flash('message', $message);
    }

    public function deleteEmpresa($empresaId)
    {
        if (!Auth::user()->can('delete empresas')) {
            session()->flash('error', 'No tienes permiso para eliminar empresas.');
            return;
        }

        $empresa = Empresa::findOrFail($empresaId);

        // Verificar si tiene sucursales asociadas
        if ($empresa->sucursales()->exists()) {
            session()->flash('error', 'No se puede eliminar la empresa porque tiene sucursales asociadas.');
            return;
        }

        try {
            $empresa->delete();
            session()->flash('message', 'Empresa eliminada exitosamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar la empresa: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'status', 'sortBy', 'sortDirection', 'perPage']);
        $this->sortBy = 'razon_social';
        $this->sortDirection = 'asc';
        $this->perPage = 10;
    }

    public function render()
    {
        $query = Empresa::with(['sucursales']);

        // Aplicar búsqueda
        if ($this->search) {
            $query->where(function($q) {
                $q->where('razon_social', 'like', '%' . $this->search . '%')
                  ->orWhere('documento', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Filtrar por status
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        // Ordenar
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener empresas paginadas
        $empresas = $query->paginate($this->perPage);

        // Calcular estadísticas
        $totalEmpresas = Empresa::count();
        $empresasActivas = Empresa::where('status', 'activo')->count();
        $empresasInactivas = Empresa::where('status', 'inactivo')->count();

        return view('livewire.admin.empresas.index', [
            'empresas' => $empresas,
            'totalEmpresas' => $totalEmpresas,
            'empresasActivas' => $empresasActivas,
            'empresasInactivas' => $empresasInactivas
        ])->layout('components.layouts.admin', ['title' => 'Empresas']);
    }
}
