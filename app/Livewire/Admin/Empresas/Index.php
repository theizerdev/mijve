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

    public function mount()
    {
        // Verificar permiso para ver empresas
        if (!Auth::user()->can('view empresas')) {
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
        $empresas = Empresa::query()
            ->when($this->search, function ($query) {
                $query->where('razon_social', 'like', '%' . $this->search . '%')
                    ->orWhere('documento', 'like', '%' . $this->search . '%');
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        // Calcular estadísticas
        $totalEmpresas = Empresa::count();
        $empresasActivas = Empresa::where('status', 'active')->count();
        $empresasInactivas = Empresa::where('status', 'inactive')->count();

        return view('livewire.admin.empresas.index', compact('empresas', 'totalEmpresas', 'empresasActivas', 'empresasInactivas'))
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Empresas'
            ]);
    }

    public function toggleStatus(Empresa $empresa)
    {
        // Verificar permiso para editar empresas
        if (!Auth::user()->can('edit empresas')) {
            session()->flash('error', 'No tienes permiso para editar empresas.');
            return;
        }

        $empresa->status = $empresa->status === 'active' ? 'inactive' : 'active';
        $empresa->save();

        session()->flash('message', 'Estado de empresa actualizado correctamente.');
    }

    public function delete(Empresa $empresa)
    {
        // Verificar permiso para eliminar empresas
        if (!Auth::user()->can('delete empresas')) {
            session()->flash('error', 'No tienes permiso para eliminar empresas.');
            return;
        }

        // Verificar si la empresa tiene sucursales
        if ($empresa->sucursales()->count() > 0) {
            session()->flash('error', 'No se puede eliminar la empresa porque tiene sucursales asociadas.');
            return;
        }

        $empresa->delete();
        session()->flash('message', 'Empresa eliminada correctamente.');
        $this->resetPage();
    }
}
